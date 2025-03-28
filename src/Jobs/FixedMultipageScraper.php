<?php

namespace Cdz\PagiScrap\Jobs;

use Cdz\PagiScrap\PaginateApiRequestInterface;
use Cdz\PagiScrap\PaginateApiScraperInterface;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Bus\Batch;
use Illuminate\Support\Testing\Fakes\PendingBatchFake;
use Throwable;

class FixedMultipageScraper extends BaseMultipageScraper
{
    use Dispatchable;

    /**
     * @var int
     */
    protected int $pages = 1;

    /**
     * Create a new job instance.
     * @param PaginateApiRequestInterface $apiRequest
     * @param PaginateApiScraperInterface $apiScraper
     * @param int $pages
     */
    public function __construct(PaginateApiRequestInterface $apiRequest, PaginateApiScraperInterface $apiScraper, int $pages = 1)
    {
        parent::__construct($apiRequest, $apiScraper);

        $this->pages = $pages;
    }

    /**
     * Execute the job.
     * @throws Throwable
     */
    public function handle(): void
    {
        $this->bus($this->pages);
    }
}
