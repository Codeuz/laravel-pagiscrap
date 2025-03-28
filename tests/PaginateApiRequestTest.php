<?php

namespace Cdz\PagiScrap\Tests;

class PaginateApiRequestTest extends TestCase
{
    use ApiRequestTrait;

    public function test_api_interface_can_be_implemented(): void {
        $api = $this->mock_api_request();

        $this->assertTrue($api->pages() == 1);

        $result = $api->process(1);
        $this->assertContains(1, $result);
        $this->assertContains(2, $result);

        $this->assertTrue($api->pages() == 4);
    }
}
