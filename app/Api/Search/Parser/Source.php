<?php
/**
 * @author Eremin Ivan
 * @email coding.ebola@gmail.com
 */
namespace App\Api\Search\Parser;


class Source
{
    public $type;
    public $uri;
    public $keywords;

    public $limit = 10;

    public function __construct($type, $uri, array $keywords)
    {
        $this->type = $type;
        $this->uri = $uri;
        $this->keywords = $keywords;
    }
}