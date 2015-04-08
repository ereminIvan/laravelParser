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
        $result = [];
        $keywords = array_flip(explode(';', $this->source->keywords));
        if ($source->attributes()->version == "2.0") {
//Limits
//            $items = !is_null($this->source->requestLimit)
//                ? array_slice((array) $source->channel->item, 0, $this->source->requestLimit)
//                : $source->channel->item;
            foreach ($source->channel->item as $item) {
                if ($this->test($item, $keywords)) {
                    $result[] = $this->normalize($item);
                }
            }
        } else {
            //todo implement other versions atom|rss1|rrs2
        }
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
            foreach (preg_split("/\s/i", $text) as $keyword) {
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
            'text'          => '',
            'link'          => (string) $item->link,
            'created_at'    => (string) $item->pubDate,
            'user'          => [
                'id' => null,
                'name' => null,
            ],
        ];
    }
}