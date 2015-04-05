<?php
/**
 * @author Eremin Ivan
 * @email coding.ebola@gmail.com
 */
namespace App\Api\Search;

use \App\Api\Search\Parser\Source;

class ParserFactory
{
    /**
     * @param Source $source
     * @return Parser|ParserInterface
     */
    public static function factory(Source $source)
    {
        return (new \ReflectionClass('\App\Api\Search\\' . ucfirst($source->type) . 'Parser'))
            ->newInstanceArgs([$source]);
    }
} 