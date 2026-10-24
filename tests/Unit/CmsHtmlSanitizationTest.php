<?php

namespace Tests\Unit;

use App\Support\Cms;
use PHPUnit\Framework\TestCase;

class CmsHtmlSanitizationTest extends TestCase
{
    public function test_it_removes_executable_alpine_attributes(): void
    {
        $html = '<p x-data x-init="window.__kpAudit=1">Bonjour</p>';

        $cleaned = Cms::cleanHtml($html);

        $this->assertStringNotContainsString('x-data', $cleaned);
        $this->assertStringNotContainsString('x-init', $cleaned);
        $this->assertStringContainsString('Bonjour', $cleaned);
    }

    public function test_it_preserves_basic_formatting(): void
    {
        $html = '<p>Bonjour <strong>Émile</strong></p>';

        $cleaned = Cms::cleanHtml($html);

        $this->assertSame($html, $cleaned);
    }


    public function test_it_removes_html_event_handlers(): void
{
    $html = '<p onclick="alert(1)">Bonjour</p>';

    $cleaned = Cms::cleanHtml($html);

    $this->assertSame('<p>Bonjour</p>', $cleaned);
}

public function test_it_removes_javascript_links(): void
{
    $html = '<a href="javascript:alert(1)">Ouvrir</a>';

    $cleaned = Cms::cleanHtml($html);

    $this->assertSame('<a>Ouvrir</a>', $cleaned);
}

public function test_it_preserves_relative_links(): void
{
    $html = '<a href="/showroom" title="Nos modèles">Découvrir</a>';

    $cleaned = Cms::cleanHtml($html);

    $this->assertSame($html, $cleaned);
}

public function test_it_accepts_null_content(): void
{
    $this->assertSame('', Cms::cleanHtml(null));
}

}