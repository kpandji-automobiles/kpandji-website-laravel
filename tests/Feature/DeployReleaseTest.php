<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;
use ZipArchive;

class DeployReleaseTest extends TestCase
{
    private const TOKEN = 'test-deploy-token-0123456789abcdef';

    private const RELEASE = '20261103120000-abc1234';

    private string $root;

    protected function setUp(): void
    {
        parent::setUp();

        $this->root = sys_get_temp_dir().'/kp-deploy-'.uniqid();
        File::ensureDirectoryExists($this->root.'/releases');
        config(['services.deploy.token' => self::TOKEN, 'services.deploy.releases_path' => $this->root.'/releases']);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->root);

        parent::tearDown();
    }

    /** @param  array<string, string>  $entries */
    private function archive(array $entries): string
    {
        $dir = $this->root.'/releases/'.self::RELEASE;
        File::ensureDirectoryExists($dir);

        $zip = new ZipArchive;
        $zip->open($dir.'/release.zip', ZipArchive::CREATE);
        foreach ($entries as $name => $content) {
            str_ends_with($name, '/') ? $zip->addEmptyDir(rtrim($name, '/')) : $zip->addFromString($name, $content);
        }
        $zip->close();

        return $dir;
    }

    public function test_unpack_is_hidden_without_the_right_token(): void
    {
        $dir = $this->archive(['artisan' => '<?php']);

        $this->withToken('wrong-token')->postJson(route('deploy.unpack'), ['release' => self::RELEASE])->assertNotFound();
        config(['services.deploy.token' => '']);
        $this->withToken('')->postJson(route('deploy.unpack'), ['release' => self::RELEASE])->assertNotFound();

        $this->assertFileExists($dir.'/release.zip');
        $this->assertFileDoesNotExist($dir.'/artisan');
    }

    public function test_unpack_rejects_a_release_name_outside_releases(): void
    {
        $this->withToken(self::TOKEN)->postJson(route('deploy.unpack'), ['release' => '../shared'])->assertStatus(422);
        $this->withToken(self::TOKEN)->postJson(route('deploy.unpack'), ['release' => self::RELEASE])
            ->assertStatus(422)
            ->assertJson(['status' => 'missing']);
    }

    public function test_unpack_extracts_the_release_and_removes_the_archive(): void
    {
        $dir = $this->archive([
            'public/.htaccess' => 'RewriteEngine On',
            'vendor/autoload.php' => '<?php // autoload',
            'bootstrap/cache/' => '',
            'RELEASE' => self::RELEASE,
        ]);

        $this->withToken(self::TOKEN)->postJson(route('deploy.unpack'), ['release' => self::RELEASE])
            ->assertOk()
            ->assertJson(['status' => 'ok', 'release' => self::RELEASE, 'entries' => 4]);

        $this->assertStringEqualsFile($dir.'/public/.htaccess', 'RewriteEngine On');
        $this->assertStringEqualsFile($dir.'/vendor/autoload.php', '<?php // autoload');
        $this->assertDirectoryExists($dir.'/bootstrap/cache');
        $this->assertFileDoesNotExist($dir.'/release.zip');
    }

    public function test_unpack_refuses_an_archive_that_writes_outside_its_release(): void
    {
        $dir = $this->archive(['public/index.php' => '<?php', '../../evil.php' => '<?php']);

        $this->withToken(self::TOKEN)->postJson(route('deploy.unpack'), ['release' => self::RELEASE])
            ->assertStatus(422)
            ->assertJson(['status' => 'unsafe', 'entry' => '../../evil.php']);

        $this->assertFileDoesNotExist($this->root.'/evil.php');
        $this->assertFileDoesNotExist($dir.'/public/index.php');
    }

    public function test_prune_keeps_the_newest_and_protected_releases_and_leaves_shared_files(): void
    {
        File::ensureDirectoryExists($this->root.'/shared/storage');
        file_put_contents($this->root.'/shared/storage/keep.txt', 'uploads');

        $names = ['20261101000000-aaaaaaa', '20261102000000-bbbbbbb', '20261103000000-ccccccc', '20261104000000-ddddddd', '20261105000000-eeeeeee'];
        foreach ($names as $name) {
            File::ensureDirectoryExists($this->root.'/releases/'.$name.'/vendor/laravel');
            file_put_contents($this->root.'/releases/'.$name.'/vendor/laravel/file.php', '<?php');
            symlink($this->root.'/shared/storage', $this->root.'/releases/'.$name.'/storage');
        }
        File::ensureDirectoryExists($this->root.'/releases/not-a-release');

        $this->withToken(self::TOKEN)
            ->postJson(route('deploy.prune'), ['keep' => 2, 'protect' => ['20261102000000-bbbbbbb']])
            ->assertOk()
            ->assertExactJson(['status' => 'ok', 'removed' => ['20261101000000-aaaaaaa', '20261103000000-ccccccc']]);

        $this->assertEqualsCanonicalizing(
            ['20261102000000-bbbbbbb', '20261104000000-ddddddd', '20261105000000-eeeeeee', 'not-a-release'],
            array_map('basename', File::directories($this->root.'/releases'))
        );
        $this->assertStringEqualsFile($this->root.'/shared/storage/keep.txt', 'uploads');
    }

    public function test_prune_never_goes_below_two_releases(): void
    {
        foreach (['20261101000000-aaaaaaa', '20261102000000-bbbbbbb', '20261103000000-ccccccc'] as $name) {
            File::ensureDirectoryExists($this->root.'/releases/'.$name);
        }

        $this->withToken(self::TOKEN)->postJson(route('deploy.prune'), ['keep' => 0])
            ->assertOk()
            ->assertExactJson(['status' => 'ok', 'removed' => ['20261101000000-aaaaaaa']]);
    }
}
