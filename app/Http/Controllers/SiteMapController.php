<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Job;

class SiteMapController extends Controller
{
    public function index()
    {
        //add home route to the sitemap
        $sitemap = Sitemap::create()
            ->add(Url::create(route('home')));

        // Get jobs with type and category
        $jobs = Job::with(['type', 'category'])->orderByDesc('id')->lazy();

        foreach ($jobs as $item) {
            $sitemap->add(
                Url::create(url("/job/{$item->slug}/{$item->id}"))
                    ->setLastModificationDate($item->updated_at)
            );
        }

        // Save the sitemap to the public directory
        $sitemap->writeToFile(base_path('sitemap.xml'));

        return 'Sitemap Created Successfully';
    }
}
