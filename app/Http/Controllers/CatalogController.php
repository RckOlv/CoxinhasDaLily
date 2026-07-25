<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Inertia\Inertia;

class CatalogController extends Controller
{
    public function index()
    {
        $categories = Category::with([
            'products' => fn ($q) => $q->where('is_active', true),
        ])
            ->whereHas('products', fn ($q) => $q->where('is_active', true))
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return Inertia::render('Catalog/Index', [
            'categories' => $categories,
        ]);
    }
}
