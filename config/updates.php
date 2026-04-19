<?php

return [

    /*
    |--------------------------------------------------------------------------
    | GitHub Repository Settings
    |--------------------------------------------------------------------------
    |
    | The repository to fetch releases from, in the format "owner/repo".
    | A GitHub personal access token can optionally be provided to increase
    | rate limits or access private repositories.
    |
    */

    'github' => [
        'repo' => env('GITHUB_REPO', 'owner/repo'),
        'token' => env('GITHUB_TOKEN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | API Cache Config
    |--------------------------------------------------------------------------
    |
    | Time-to-Live for caching GitHub API responses (in minutes). A default
    | of 60 minutes prevents excessive API hits while keeping updates fresh.
    |
    */

    'cache_ttl' => env('UPDATE_CACHE_TTL_MINUTES', 60),

    /*
    |--------------------------------------------------------------------------
    | Pre-Release Inclusion
    |--------------------------------------------------------------------------
    |
    | Whether the app should flag pre-releases as available updates for tenants.
    | Set to false for environments that require strict stability.
    |
    */

    'include_prereleases' => env('UPDATE_INCLUDE_PRERELEASES', false),

    /*
    |--------------------------------------------------------------------------
    | Update Job Queue
    |--------------------------------------------------------------------------
    |
    | Defines the queue specifically used to dispatch tenant updates.
    |
    */

    'queue' => env('UPDATE_QUEUE', 'default'),

];
