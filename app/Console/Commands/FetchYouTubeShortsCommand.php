<?php

namespace App\Console\Commands;

use App\Services\YouTubeService;
use Illuminate\Console\Command;

class FetchYouTubeShortsCommand extends Command
{
    protected $signature = 'youtube:shorts {--refresh : Bypass cache and fetch from API}';
    protected $description = 'Fetch latest YouTube Shorts from @StarJDs channel (for debugging Brand page)';

    public function handle(): int
    {
        $apiKey = config('services.youtube.api_key') ?: env('YOUTUBE_API_KEY');
        $handle = config('services.youtube.channel_handle') ?: env('YOUTUBE_CHANNEL_HANDLE', 'StarJDs');

        if (empty($apiKey)) {
            $this->error('YOUTUBE_API_KEY is not set in .env');
            $this->line('Add it and run: php artisan config:clear && php artisan youtube:shorts --refresh');
            return 1;
        }

        $this->info("Fetching latest Shorts for channel: {$handle}");
        $youtube = app(YouTubeService::class);
        $shorts = $youtube->getLatestShorts(3, $this->option('refresh'));

        if (empty($shorts)) {
            $this->warn('No Shorts returned. Possible causes:');
            $this->line('  - Channel not found (check YOUTUBE_CHANNEL_HANDLE)');
            $this->line('  - No videos ≤61s in last 100 uploads');
            $this->line('  - API quota or key invalid. Check storage/logs/laravel.log');
            $this->line('Try: php artisan youtube:shorts --refresh');
            return 1;
        }

        $this->info('Found ' . count($shorts) . ' Short(s):');
        foreach ($shorts as $i => $s) {
            $this->line(sprintf('  %d. %s', $i + 1, $s['title'] ?? 'Short'));
            $this->line('     https://www.youtube.com/shorts/' . $s['videoId']);
        }
        $this->newLine();
        $this->info('Brand page will show these when /api/shorts is called (after config/cache clear if needed).');
        return 0;
    }
}
