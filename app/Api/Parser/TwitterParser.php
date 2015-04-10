<?php
/**
 * @author Eremin Ivan
 * @email coding.ebola@gmail.com
 */
namespace App\Api\Parser;

use App\Api\TwitterAPI;

class TwitterParser extends Parser
{
    protected $limitPerRequests = 200;

    /**
     * @return array
     * @throws \Exception
     */

    public function parse()
    {
        $result = [];
        $handler = TwitterAPI::getCodeBird();
        $handler->setToken(TwitterAPI::ACCESS_TOKEN, TwitterAPI::ACCESS_SECRET);

        if(empty($this->source->keywords)) {
            throw new \Exception('Keywords not passed');
        }

        //Extract ScreenName
        preg_match("/https?:\/\/(www\.)?twitter\.com\/(#!\/)?@?([^\/]*)/", $this->source->uri, $matches);
        $screenName = !empty($matches[3]) ? $matches[3] : $this->source->uri;

        $keywords = explode(';', $this->source->keywords);
        $requestParams = ['screen_name' => $screenName];

        $lastCheckedId = null;
        $iteration = 0;
        do {
            echo PHP_EOL . 'Iteration: ' . $iteration . ' Last id: ' . $lastCheckedId;
            ++$iteration;
            if ($iteration > 1) {
                $requestParams['max_id'] = $lastCheckedId;
            }

            $items = $handler->statuses_userTimeline(array_merge([
                'exclude_replies'   => 'true',  //may be turned off
                'include_rts'       => 'false', //may be turned off
                'count'             => $this->limitPerRequests,
            ], $requestParams));

            list ($result, $failed, $lastCheckedId) =
                $this->processResults($items, $keywords, $this->source->executed_at, $result, $lastCheckedId);
            var_dump([$failed, $lastCheckedId]);
        } while ($failed);

        unset($iteration, $failed, $lastCheckedId);

        die;
        return $result;
    }

    /**
     * @param \StdClass $items          Current item of feed for check
     * @param string    $keywords       Keywords for search
     * @param string    $time           Time when last time task was executed
     * @param array     $result         Result set
     * @param string    $lastCheckedId  Last checked tweet id
     *
     * @return array
     */
    public function processResults($items, $keywords, $time, &$result, $lastCheckedId)
    {
        $statement = true;

        /** @var \StdClass $item */
        foreach ($items as $item) {
            if (!isset($item->id_str)) {
                break;
            }
            if ($lastCheckedId == $item->id_str) {
                continue;
            }
            //If tweet created time less then last scheduler execute time - it is old tweet: go out
            if (strtotime($item->created_at) < strtotime($time)) {
                break;
            }
            if ($this->test($item->text, $keywords)) {
                $result[$item->id_str] = $this->normalize($item);
            }

            $lastCheckedId = $item->id_str;
        }

        unset($item);

        return [$result, $statement, $lastCheckedId];
    }


    /**
     * @param $item
     *
     * @return array|mixed
     */
    public function normalize($item)
    {
        return [
            'id'            => $item->id_str,
            'title'         => '',
            'description'   => $item->text,
            'text'          => $item->text,
            'link'          => "https://twitter.com/{$item->user->id}/status/{$item->id_str}",
            'created_at'    => date('Y-m-d H:i:s', strtotime($item->created_at)),
            'user'          => [
                'id'        => $item->user->id,
                'name'      => $item->user->name
            ],
        ];
    }
}