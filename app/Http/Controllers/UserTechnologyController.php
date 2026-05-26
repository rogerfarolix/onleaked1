<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserTechnologyController extends Controller
{
    public function update(Request $request)
    {
        $validated = $request->validate([
            'technologies' => 'array',
            'technologies.*' => 'exists:technologies,id',
        ]);

        $request->user()->technologies()->sync($validated['technologies'] ?? []);

        return back()->with('status', 'technologies-updated');
    }
}
