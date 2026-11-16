<?php

namespace App\Http\Controllers;

use App\Models\MarketingPage;
use Illuminate\View\View;

class MarketingPagesController extends Controller
{
    public function show(string $slug): View
    {
        $page = MarketingPage::query()->where('slug', $slug)->first();

        return view("pages.{$slug}", [
            'marketing_page' => $page,
        ]);
    }
}
