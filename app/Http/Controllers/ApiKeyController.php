<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use Illuminate\Http\Request;

class ApiKeyController extends Controller
{
    public function index()
    {
        $keys = auth()->user()->apiKeys()->orderBy('created_at', 'desc')->get(['id', 'name', 'last_used_at', 'created_at']);
        return response()->json($keys);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        if (auth()->user()->apiKeys()->count() >= 10) {
            return response()->json(['message' => 'Maximum 10 API keys allowed.'], 422);
        }

        $rawKey = ApiKey::generate();

        $key = ApiKey::create([
            'user_id' => auth()->id(),
            'name'    => $request->input('name'),
            'key'     => $rawKey,
        ]);

        return response()->json([
            'id'   => $key->id,
            'name' => $key->name,
            'key'  => $rawKey,
        ], 201);
    }

    public function destroy(int $id)
    {
        $key = ApiKey::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $key->delete();
        return response()->noContent();
    }
}
