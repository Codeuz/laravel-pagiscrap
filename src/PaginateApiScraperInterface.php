<?php

namespace Cdz\PagiScrap;


use Illuminate\Bus\Batch;
use Throwable;

interface PaginateApiScraperInterface
{
    function getName() : string;
    function onSuccess(Batch $batch) : void;
    function onError(Batch $batch, Throwable $exception) : void;
    function onFinally(Batch $batch) : void;
}
