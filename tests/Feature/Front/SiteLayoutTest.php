<?php

namespace Tests\Feature\Front;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class SiteLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_layout_loads_the_design_system_and_renders_the_page_content(): void
    {
        $html = Blade::render("@extends('layouts.site')\n@section('title', 'Nos modèles')\n@section('content')<p>Contenu de la page</p>@endsection");

        $this->assertStringContainsString(asset('css/kpandji/tokens.css'), $html);
        $this->assertStringContainsString(asset('css/kpandji/components.css'), $html);
        $this->assertStringContainsString('<title>Nos modèles — KPANDJI', $html);
        $this->assertStringContainsString('<main id="contenu">', $html);
        $this->assertStringContainsString('<p>Contenu de la page</p>', $html);
    }

    public function test_design_system_stylesheets_are_published(): void
    {
        $this->assertFileExists(public_path('css/kpandji/tokens.css'));
        $this->assertStringContainsString('--gold: #c9a962', file_get_contents(public_path('css/kpandji/tokens.css')));
        $this->assertStringContainsString('.kp-btn--gold', file_get_contents(public_path('css/kpandji/components.css')));
    }
}
