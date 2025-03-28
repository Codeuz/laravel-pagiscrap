<?php

namespace Cdz\PagiScrap;

use Illuminate\Bus\Batch;
use Illuminate\Support\Testing\Fakes\PendingBatchFake;
use Throwable;

class PaginateApiScraper implements PaginateApiScraperInterface
{
    /**
     * @var string
     */
    protected string $name = self::class;

    /**
     * @var callable|null
     */
    protected $success = null;

    /**
     * @var callable|null
     */
    protected $error = null;

    /**
     * @var callable|null
     */
    protected $finally = null;

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param Batch|PendingBatchFake $batch
     * @return void
     */
    public function onSuccess(Batch|PendingBatchFake $batch) : void
    {
        if ($this->success) {
            call_user_func($this->success, $batch);
        }
    }

    /**
     * @param Batch|PendingBatchFake $batch
     * @param Throwable $exception
     * @return void
     */
    public function onError(Batch|PendingBatchFake $batch, Throwable $exception) : void
    {
        if ($this->error) {
            call_user_func($this->error, $batch, $exception);
        }
    }

    /**
     * @param Batch|PendingBatchFake $batch
     * @return void
     */
    public function onFinally(Batch|PendingBatchFake $batch) : void
    {
        if ($this->finally) {
            call_user_func($this->finally, $batch);
        }
    }

    /**
     * @param string $name
     * @return void
     */
    public function name(string $name) : void
    {
        $this->name = $name;
    }

    /**
     * @param callable $success
     * @return void
     */
    public function success(callable $success) : void
    {
        $this->success = $success;
    }

    /**
     * @param callable $error
     * @return void
     */
    public function error(callable $error) : void
    {
        $this->error = $error;
    }

    /**
     * @param callable $finally
     * @return void
     */
    public function finally(callable $finally) : void
    {
        $this->finally = $finally;
    }
}
