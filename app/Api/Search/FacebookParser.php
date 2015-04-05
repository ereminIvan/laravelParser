<?php
/**
 * @author Eremin Ivan
 * @email coding.ebola@gmail.com
 */
namespace App\Api\Search;

use App\Api\FacebookAPI;
use FaceBook\GraphObject;

class FacebookParser extends Parser implements ParserInterface
{
    /**
     * @return array
     */
    public function parse()
    {
        FacebookAPI::init();
        $graph = FacebookAPI::execute('GET', $this->source->uri);

        $result = [];
        $keywords = array_flip($this->source->keywords);

        /** @var $item GraphObject */
        foreach ($graph->getPropertyAsArray('data') as $item) {
            if($this->test($item, $keywords)) {
                $result[] = $this->normalize($item);
            }
        }
//If we need to get other results from paginated set this part should to implemented
//        $paging = $graph->getPropertyAsArray('paging');
//        if($this->source->requestLimit > count($paging)) {
//            /** @var $p \Facebook\GraphObject */
//            foreach ($paging as $p) {
//                $p->asArray();
//            }
//        }
        return $result;
    }

    /**
     * @param GraphObject   $item
     * @param array         $keywords
     * @return bool
     */
    public function test($item, $keywords)
    {
        if ($text = $item->getProperty('message')) {
            foreach (preg_split("/\s/", $text) as $keyword) {
                if (isset($keywords[$keyword])) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param GraphObject $item
     * @return array
     */
    public function normalize($item)
    {
        return $item->asArray();
    }
}