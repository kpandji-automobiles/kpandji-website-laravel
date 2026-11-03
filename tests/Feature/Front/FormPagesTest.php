<?php

namespace Tests\Feature\Front;

use App\Models\Car;
use App\Models\Modele;
use App\Models\TestDriveRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class FormPagesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Chaque page envoie vers son point d'entrée avec les champs que le contrôleur lit.
     *
     * @return array<string, array{string, string, list<string>}>
     */
    public static function formPages(): array
    {
        return [
            'essai' => ['/essai', 'contact.store', ['type', 'car_id', 'name', 'phone', 'email', 'preferred_date', 'message']],
            'contact' => ['/contact', 'contact.send', ['name', 'email', 'phone', 'subject', 'message']],
            'sav' => ['/sav', 'sav.store', ['name', 'contact', 'model', 'issue', 'location']],
            'sira' => ['/sira', 'sira.store', ['name', 'country', 'city', 'phone', 'email']],
            'privilege' => ['/privilege', 'privilege.store', ['name', 'country', 'city', 'phone', 'email', 'source']],
            'emplois' => ['/emplois', 'emplois.store', ['full_name', 'email', 'phone', 'position', 'cv_summary', 'cv', 'education', 'skills', 'languages', 'extras', 'website']],
        ];
    }

    #[DataProvider('formPages')]
    public function test_form_page_posts_the_expected_fields_to_its_endpoint(string $url, string $route, array $fields): void
    {
        $response = $this->get($url)
            ->assertOk()
            ->assertSee(asset('css/kpandji/site.css'), false)
            ->assertSee('action="'.route($route).'"', false);

        foreach ($fields as $field) {
            $response->assertSee('name="'.$field.'"', false);
        }
    }

    public function test_privilege_offers_the_advisor_and_contact_requests(): void
    {
        $this->get('/privilege')
            ->assertSee('name="source" value="advisor"', false)
            ->assertSee('name="source" value="contact"', false);
    }

    public function test_application_form_sends_files(): void
    {
        $this->get('/emplois')->assertSee('enctype="multipart/form-data"', false);
    }

    public function test_sav_offers_the_range_models_and_an_other_choice(): void
    {
        Modele::create(['key' => 'lathaye', 'name' => 'LATHAYE', 'car_slug' => 'lathaye', 'is_active' => true, 'sort' => 1, 'sr_sort' => 1]);

        $this->get('/sav')
            ->assertSee('<option value="LATHAYE"', false)
            ->assertSee('<option value="'.e(content('savp.f_other')).'"', false);
    }

    public function test_test_drive_preselects_the_model_and_confirms_the_request(): void
    {
        $car = Car::create(['name' => 'LATHAYE', 'slug' => 'lathaye', 'category' => 'SUV', 'is_active' => true, 'sort_order' => 1]);

        $this->get('/essai?modele=lathaye')
            ->assertSee('<option value="'.$car->id.'" selected', false)
            ->assertSee('data-kp-vcard="lathaye"', false);

        $this->post(route('contact.store'), ['type' => 'essai', 'car_id' => $car->id, 'name' => 'Awa Koné', 'phone' => '0102030405'])
            ->assertRedirect(route('contact'));

        $this->assertSame(1, TestDriveRequest::where('car_id', $car->id)->count());
        $this->get(route('contact'))->assertSee(content('form.success'));
    }

    public function test_test_drive_errors_are_shown_next_to_the_fields(): void
    {
        $this->from('/essai')->post(route('contact.store'), ['type' => 'essai', 'name' => '', 'phone' => ''])->assertRedirect('/essai');

        $this->get('/essai')->assertSee('kp-field--error', false)->assertSee('role="alert"', false);
    }
}
