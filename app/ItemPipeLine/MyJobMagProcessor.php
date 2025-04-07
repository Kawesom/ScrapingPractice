<?php

// specify the namespace
namespace App\ItemPipeline;

// import the required modules

use App\Class\TagFinder;
use App\Models\FoundJobs;
use App\Models\JobSource;
use Illuminate\Support\Facades\DB;
use RoachPHP\ItemPipeline\ItemInterface;
use RoachPHP\ItemPipeline\Processors\ItemProcessorInterface;
use RoachPHP\Support\Configurable;

class MyJobMagProcessor implements ItemProcessorInterface
{
    // allow configuration
    use Configurable;
    /*
    public $file;

    public function __construct()
    {
        // open the CSV file in the write mode
        $this->file = fopen("product.csv", "a");
    }
        */

    public function processItem(ItemInterface $item): ItemInterface
    {
        // obtain the extracted data from the item
        $data = $item->all();

        // write the data to the CSV file fputcsv($this->file, $data);

        /*
        DB::table('scraping_data')->insert([
            'title' => $data['title'],
            'price' => str_replace('£','',$data['price']),
            'description' => $data['description'],
            //'upc' => $data['upc'],
            'link' => $data['url'],
            'availability' => $data['availability'],
            'image' => $data['image'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        */
        //dd($data);
        FoundJobs::create([
            'job_name' => $data['job_name'],
            'company_name' => $data['company_name'],
            'industry' => $data['Job Field '],
            'job_url' => $data['url'],
            //'employer_image',
            //'salary',
            'description' => $data['description'],
            //'requirements',
            //'gender_preference',
            //'people_applied',
            'years_of_experience' => $data['Experience '] ??= '0',
            //'worker_looking_for',
            //'min_age',
            //'max_age',
            'salary' => $data['Salary Range '] ??= null,
            'qualification' => $data['Qualification '] ?? "None Specified",
            'city_or_province' => $data['City '] ?? null,
            'state' => $data['Location '] ??= 'Unknown',
            'country' => "Nigeria",
            'field_hiring_for' => $data['Job Field '],
            'tags' => (new TagFinder)->DescriptionSearch($data['description']),
            'job_type' => $data['Job Type '],
            'relevant_fields' => $data['Job Field '],
            'source_id' => 1,
            'email_to_apply' => ($data['email_to_apply'] == 'no email') ? null : trim($data['email_to_apply']),
            'link_to_apply' => $data['link_to_apply'] ?? null,
            'date_posted' => $data['date_posted'],
            'materials_to_apply' => (count(array_values($data['materials_to_apply'])) === 0) ? ['CV'] : array_values($data['materials_to_apply']),
            'deadline' => ($data['deadline'] == 'Not specified') ? null : $data['deadline'],
        ]);

        $last_crawled_id = FoundJobs::latest('id')->first()->id;

        JobSource::updateOrCreate([
            'job_source_id' => 1
        ], [
            'last_crawled' => $last_crawled_id,
        ]);

        return $item;
    }

    /* close the file
    public function __destruct()
    {
        fclose($this->file);
    }
        */
}
