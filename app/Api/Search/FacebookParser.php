<?php
/**
 * Created by PhpStorm.
 * User: Ebola
 * Date: 4/6/15
 * Time: 9:01 PM
 */

namespace App\Api\Search;

use App\Api\FacebookAPI;

class FacebookParser extends Parser implements ParserInterface
{
    public function parse()
    {
        FacebookAPI::init();
        $data = FacebookAPI::execute('GET', $this->source->uri);
        var_dump($data);
    }
}