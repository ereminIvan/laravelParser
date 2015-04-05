<?php
/**
 * @author Eremin Ivan
 * @email coding.ebola@gmail.com
 */
namespace App\Api\Search;

use \App\Api\TwitterAPI;

class TwitterParser extends Parser implements ParserInterface
{
    public function parse()
    {
        var_dump(TwitterAPI::getCodeBird());
        // TODO: Implement parse() method.
    }

    /**
     * @param $item
     * @param $keywords
     * @return bool
     */
    public function test($item, $keywords)
    {
        // TODO: Implement test() method.
    }

    /**
     * @param $item
     * @return array
     */
    public function normalize($item)
    {
        // TODO: Implement normalize() method.
    }
}