<?php

namespace Cdz\PagiScrap\Jobs;

use Cdz\PagiScrap\PaginateApiRequestInterface;
use Cdz\PagiScrap\PaginateApiScraperInterface;
use Illuminate\Bus\Batch;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Testing\Fakes\PendingBatchFake;
use Throwable;

class BaseMultipageScraper
{
    use Dispatchable;

    /**
     * @var PaginateApiRequestInterface|null
     */
    protected ?PaginateApiRequestInterface $apiRequest = null;

    /**
     * @var PaginateApiScraperInterface|null
     */
    protected ?PaginateApiScraperInterface $apiScraper = null;

    /**
     * Create a new job instance.
     * @param PaginateApiRequestInterface $apiRequest
     * @param PaginateApiScraperInterface $apiScraper
     */
    public function __construct(PaginateApiRequestInterface $apiRequest, PaginateApiScraperInterface $apiScraper)
    {
        $this->apiRequest = $apiRequest;
        $this->apiScraper = $apiScraper;
    }

    /**
     * Create a bus batch
     * @param int $num_pages
     * @param int $start
     * @return void
     * @throws Throwable
     */
    protected function bus(int $num_pages, int $start = 1): void
    {
        $jobs = [];
        for ($i = $start; $i <= $num_pages; $i++) {
            $jobs[] = new OnePageScraper($this->apiRequest, $i);
        }

        Bus::batch($jobs)->then(function (Batch|PendingBatchFake $batch) {
            $this->apiScraper->onSuccess($batch);
        })->catch(function (Batch|PendingBatchFake $batch, Throwable $exception) {
            $this->apiScraper->onError($batch, $exception);
        })->finally(function (Batch|PendingBatchFake $batch) {
            $this->apiScraper->onFinally($batch);
        })->name($this->apiScraper->getName())->dispatch();
    }
}
