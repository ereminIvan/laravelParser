<?php
/**
 * @author Eremin Ivan
 * @email coding.ebola@gmail.com
 */
namespace App\Api\Search;

use App\Models\ParserSource;

class ParserFactory
{
    /**
     * @param ParserSource $source
     * @return Parser|ParserInterface
     */
    public static function factory(ParserSource $source)
    {
        return (new \ReflectionClass('\App\Api\Search\\' . ucfirst($source->type) . 'Parser'))
            ->newInstanceArgs([$source]);
    }
} 