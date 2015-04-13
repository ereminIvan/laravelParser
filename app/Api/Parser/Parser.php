<?php
/**
 * @author Eremin Ivan
 * @email coding.ebola@gmail.com
 */
namespace App\Api\Parser;

use App\Models\ParserSource;

/**
 * @property int    limitPerRequests
 * @property array  keywords
 * @property string sourceURI
 */
abstract class Parser
{
    protected $limitPerRequests = 0;
    protected $sourceURI;
    protected $keywords = [];

    /**
     * @param string    $sourceURI
     * @param array     $keywords
     * @param string    $executedAt
     */
    public function __construct($sourceURI, array $keywords, $executedAt)
    {
        $this->sourceURI = $sourceURI;
        $this->keywords  = $keywords;
        $this->executedAt = $executedAt;
    }

    /**
     * @return array
     */
    public abstract function parse();

    /**
     * @param string $text
     *
     * @return bool
     */
    protected function test($text)
    {
        preg_match('/(?:'.implode('|', $this->keywords).')/iu', strip_tags($text), $matches);
        return (bool) count($matches);
    }
}