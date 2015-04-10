<?php
/**
 * @author Eremin Ivan
 * @email coding.ebola@gmail.com
 */
namespace App\Api\Parser;

use App\Api\TwitterAPI;

class TwitterParser extends Parser implements ParserInterface
{
    protected $limit = 200;

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

        $screenName = $this->extractScreenName($this->source->uri);
        $keywords = $this->source->keywords;
        $requestParams = ['screen_name' => $screenName];

        $lastCheckedId = null;
        $iteration = 0;

        do {
            ++$iteration;
            if ($iteration > 1 && $lastCheckedId) {
                $requestParams['max_id'] = $lastCheckedId;
            }
            list ($result, $failed, $lastCheckedId) =
                $this->processResult(
                    $this->request($handler, $requestParams),
                    $keywords,
                    $this->source->executed_at,
                    $result,
                    $lastCheckedId
                );
        } while ($failed);

        unset($iteration, $failed, $lastCheckedId);

        return $result;
    }

    /**
     * @param $url
     * @return string
     */
    public function extractScreenName($url)
    {
        preg_match("/https?:\/\/(www\.)?twitter\.com\/(#!\/)?@?([^\/]*)/", $url, $matches);
        return !empty($matches[3]) ? $matches[3] : $url;
    }

    /**
     * @param \Codebird\Codebird  $handler
     * @param array $params
     */
    public function request($handler, array $params)
    {
        return $handler->statuses_userTimeline(array_merge([
            'exclude_replies'   => 'true',  //may be turned off
            'include_rts'       => 'false', //may be turned off
            'count'             => $this->limit,
        ], $params));
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
    public function processResult($items, $keywords, $time, &$result, $lastCheckedId)
    {
        $statement = true;
        /** @var \StdClass $item */
        $iteration = 0;
        foreach ($items as $item) {
            //If we reach after iterations request limit we out of result set: go out
            if(++$iteration == $this->limit) {
                break;
            }
            //If tweet has no date - it is not tweet: go out
            if (empty($item->created_at) ) {
                $statement = false;
                break;
            }
            //If tweet created time less then last scheduler execute time - it is old tweet: go out
            if (strtotime($item->created_at) < strtotime($time)) {
                $statement = false;
                break;
            }

            if ($item instanceof \StdClass && $this->test($item, $keywords)) {
                $result[$item->id_str] = $this->normalize($item);
            }

            $lastCheckedId = $item->id_str;
        }

        unset($item, $iteration);

        return [$result, $statement, $lastCheckedId];
    }

    /**
     * @param \StdClass $item
     * @param array     $keywords
     *
     * @return bool
     */
    public function test($item, $keywords)
    {
        if (isset($item->text) && $item->text) {
            preg_match('/(?:'.implode('|', $keywords).')/i', strip_tags($item->text), $matches);
            return (bool)count($matches);
        }
        return false;
    }

    /**
     * @param $item
     * @return array
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