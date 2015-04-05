<?php
/**
 * Created by PhpStorm.
 * User: Ebola
 * Date: 4/6/15
 * Time: 9:51 PM
 */

namespace App\Api\Search;


class Parser
{
    protected $source;

    public function __construct(Source $source)
    {
        $this->source = $source;
    }
} 