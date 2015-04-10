<?php
/**
 * @author Eremin Ivan
 * @email coding.ebola@gmail.com
 */
namespace App\Api\Parser;

use App\Models\ParserSource;

abstract class Parser implements ParserInterface
{
    /** @var int */
    protected $limitPerRequests = 0;

    /** @var \App\Models\ParserSource */
    protected $source;

    /**
     * @param \App\Models\ParserSource $source
     */
    public function __construct(ParserSource $source)
    {
        $this->source = $source;
    }

    /**
     * @param string $text
     * @param array $keywords
     *
     * @return bool
     */
    public function test($text, array $keywords)
    {
        preg_match('/(?:'.implode('|', $keywords).')/iu', strip_tags($text), $matches);
        return (bool) count($matches);
    }
}