<?php

namespace Cdz\PagiScrap\Jobs;

use Cdz\PagiScrap\PaginateApiRequestInterface;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class OnePageScraper implements ShouldQueue
{
    use Queueable;
    use Batchable;
    use Dispatchable;

    /**
     * @var int
     */
    protected int $page = 1;

    /**
     * @var bool
     */
    protected bool $next = false;

    /**
     * @var PaginateApiRequestInterface|null
     */
    protected ?PaginateApiRequestInterface $apiRequest = null;

    /**
     * Create a new job instance.
     */
    public function __construct(PaginateApiRequestInterface $apiRequest, int $page = 1)
    {
        $this->apiRequest = $apiRequest;
        $this->page = $page;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $response = $this->apiRequest->process($this->page);
        if ($response) {
            $this->apiRequest->after($response);
        }
    }
}
