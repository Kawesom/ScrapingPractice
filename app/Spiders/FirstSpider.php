<?php

namespace App\Spiders;

use App\Class\GetUserAgent;
use App\ItemPipeline\MyJobMagProcessor;
use App\Models\FoundJobs;
use Exception;
use Generator;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\HtmlString;
use InvalidArgumentException;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use RoachPHP\Extensions\LoggerExtension;
use RoachPHP\Extensions\StatsCollectorExtension;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use RoachPHP\Spider\ParseResult;
use RoachPHP\Downloader\Middleware\UserAgentMiddleware;
use RoachPHP\Roach;
use Symfony\Component\DomCrawler\Crawler;

class FirstSpider extends BasicSpider
{
    public array $startUrls = [
        //'https://books.toscrape.com/'
        'https://www.myjobmag.com/search/jobs?q='
    ];

    public array $downloaderMiddleware = [
        RequestDeduplicationMiddleware::class,
        [UserAgentMiddleware::class, [
            //dd((new GetUserAgent)->Rand()),
            //"userAgent" => (new GetUserAgent)->Rand(),
            "userAgent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36"
            ]],
    ];

    public array $spiderMiddleware = [
        //
    ];

    public array $itemProcessors = [
        MyJobMagProcessor::class,
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
        $last_entry_from_prev_job_search = FoundJobs::find(10);
        $last_entry = FoundJobs::latest('id')->first([
            'job_name',
            'company_name',
            'job_url',
            'date_posted', //convert toDateTimeString()
        ]);
        if ($last_entry_from_prev_job_search && $last_entry_from_prev_job_search->job_url === $last_entry->job_url) {
            throw new InvalidArgumentException('Jackpot');
        }

        $items = $response
            ->filter('.job-info')
            ->each(fn(Crawler $node) => [
                'job_name' => explode(" at ",$node->filter('.mag-b a')->text())[0],
                'company_name' => explode(" at ",$node->filter('.mag-b a')->text())[1],
                'description' => $node->filter('.job-desc')->text(),
                'url' => $node->filter(".mag-b a")->link()->getUri(),
                'date_posted' => $node->filter('#job-date')->text(),
            ]);

        $currentPage = $response->filter('.current_page')->text() + 1;
        //dd($currentPage);
        try {
            $nextPageUrl = "https://www.myjobmag.com/search/jobs?&currentpage=".$currentPage;
            //dd($nextPageUrl);
            yield $this->request('GET', $nextPageUrl);

        } catch (Exception) {
        }

        // pass the extracted content to an item pipeline
        foreach ($items as $item) {
            yield $this->request('GET', $item['url'], 'parseJob', ['item' => $item]);
        }

         //find the next page URL and make a request, needs work since next class was removed
        //$next_page = $response->filter(".setPaginate > li:nth-child(2) > a");

         //follow the next page link if it exists
        //if ($next_page) {
            //$nextPageUrl = $next_page->link()->getUri();
            yield $this->request("GET", $nextPageUrl);
        //}
        //dd($items);
     /*   */
    }

/**
 * Parses the book page and returns a generator of items.
 */
    public function parseBookPage(Response $response): Generator
    {
        $item = $response->getRequest()->getOptions()['item'];

        $descriptionArray = $response
            ->filter('.product_page > p')
            ->each(fn(Crawler $node) => $node->text());

        $item['description'] = implode("\n", $descriptionArray);

        $avail = $response->filter('.instock.availability')->text();
        preg_match('/\d+/', $avail, $arr);

        $item['availability'] = implode('', $arr);
        $item['upc'] = $response->filter('table > tr > td')->text();

        //dd($item);
        yield $this->item($item);
    }

    public function parseJob(Response $response): Generator
    {
        $item = $response->getRequest()->getOptions()['item'];

        $descriptionArray = $response
            ->filter('.job-details')
            ->each(fn(Crawler $node) => $node->text());

        //image
        $item['group'] = $response->filter('ul.job-key-info > li')->each(fn($x) => $x->text());
        $i = 0;
        $x = 0;
        $matches = 0;
        $count = count($item['group']);
        $arr = ["Job Type ", "Qualification ", "Experience ", "Location ", "City ", "Job Field ", "Salary Range "];
        $arrCount = count($arr);
        while ($x < $count && $matches < $arrCount) {
            if (str_contains($item['group'][$x], $arr[$i])) {
                $item[$arr[$i]] = str_replace('&nbsp', '', str_replace($arr[$i], '', $item['group'][$x]));
                $matches++;
                $x++;
            } else {
                $i++;
                if ($i === count($arr)) {
                    $i = 0;
                    $x++; // Move to the next group element if no match is found
                }
            }
        }
        /*
        foreach($item['group'] as $value) {
            if (str_contains($value, $arr[$i])) {
                $item[$arr[$i]] = str_replace('&nbsp','',str_replace($arr[$i],'', $value));
                $i++;
            }
            //$i++;
        }
        */
        $item['description'] = new HtmlString(nl2br(e(implode("\n", $descriptionArray))));
        //$item['job_type'] = $response->filter('.job-key-info > li:nth-child(1) > span:nth-child(2) > a:nth-child(1)')->text('no job type specified');
        //$item['qualifications'] = $response->filter('.job-key-info li:nth-child(2) > span:nth-child(2)')->text('no qualification stated');
        //$item['experience'] = $response->filter('.job-key-info > li:nth-child(3) > span:nth-child(2)')->text('no experience stated');
        //salary range, some postings have, some don't
        //$item['state'] = $response->filter('.job-key-info li:nth-child(4) > span:nth-child(2)')->text('no state');
        //location(city)
        //$item['fields'] = $response->filter('.job-key-info > li:nth-child(5) > span:nth-child(2) > a:nth-child(1)')->text('no fields');
        //dd($item);&nbsp;
         //some links take you to other recruitmnt websites
        $item['email_to_apply'] = $response->filter('div.mag-b > p > strong:last-of-type')->text('no email');
        if (filter_var($item['email_to_apply'], FILTER_VALIDATE_EMAIL) === false) {
            $item['email_to_apply'] = $response->filter('div.mag-b > p > strong:first-of-type')->text('no email');
        }
        if ($item['email_to_apply'] == 'no email') {
            $item['link_to_apply'] = $response->filter('div.mag-b > a')->link()->getUri(); //try selectLink
            if ($item['link_to_apply'] == 'https://www.myjobmag.com/featured-jobs') {
                $item['link_to_apply'] = $response->filter('.apply-but')->link()->getUri();;
            }
        }
        $item['date_posted'] = substr($response->filter('#posted-date')->text(), 8);
        $item['deadline'] = substr($response->filter('.read-date-sec-li:nth-child(2)')->text(), 10);
        $item['materials_to_apply'] = $response->filter('div.mag-b > p')->text();
        $applyMaterials = [
            'CV',
            'cv',
            'Resume',
            'resume',
            'Cover Letter',
            'cover letter',
            'Portfolio',
            'portfolio',
            'References',
            'Transcript',
            'Samples',
        ];
        $item['materials_to_apply'] = array_filter($applyMaterials, function ($material) use ($item) {
            return str_contains($item['materials_to_apply'], $material);
        });
        //add application requirements
        //dd($item);

        $found_job = FoundJobs::orderByDesc('id')->first();
        //dd($found_job,$found_job->job_name === $item['job_name'],$found_job->company_name === $item['company_name'] ,$found_job->date_posted, Carbon::parse($item['date_posted'])->toDateTimeString(), $found_job->company_name, $item['company_name']);

        if ($found_job && $found_job->job_name == $item['job_name'] && $found_job->company_name === $item['company_name'] && $found_job->job_url === $item['url']) {
            throw new InvalidArgumentException('Jackpot');
        }

        yield $this->item($item);
    }
}
