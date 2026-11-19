<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Support\FaqContent;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $slug): View
    {
        $page = Page::query()->where('slug', $slug)->where('is_active', true)->firstOrFail();

        if ($page->template && view()->exists('page.'.$page->template)) {
            $data = compact('page');

            if (in_array($page->template, ['faq', 'faqs'], true)) {
                $faq = FaqContent::parse($page->content);

                if ($faq['groups'] === []) {
                    return view('page.show', $data);
                }

                $data['faq'] = $faq;
            }

            return view('page.'.$page->template, $data);
        }

        $view = view()->exists("page.{$slug}") ? "page.{$slug}" : 'page.show';

        return view($view, compact('page'));
    }
}
