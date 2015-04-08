<?php
/**
 * @author Eremin Ivan
 * @email coding.ebola@gmail.com
 */
namespace App\Api\Parser;

use App\Models\ParserSource;

class ParserFactory
{
    /**
     * @param ParserSource $source
     * @return Parser|ParserInterface
     */
    public static function factory(ParserSource $source)
    {
        return (new \ReflectionClass('\App\Api\Parser\\' . ucfirst($source->type) . 'Parser'))
            ->newInstanceArgs([$source]);
    }
} 