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

        $keywords = explode($this->source->keywords, ';');

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
    protected function processResult($items, $keywords, $time)
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
     * @param array $keywords
     * @return bool
     */
    public function test($item, $keywords)
    {
        if ($text = $item->description) {
            preg_match('/(?:'.implode('|', $keywords).')/i', strip_tags($text), $matches);
            return (bool)count($matches);
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