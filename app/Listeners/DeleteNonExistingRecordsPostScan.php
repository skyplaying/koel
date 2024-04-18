<?php

namespace App\Listeners;

use App\Events\MediaScanCompleted;
use App\Models\Song;
use App\Repositories\SongRepository;
use App\Values\ScanResult;

class DeleteNonExistingRecordsPostScan
{
    public function __construct(private readonly SongRepository $songRepository)
    {
    }

    public function handle(MediaScanCompleted $event): void
    {
        $paths = $event->results
            ->valid()
            ->map(static fn (ScanResult $result) => $result->path)
            ->merge($this->songRepository->getAllStoredOnCloud()->pluck('path'))
            ->toArray();

        Song::deleteWhereValueNotIn($paths, 'path');
    }
}
