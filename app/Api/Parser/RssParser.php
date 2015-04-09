<?php
/**
 * @author Eremin Ivan
 * @email coding.ebola@gmail.com
 */
namespace App\Api\Parser;

use App\Api\RssAPI;

class RssParser extends Parser implements ParserInterface
{
    public function parse()
    {
        $source = RssAPI::get($this->source->uri);

        $keywords = array_flip(explode(';', mb_convert_case($this->source->keywords, MB_CASE_UPPER, "UTF-8")));

        if(empty($keywords)) {
            throw new \Exception('Keywords not passed');
        }

        if ($source->attributes()->version == "2.0") {
            if (isset($source->channel->item)) {
                return $this->processResult($source->channel->item, $keywords, $this->source->executed_at);
            }
        } else {
            //todo implement other versions atom|rss1|rrs2
        }

        return [];
    }


    /**
     * @param $items
     * @param $keywords
     * @param $time
     * @return array
     */
    public function processResult($items, $keywords, $time)
    {
        $result = [];
        foreach ($items as $item) {
            if (strtotime($item->pubDate) < strtotime($time)) {
                continue;
            }
            if ($this->test($item, $keywords)) {
                $result[] = $this->normalize($item);
            }
        }
        unset($item);
        return $result;
    }

    /**
     * @param $item
     * @param $keywords
     * @return bool
     */
    public function test($item, $keywords)
    {
        if ($text = $item->description) {
            foreach (str_word_count(mb_convert_case($text, MB_CASE_UPPER, "UTF-8"), 2, self::CHAR_LIST) as $keyword) {
                if (isset($keywords[$keyword])) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param \SimpleXMLElement $item
     * @return array
     */
    public function normalize($item)
    {
        return [
            'id'            => (string) $item->guid,
            'title'         => (string) $item->title,
            'description'   => (string) $item->description,
            'text'          => (string) $item->description,
            'link'          => (string) $item->link,
            'created_at'    => (string) date('Y-m-d H:i:s', strtotime($item->pubDate)),
            'user'          => [
                'id' => null,
                'name' => null,
            ],
        ];
    }
}