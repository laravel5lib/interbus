<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use tristanpollard\ESIClient\Services\ESIClient;

abstract class ESIJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $endpoint = "";

    public $version = "";

    protected $uid;

    protected $optionalColumns = [];

    protected $dateFields = [];

    public $tries = 1;

    public function __construct()
    {
        $this->uid = uniqid('job_');
    }

    public abstract function getId(): Int;

    protected abstract function logStart();

    protected abstract function logFinished();

    public abstract function shouldDispatch();

    protected function etagProcessed(string $etag) {
        return Cache::has($this->getEtagKey($etag));
    }

    protected function setEtag(string $etag) {
        Cache::forever($this->getEtagKey($etag), true);
    }

    protected function getEtagKey(string $etag) {
        return $this->getId() . ':' . get_class($this) . ':' . $etag;
    }

    protected function getClient(): ESIClient{
        $client = app()->make(ESIClient::class);
        return $client;
    }

    protected function mapRequiredColumn(array $item): array{
        foreach ($this->optionalColumns as $column) {
            if (!isset($item[$column])) {
                $item[$column] = null;
            }
        }
        return $item;
    }

    protected function mapRequiredColumns(Collection $collection): Collection{
        return $collection->map(function ($item){
            return $this->mapRequiredColumn($item);
        });
    }

    protected function mapDateColumn($item){
        foreach ($this->dateFields as $dateField) {
            if (isset($item[$dateField])) {
                $item[$dateField] = Carbon::parse($item[$dateField]);
            }
        }
        return $item;
    }

    protected function mapDateColumns(Collection $collection): Collection{
        return $collection->map(function ($item) {
            return $this->mapDateColumn($item);
        });
    }

    protected function addDateFields(Collection $collection): Collection{
        $now = Carbon::now();
        return $collection->map(function ($item) use ($now) {
            $item['updated_at'] = $now;
            $item['created_at'] = $now;
            return $item;
        });
    }
}