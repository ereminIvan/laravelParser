<?php
/**
 * @author Eremin Ivan
 * @email coding.ebola@gmail.com
 */
namespace App\Api\Search;


class RssParser extends Parser implements ParserInterface
{
    public function parse()
    {
        $source = simplexml_load_file($this->source->uri);

        $result = [];
        $keywords = array_flip($this->source->keywords);

        if ((float) $source->attributes()->version == 2) {
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
            throw new \Exception('RssParser: Unsupported RSS version');
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
            foreach (preg_split("/\s/", $text) as $keyword) {
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
            'created_at'    => (string) $item->pubDate,
            'title'         => (string) $item->title,
            'text'          => (string) $item->description,
            'link'          => (string) $item->link,
            'user'          => ['id' => null, 'name' => null,],
        ];
    }
}