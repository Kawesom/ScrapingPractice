<?php

// specify the namespace
namespace App\ItemPipeline;

// import the required modules

use Illuminate\Support\Facades\DB;
use RoachPHP\ItemPipeline\ItemInterface;
use RoachPHP\ItemPipeline\Processors\ItemProcessorInterface;
use RoachPHP\Support\Configurable;

class CsvProcessor implements ItemProcessorInterface
{
    // allow configuration
    use Configurable;

    public $file;

    public function __construct()
    {
        // open the CSV file in the write mode
        $this->file = fopen("product.csv", "a");
    }

    public function processItem(ItemInterface $item): ItemInterface
    {
        // obtain the extracted data from the item
        $data = $item->all();

        // write the data to the CSV file
        fputcsv($this->file, $data);

        DB::table('scraping_data')->insert([
            'title' => $data['title'],
            'price' => str_replace('£','',$data['price']),
            'image' => $data['image'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $item;
    }

    // close the file
    public function __destruct()
    {
        fclose($this->file);
    }
}
