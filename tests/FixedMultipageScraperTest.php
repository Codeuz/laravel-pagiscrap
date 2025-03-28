<?php

namespace Cdz\PagiScrap\Tests;

use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Session;
use Cdz\PagiScrap\PaginateApiScraper;
use Cdz\PagiScrap\Jobs\FixedMultipageScraper;

class FixedMultipageScraperTest extends TestCase
{
    use ApiRequestTrait;

    public function test_fixed_page_scraper_jobs_are_dispatched(): void
    {
        $apiRequest = $this->mock_api_request();

        $apiScraper = new PaginateApiScraper();

        Bus::fake();

        $job = new FixedMultipageScraper($apiRequest, $apiScraper, 2);
        $job->handle();

        Bus::assertBatched( function (PendingBatch $batch) {
            return $batch->jobs->count() === 2;
        });
    }

    public function test_fixed_page_scraper_batch_name_is_custom(): void
    {
        $apiRequest = $this->mock_api_request();

        $apiScraper = new PaginateApiScraper();
        $apiScraper->name('My Batch');

        Bus::fake();

        $job = new FixedMultipageScraper($apiRequest, $apiScraper, 2);
        $job->handle();

        Bus::assertBatched( function (PendingBatch $batch) {
            return $batch->name === 'My Batch';
        });
    }

    public function test_fixed_page_scraper_batch_callback_success_is_custom(): void
    {
        Session::flush();

        $apiRequest = $this->mock_api_request();

        $apiScraper = new PaginateApiScraper();
        $apiScraper->success(function ($bath){
            Session::put('success', true);
        });

        Bus::fake();

        $job = new FixedMultipageScraper($apiRequest, $apiScraper, 4);
        $job->handle();

        Bus::assertBatched( function (PendingBatch $batch) use ($job){
            [$thenCallback] = $batch->thenCallbacks();
            $thenCallback->getClosure()->call($job, $batch);

            $this->assertTrue(Session::get('success'));

            return true;
        });
    }

    public function test_fixed_page_scraper_batch_callback_error_is_custom(): void
    {
        Session::flush();

        $apiRequest = $this->mock_api_request();

        $apiScraper = new PaginateApiScraper();
        $apiScraper->error(function ($bath, $exception) {
            Session::put('error', true);
        });

        Bus::fake();

        $job = new FixedMultipageScraper($apiRequest, $apiScraper, 4);
        $job->handle();

        Bus::assertBatched( function (PendingBatch $batch) use ($job){
            [$errorCallback] = $batch->catchCallbacks();
            $errorCallback->getClosure()->call($job, $batch, new \Exception());

            $this->assertTrue(Session::get('error'));

            return true;
        });
    }

    public function test_fixed_page_scraper_batch_callback_finally_is_custom(): void
    {
        Session::flush();

        $apiRequest = $this->mock_api_request();

        $apiScraper = new PaginateApiScraper();
        $apiScraper->finally(function ($bath) {
            Session::put('finally', true);
        });

        Bus::fake();

        $job = new FixedMultipageScraper($apiRequest, $apiScraper, 4);
        $job->handle();

        Bus::assertBatched( function (PendingBatch $batch) use ($job){
            [$finallyCallback] = $batch->finallyCallbacks();
            $finallyCallback->getClosure()->call($job, $batch);

            $this->assertTrue(Session::get('finally'));

            return true;
        });
    }

    public function test_fixed_page_scraper_get_api_data(): void {
        Session::flush();

        $apiRequest = $this->mock_api_request();

        $apiScraper = new PaginateApiScraper();

        Bus::fake();
        Queue::fake();

        $job = new FixedMultipageScraper($apiRequest, $apiScraper, 2);
        $job->handle();

        Bus::assertBatched( function (PendingBatch $batch){
            $batch->jobs->each(function ($job){
                $job->handle();
            });

            $data = Session::get('api_data');

            return is_array($data) && (count($data) == 4) && in_array(3, $data);
        });
    }
}
