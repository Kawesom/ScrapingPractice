<?php

namespace App\Class;

use Dotenv\Util\Str;

class TagFinder
{
    private $json;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->json = json_decode(file_get_contents('data.json'), true);
    }

    public function DescriptionSearch(String $description) {
        $skillsArray = $this->json['Skills'];
        $arr = [];

        foreach($skillsArray as $skill) {
            if (str_contains($description, $skill)) {
                array_push($arr, $skill);
            }
        }

        return $arr;
    }
}
