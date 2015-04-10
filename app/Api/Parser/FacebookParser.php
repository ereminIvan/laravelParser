<?php
/**
 * @author Eremin Ivan
 * @email coding.ebola@gmail.com
 */
namespace App\Api\Parser;

use App\Api\FacebookAPI;
use FaceBook\GraphObject;

class FacebookParser extends Parser implements ParserInterface
{
    protected $limit = 100;

    public function parse()
    {
        $result = [];

        FacebookAPI::init();

        //Request explanation of page
        $sourceGraph = FacebookAPI::execute('GET', "?ids=" . urlencode($this->source->uri))
            ->getProperty($this->source->uri);

        if ($sourceGraph->getProperty('category')) {
            $uid = $sourceGraph->getProperty('id');
        } elseif ($sourceGraph->getProperty('og_object')) {
            $uid = $sourceGraph->getProperty('og_object')->getProperty('id');
        } else {
            preg_match('/\d+$|[\w\.]+$/i', $this->source->uri, $m);
            if (!isset($m[0])) {
                throw new \Exception('Undetectable page id', 404);
            }
            $uid = $m[0];
            unset($m);
        }

        $keywords = explode($this->source->keywords, ';');
        $iteration = 0;
        $query = "/{$uid}/posts?fields=id,message,link,created_time,name&limit={$this->limit}";
        do {
            list ($result, $failed, $query) =
                $this->processResult(
                    FacebookAPI::execute('GET', $query),
                    $keywords,
                    $this->source->executed_at,
                    $result
                );
        } while ($failed);

        unset($iteration, $failed, $query, $uid);
        return $result;
    }

    /**
     * @param GraphObject   $items
     * @param string        $keywords
     * @param string        $time
     * @param $result       $array
     *
     * @return array
     */
    protected function processResult($items, $keywords, $time, &$result)
    {
        $statement = true;
        $iteration = 0;
        /** @var GraphObject $item */
        foreach ($items->getPropertyAsArray('data') as $item) {
            ++$iteration;
            //If tweet created time less then last scheduler execute time - it is old tweet: go out
            if (strtotime($item->getProperty('created_time')) < strtotime($time)) {
                $statement = false;
                break;
            }
            if ($this->test($item, $keywords)) {
                $result[$item->getProperty('id')] = $this->normalize($item);
            }
        }

        if ($iteration == $this->limit) {
            $query = preg_replace(
                '/^https:\/\/graph\.facebook\.com\/v\d\.\d/', '',
                $items->getProperty('paging')->getProperty('next'));

        } else {
            $query = null;
            $statement = false;
        }
        unset($item, $iteration);

        return [$result, $statement, $query];
    }

    /**
     * @param GraphObject   $item
     * @param array         $keywords
     * @return bool
     */
    public function test($item, $keywords)
    {
        if ($text = $item->getProperty('message')) {
            preg_match('/(?:'.implode('|', $keywords).')/i', strip_tags($text), $matches);
            return (bool)count($matches);
        }
        return false;
    }

    /**
     * @param  GraphObject $item
     * @return array
     */
    public function normalize($item)
    {
        $message = $item->getProperty('message') ? : '';

        return [
            'id'            => $item->getProperty('id'),
            'title'         => $item->getProperty('name') ? : '',
            'description'   => substr($message, 0, 200),
            'text'          => $message,
            'link'          => $item->getProperty('link'),
            'created_at'    => date('Y-m-d H:i:s', strtotime($item->getProperty('created_time'))), //or updated_time?
            'user'          => [
                'id'    => null,
                'name'  => null
            ],
        ];
    }
}