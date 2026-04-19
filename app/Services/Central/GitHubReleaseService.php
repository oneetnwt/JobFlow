<?php

namespace App\Services\Central;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class GitHubReleaseService
 *
 * Syncs application releases from a configured GitHub repository.
 */
class GitHubReleaseService
{
    /**
     * Retrieve the latest releases from the configured GitHub repository.
     */
    public function getReleases(): array
    {
        $repo = config('updates.github.repo');
        $token = config('updates.github.token');
        $cacheTtl = config('updates.cache_ttl', 60);

        if (empty($repo)) {
            Log::warning('GitHub repository not configured for updates synchronization.');

            return [];
        }

        return collect(Cache::remember("github_releases_{$repo}", now()->addMinutes($cacheTtl), function () use ($repo, $token) {
            $request = Http::withHeaders([
                'Accept' => 'application/vnd.github.v3+json',
                'User-Agent' => 'JobFlow-Update-Client',
            ]);

            if (app()->environment('local')) {
                $request->withoutVerifying();
            }

            if (! empty($token)) {
                $request->withToken($token);
            }

            $response = $request->get("https://api.github.com/repos/{$repo}/releases");

            if ($response->failed()) {
                Log::error("Failed to fetch GitHub releases for {$repo}: ".$response->body());

                return [];
            }

            return $response->json();
        }))->filter(function ($release) {
            // Respect pre-release configuration
            $includePrereleases = config('updates.include_prereleases', false);
            if (! $includePrereleases && ! empty($release['prerelease'])) {
                return false;
            }

            return true;
        })->values()->toArray();
    }
}
