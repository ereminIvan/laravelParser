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
    public $requestLimit;

    public function __construct($type, $uri, array $keywords, $requestLimit = null)
    {
        $this->type = $type;
        $this->uri = $uri;
        $this->keywords = $keywords;
        $this->requestLimit = $requestLimit;
    }
}