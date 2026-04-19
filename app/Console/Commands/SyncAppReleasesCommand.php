<?php

namespace App\Console\Commands;

use App\Models\AppVersion;
use App\Models\Tenant;
use App\Notifications\Central\NewUpdateAvailableNotification;
use App\Services\Central\GitHubReleaseService;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

#[Signature('app:sync-releases')]
#[Description('Synchronize available updates originating from the configured GitHub repository.')]
class SyncAppReleasesCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(GitHubReleaseService $githubService)
    {
        $this->info('Starting GitHub releases synchronization...');

        $repo = config('updates.github.repo');
        if (empty($repo) || $repo === 'owner/repo') {
            $this->error('Update repository not configured! Please set GITHUB_REPO.');

            return Command::FAILURE;
        }

        $releases = $githubService->getReleases();

        if (empty($releases)) {
            $this->warn('No releases fetched or the JSON response is empty.');

            return Command::SUCCESS;
        }

        $addedCounter = 0;
        $skippedCounter = 0;
        $isFirstSync = !AppVersion::exists();
        $newVersions = [];

        // Reverse to process oldest first
        foreach (array_reverse($releases) as $release) {
            $tagName = $release['tag_name'] ?? null;
            if (!$tagName) {
                continue;
            }

            $existing = AppVersion::where('version', $tagName)->first();

            if ($existing) {
                $skippedCounter++;

                continue;
            }

            $appVersion = AppVersion::create([
                'version' => $tagName,
                'title' => $release['name'] ?? $tagName,
                'release_notes' => $release['body'] ?? '',
                'is_prerelease' => $release['prerelease'] ?? false,
                'github_release_id' => (string) ($release['id'] ?? ''),
                'released_at' => isset($release['published_at']) ? Carbon::parse($release['published_at']) : now(),
                'is_critical' => Str::contains(strtolower($release['body'] ?? ''), '[critical]'),
            ]);

            $addedCounter++;
            $newVersions[] = $appVersion;
            $this->line("Synced new version: {$appVersion->version}");
        }

        $this->info("Sync completed: {$addedCounter} added, {$skippedCounter} skipped.");

        if (!$isFirstSync && count($newVersions) > 0) {
            $this->notifyTenantsAboutNewReleases(collect($newVersions)->last());
        }

        return Command::SUCCESS;
    }

    protected function notifyTenantsAboutNewReleases(AppVersion $latestVersion): void
    {
        $this->info('Notifying active tenants about newly available releases...');

        $tenants = Tenant::whereNotNull('admin_email')->get();

        foreach ($tenants as $tenant) {
            if (!empty($tenant->admin_email)) {
                Notification::route('mail', $tenant->admin_email)
                    ->notify(new NewUpdateAvailableNotification($tenant, $latestVersion));
            }
        }
    }
}
