<?php
/**
 * Created by PhpStorm.
 * User: Ebola
 * Date: 4/6/15
 * Time: 8:53 PM
 */

namespace App\Api\Search;


class Source
{
    public $type;
    public $uri;
    public $keywords;

    public function __construct($type, $uri, array $keywords)
    {
        $this->type = $type;
        $this->uri = $uri;
        $this->keywords = $keywords;
    }
}