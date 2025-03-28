<?php

namespace Cdz\PagiScrap\Jobs;

use Cdz\PagiScrap\PaginateApiRequestInterface;
use Cdz\PagiScrap\PaginateApiScraperInterface;
use Illuminate\Foundation\Bus\Dispatchable;
use Throwable;

class AdaptiveMultipageScraper extends BaseMultipageScraper
{
    use Dispatchable;

    /**
     * Create a new job instance.
     * @param PaginateApiRequestInterface $apiRequest
     * @param PaginateApiScraperInterface $apiScraper
     */
    public function __construct(PaginateApiRequestInterface $apiRequest, PaginateApiScraperInterface $apiScraper)
    {
        parent::__construct($apiRequest, $apiScraper);
    }

    /**
     * Execute the job.
     * @throws Throwable
     */
    public function handle(): void
    {
        $response = $this->apiRequest->process(1);
        if ($response) {
            $this->apiRequest->after($response);

            $num_pages = $this->apiRequest->pages();
            if ($num_pages > 1) {
                $this->bus($num_pages, 2);
            }
        }
    }
}
