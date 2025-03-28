# PagiScrap

[![Latest Version on Packagist](https://img.shields.io/packagist/v/cdz/laravel-pagiscrap)](https://packagist.org/packages/cdz/laravel-pagiscrap)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/Codeuz/laravel-pagiscrap/run-tests.yml)](https://github.com/Codeuz/laravel-pagiscrap/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/cdz/laravel-pagiscrap.svg?style=flat-square)](https://packagist.org/packages/cdz/laravel-pagiscrap)

PagiScrap will efficiently extract paginated data from any API while optimizing performance with Laravel Queues. Each page is processed asynchronously, ensuring a smooth, non-blocking execution.

## Support

- PHP >= 8.3
- Laravel >= 11

## Installation

You can install the package via composer:

```bash
composer require cdz/laravel-pagiscrap
```

## Usage

### API class
First, you need to create a custom class that will handle the API calls. This class should implement the **PaginateApiRequestInterface** and define its methods:

- **pages(): int** - 
This method returns the total number of pages available for the API calls. It helps determine how many pages need to be processed.

- **process(int $page): mixed** - This method handles the API request for the given page. It makes an API call, retrieves the data, updates the total number of pages, and returns the extracted data.

- **after(mixed $result): void:** - This method is executed after the data is retrieved. You can use it to format and save the data before using it in your application.

Here is a complete example to illustrate how to use this class:

```php
<?php

use Cdz\PagiScrap\PaginateApiRequestInterface;

class ApiRequest implements PaginateApiRequestInterface {

    protected int $pages = 1;

    function pages(): int
    {
        return $this->pages;
    }

    function process(int $page): mixed
    {
        // Insert your API call logic here
        $api = new MyApi();
        $result = $api->get_page_data($page);

        if ($result) {
            // Update the total number of pages based on the API response (pagination)
            $this->pages = $result['to'];

            // Return data
            return $result['data'];
        }

        return null;
    }

    function after(mixed $result): void
    {
        // Process data 
        foreach ($result as $data) {
            ...
        }
    }
}
```
### Scraping
Next, you can scrape the paginated data

```php
<?php
use Cdz\PagiScrap\PaginateApiScraper;
use Cdz\PagiScrap\Jobs\FixedMultipageScraper;
use Cdz\PagiScrap\Jobs\AdaptiveMultipageScraper;

// Your Custom class
$apiRequest = new ApiRequest();

// Scrape a specified number of pages (3)
FixedMultipageScraper::dispatch($apiRequest, new PaginateApiScraper(), 3);

// Or scrape all pages when the total number of pages is unknown.
AdaptiveMultipageScraper::dispatch($apiRequest, new PaginateApiScraper())
```
### Customisation
You can customize the batch name and define its completion callbacks.
```php
<?php
$apiScraper = new PaginateApiScraper();
$apiScraper->name('Import API data');
$apiScraper->finally(function ($batch){
    Log::info('The import is complete!');
});
$apiScraper->success(function ($batch){
    Log::info('The import was successful!');
});
$apiScraper->error(function ($batch, \Exception $exception){
    Log::info('The import failed!');
});

FixedMultipageScraper::dispatch($apiRequest, $apiScraper, 3);
```

### Run the Queue Worker
```php
php artisan queue:work
```
Refer to the [Laravel documentation](https://laravel.com/docs/11.x/queues#running-the-queue-worker) for queue configuration options.

## Testing

```bash
composer test
```

## Troubleshooting
Remember, queue workers are long-lived processes and store the booted application state in memory. As a result, they will not notice changes in your code base after they have been started. So, during your deployment process, be sure to [restart your queue workers](https://laravel.com/docs/11.x/queues#queue-workers-and-deployment). In addition, remember that any static state created or modified by your application will not be automatically reset between jobs.

## Credits

- [Aurelie Palette](https://github.com/Codeuz)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
