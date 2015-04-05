<?php
/**
 * Created by PhpStorm.
 * User: Ebola
 * Date: 4/6/15
 * Time: 9:15 PM
 */

namespace App\Api\Search;

use \App\Api\Search\Source;

class ParserFactory
{
    /**
     * @param Source $source
     * @return Parser
     */
    public static function factory(Source $source)
    {
        return (new \ReflectionClass('\App\Api\Search\\' . ucfirst($source->type) . 'Parser'))
            ->newInstanceArgs([$source]);
    }
} 