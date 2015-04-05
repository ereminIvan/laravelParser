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
            if ($text = $item->getProperty('message')) {
                foreach (preg_split("/\s/", $text) as $keyword) {
                    if (isset($keywords[$keyword])) {
                        $result[] = $item->asArray();
                        continue 2;
                    }
                }
            }
            continue;
        }
//        $paging = $graph->getPropertyAsArray('paging');
//        if($this->source->limit > count($paging)) {
//            /** @var $p \Facebook\GraphObject */
//            foreach ($paging as $p) {
//                $p->asArray();
//            }
//        }
        return $result;
    }
}