<?php

namespace app\Class;

use Dotenv\Util\Str;

class GetUserAgent
{
    private $json;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->json = json_decode(file_get_contents('./user_agents.json'), true);
    }

    public function Rand() {
        $userAgent = $this->json['user_agents'][array_rand($this->json['user_agents'])];
        return $userAgent;
    }
}
