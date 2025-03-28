<?php

namespace Cdz\PagiScrap\Tests;

use Cdz\PagiScrap\PaginateApiRequestInterface;
use Illuminate\Support\Facades\Session;

trait ApiRequestTrait
{
    protected function mock_api_request(): PaginateApiRequestInterface
    {
        return new class() implements PaginateApiRequestInterface {
            protected int $pages = 1;

            public function pages(): int
            {
                return $this->pages;
            }

            public function process(int $page): array
            {
                $num_pages = 4;

                $data = [
                    [1, 2], // Page 1 data
                    [3, 4], // Page 2 data
                    [5, 6], // Page 3 data
                    [7, 8]  // Page 4 data
                ];

                $result = [
                    "data" => $data[$page-1],
                    "links" => [
                        "first" => "http://example.com/users?page=1",
                        "last" => "http://example.com/users?page=".$num_pages,
                        "prev" => ($page > 1) ? "http://example.com/users?page=".($page - 1) : null,
                        "next" => ($page < $num_pages) ? "http://example.com/users?page=".($page + 1) : null,
                    ],

                    "meta" => [
                        "current_page" => $page,
                        "from" => 1,
                        "last_page" => $num_pages,
                        "path" => "http://example.com/users",
                        "per_page" => 15
                    ]
                ];

                $this->pages = $result['meta']['last_page'];

                return $result['data'];
            }

            public function after(mixed $result): void
            {
                foreach ($result as $r) {
                    Session::push('api_data', $r);
                }
            }
        };
    }
}
