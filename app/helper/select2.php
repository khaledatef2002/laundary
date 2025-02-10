<?php

namespace App\helper;

use App\Models\ArticleCategory;
use App\Models\Author;
use App\Models\BooksCategory;
use App\Models\Client;
use App\Models\Service;
use App\PeopleType;
use Illuminate\Http\Request;

class select2
{
    /**
     * Create a new class instance.
     */
    public function clients(Request $request)
    {
        $search = $request->get('q'); // For searching functionality

        $clients = Client::when($search, function($query) use ($search) {
                return $query->where('name', 'LIKE', "%{$search}%");
            })
            ->get();

        return response()->json($clients);
    }

    public function services(Request $request)
    {
        $search = $request->get('q'); // For searching functionality

        $services = Service::whereTranslationLike('title', "%{$search}%")
        ->get();

        return response()->json($services);
    }
}
