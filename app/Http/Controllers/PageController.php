<?php
namespace App\Http\Controllers;

use App\Models\Page;

class PageController extends Controller
{
    public function show(string $slug)
    {
        $page = Page::where('slug', $slug)->first();
        $admin = auth()->user()?->is_admin;
        abort_if(! $page || (! $page->published && ! $admin), 404);
        return view('pages.placeholder', compact('page'));
    }
}
