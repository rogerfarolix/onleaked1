<?php

namespace App\Services\Sources;

use Illuminate\Support\Facades\Http;

class GravatarService
{
    public function lookup(string $email): array
    {
        $hash    = md5(strtolower(trim($email)));
        $url     = "https://www.gravatar.com/avatar/{$hash}?d=404&s=200";
        $profile = "https://www.gravatar.com/{$hash}.json";

        // Check if avatar exists (404 = no gravatar)
        $avatarResponse = Http::timeout(8)->get($url);
        $hasGravatar    = $avatarResponse->successful();

        $profileData = [];
        if ($hasGravatar) {
            try {
                $profileResponse = Http::timeout(8)->get($profile);
                if ($profileResponse->successful()) {
                    $entry = $profileResponse->json('entry.0', []);
                    $profileData = [
                        'display_name' => $entry['displayName'] ?? null,
                        'username'     => $entry['preferredUsername'] ?? null,
                        'about_me'     => $entry['aboutMe'] ?? null,
                        'location'     => $entry['currentLocation'] ?? null,
                        'urls'         => $entry['urls'] ?? [],
                        'accounts'     => $entry['accounts'] ?? [],
                    ];
                }
            } catch (\Throwable) {}
        }

        return [
            'has_gravatar' => $hasGravatar,
            'avatar_url'   => $hasGravatar ? "https://www.gravatar.com/avatar/{$hash}?s=200" : null,
            'hash'         => $hash,
            'profile'      => $profileData,
        ];
    }
}
