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
     * @param string $sourceType
     * @param string $sourceURI
     * @param array  $keywords
     * @param string $executedAt
	 * @param string $createdAt
     *
     * @return Parser
     */
    public static function factory($sourceType, $sourceURI, array $keywords, $executedAt, $createdAt)
    {
        return (new \ReflectionClass('\App\Api\Parser\\' . ucfirst($sourceType) . 'Parser'))
            ->newInstanceArgs([$sourceURI, $keywords, $executedAt, $createdAt]);
    }
} 