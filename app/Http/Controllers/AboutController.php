<?php

namespace App\Http\Controllers;

use App\Models\AboutPage;
use App\Models\TeamMember;
use Illuminate\View\View;

class AboutController extends Controller
{
    /**
     * عرض صفحة من نحن في الموقع العام.
     */
    public function index(): View
    {
        $aboutPage = AboutPage::query()
            ->where('is_active', true)
            ->latest('id')
            ->first();

        $teamMembers = TeamMember::query()
            ->where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('id')
            ->get();

        return view('about', compact(
            'aboutPage',
            'teamMembers'
        ));
    }
}