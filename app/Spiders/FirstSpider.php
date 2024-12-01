<?php

namespace App\Spiders;

use App\ItemPipeline\CsvProcessor;
use Exception;
use Generator;
use InvalidArgumentException;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use RoachPHP\Extensions\LoggerExtension;
use RoachPHP\Extensions\StatsCollectorExtension;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use RoachPHP\Spider\ParseResult;
use RoachPHP\Downloader\Middleware\UserAgentMiddleware;
use Symfony\Component\DomCrawler\Crawler;

class FirstSpider extends BasicSpider
{
    public array $startUrls = [
        'https://books.toscrape.com/'
    ];

    public array $downloaderMiddleware = [
        RequestDeduplicationMiddleware::class,
        [UserAgentMiddleware::class, [
            "userAgent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36"
            ]],
    ];

    public array $spiderMiddleware = [
        //
    ];

    public array $itemProcessors = [
        CsvProcessor::class,
    ];

    public array $extensions = [
        LoggerExtension::class,
        StatsCollectorExtension::class,
    ];

    public int $concurrency = 2;

    public int $requestDelay = 1;

    /**
     * @return Generator<ParseResult>
     */
    public function parse(Response $response): Generator
    {
        $items = $response
            ->filter("article.product_pod")
            ->each(fn(Crawler $node) => [
                "title" => $node->filter(".image_container img")->attr('alt'),
                "price" => $node->filter(".price_color")->text(),
                //"url" => $node->filter("a")->link()->getUri(),
                "image" => $node->filter(".thumbnail")->attr("src"),
            ]);

            try {
                $nextPageUrl = $response->filter('.next > a')->link()->getUri();
                yield $this->request('GET', $nextPageUrl);
            } catch (Exception) {
            }
        

        // pass the extracted content to an item pipeline
        foreach ($items as $item) {
          yield $this->item($item);
        }
        /*
        // find the next page URL and make a request
        $next_page = $response->filter(".next > a");

        // follow the next page link if it exists
        if ($next_page) {
            $nextPageUrl = $next_page->link()->getUri();
            yield $this->request("GET", $nextPageUrl);
        }
            */
    }
}
