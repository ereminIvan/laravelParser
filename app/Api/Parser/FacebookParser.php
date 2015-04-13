<?php
/**
 * @author Eremin Ivan
 * @email coding.ebola@gmail.com
 */
namespace App\Api\Parser;

use App\Api\FacebookAPI;
use FaceBook\GraphObject;

class FacebookParser extends Parser
{
    protected $limitPerRequests = 100;

    public function parse()
    {
        $result = [];

        FacebookAPI::init();

        //Request explanation of page
        $sourceGraph = FacebookAPI::execute('GET', "?ids=" . urlencode($this->sourceURI))
            ->getProperty($this->sourceURI);

        if ($sourceGraph->getProperty('category')) {
            $uid = $sourceGraph->getProperty('id');
        } elseif ($sourceGraph->getProperty('og_object')) {
            $uid = $sourceGraph->getProperty('og_object')->getProperty('id');
        } else {
            preg_match('/\d+$|[\w\.]+$/i', $this->sourceURI, $m);
            if (!isset($m[0])) {
                throw new \Exception('Undetectable page id', 404);
            }
            $uid = $m[0];
            unset($m);
        }

        $query = "/{$uid}/posts?fields=id,message,link,created_time,name&limit={$this->limitPerRequests}";

        do {
            list ($result, $failed, $query) = $this->processResults(FacebookAPI::execute('GET', $query), $result);
        } while ($failed);

        unset($failed, $query, $uid);
        return $result;
    }

    /**
     * @param GraphObject   $items
     *
     * @param $result       $array
     *
     * @return array
     */
    protected function processResults($items, &$result)
    {
        $statement = true;
        $iteration = 0;
        $query = null;

        /** @var GraphObject $item */
        foreach ($items->getPropertyAsArray('data') as $item) {
            ++$iteration;
            //If tweet created time less then last scheduler execute time - it is old tweet: go out
            if (strtotime($item->getProperty('created_time')) < strtotime($this->executedAt)) {
                $statement = false;
                break;
            }
            if ($this->test($item->getProperty('message'), $this->keywords)) {
                $result[$item->getProperty('id')] = $this->normalize($item);
            }
        }

        if ($iteration == $this->limitPerRequests) {
            $query = preg_replace(
                '/^https:\/\/graph\.facebook\.com\/v\d\.\d/', '',
                $items->getProperty('paging')->getProperty('next'));
        } else {
            $statement = false;
        }

        unset($item, $iteration);

        return [$result, $statement, $query];
    }

    /**
     * @param  GraphObject $item
     * @return array
     */
    protected function normalize($item)
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