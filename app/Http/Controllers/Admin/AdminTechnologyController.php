<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Technology;
use Illuminate\Http\Request;

class AdminTechnologyController extends Controller
{
    public function index()
    {
        $technologies = Technology::withCount(['users', 'vulnerabilities'])->paginate(25);
        return \Inertia\Inertia::render('Admin/Technologies', compact('technologies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:technologies,name'],
        ]);

        Technology::create(['name' => trim($request->input('name'))]);

        return back()->with('success', 'Technology added successfully.');
    }

    public function destroy(Technology $technology)
    {
        $technology->delete();
        return back()->with('success', 'Technology deleted.');
    }
}
