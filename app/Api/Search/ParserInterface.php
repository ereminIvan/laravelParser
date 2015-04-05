<?php
/**
 * @author Eremin Ivan
 * @email coding.ebola@gmail.com
 */
namespace App\Api\Search;


interface ParserInterface
{
    /**
     * @return array
     */
    public function parse();

    /**
     * @param $item
     * @param $keywords
     * @return bool
     */
    public function test($item, $keywords);

    /**
     * @param $item
     * @return array
     */
    public function normalize($item);
}