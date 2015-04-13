<?php
/**
 * @author Eremin Ivan
 * @email coding.ebola@gmail.com
 */
namespace App\Api\Parser;

use App\Api\RssAPI;

class RssParser extends Parser
{
    /**
     * @return array
     */
    public function parse()
    {
        $source = RssAPI::get($this->sourceURI);

        if ($source->attributes()->version == "2.0") {
            if (isset($source->channel->item)) {
                return $this->processResults($source->channel->item);
            }
        } else {
            //todo implement other versions atom|rss1
        }

        return [];
    }


    /**
     * @param $items
     *
     * @return array
     */
    protected function processResults($items)
    {
        $result = [];
        foreach ($items as $item) {
            if (strtotime($item->pubDate) < strtotime($this->executedAt)) {
                continue;
            }
            if ($this->test($item->description, $this->keywords)) {
                $result[] = $this->normalize($item);
            }
        }
        unset($item);
        return $result;
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