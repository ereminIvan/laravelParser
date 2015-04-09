<?php
/**
 * @author Eremin Ivan
 * @email coding.ebola@gmail.com
 */
namespace App\Api\Parser;

use App\Api\TwitterAPI;

class TwitterParser extends Parser implements ParserInterface
{
    const REQUEST_LIMIT = 200;

    /**
     * @return array
     * @throws \Exception
     */
    public function parse()
    {
        $result = [];
        $handler = TwitterAPI::getCodeBird();
        $handler->setToken(TwitterAPI::ACCESS_TOKEN, TwitterAPI::ACCESS_SECRET);

        $keywords = array_flip(explode(';', mb_convert_case($this->source->keywords, MB_CASE_UPPER, "UTF-8")));

        if(empty($keywords)) {
           throw new \Exception('Keywords not passed');
        }

        $screenName = $this->extractScreenName($this->source->uri);

        $requestParams = ['screen_name' => $screenName];

        $lastId = null;
        $iteration = 0;

        do {
            ++$iteration;
            echo PHP_EOL. 'iteration: ' . $iteration . '  lastId : '. $lastId;
            if ($iteration > 1 && $lastId) {
                $requestParams['max_id'] = $lastId;
            }
            list ($result, $failed, $lastId) =
                $this->processResult(
                    $this->request($handler, $requestParams),
                    $keywords,
                    $this->source->executed_at,
                    $result,
                    $lastId
                );
        } while ($failed);

        $lastId = null;

        unset($iteration, $failed, $lastId);

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
            'count'             => self::REQUEST_LIMIT,
        ], $params));
    }

    /**
     * @param \StdClass $items      Current item of feed for check
     * @param array     $keywords   Keywords for search
     * @param string    $time       Time when last time task was executed
     * @param array     $result     Result set
     * @param string    $lastId     Last checked tweet id
     *
     * @return array
     */
    public function processResult($items, $keywords, $time, &$result, $lastId)
    {
        $statement = true;
        /** @var \StdClass $item */
        $iteration = 0;
        foreach ($items as $item) {
            //If we reach after iterations request limit we out of result set: go out
            if(++$iteration == self::REQUEST_LIMIT) {
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

            $lastId = $item->id_str;
        }

        unset($item, $iteration);

        return [$result, $statement, $lastId];
    }

    /**
     * @param \StdClass $item
     * @param array     $keywords
     *
     * @return bool
     */
    public function test($item, $keywords)
    {
        if (!property_exists($item, 'text')) {
            return false;
        }
        if ($text = $item->text) {
            foreach (str_word_count(mb_convert_case($text, MB_CASE_UPPER, "UTF-8"), 2, self::CHAR_LIST) as $keyword) {
                if (isset($keywords[$keyword])) {
                    return true;
                }
            }
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