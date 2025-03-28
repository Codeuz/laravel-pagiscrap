<?php

namespace Cdz\PagiScrap;

interface PaginateApiRequestInterface
{
    function pages() : int;
    function process(int $page) : mixed;
    function after(mixed $result) : void;
}
