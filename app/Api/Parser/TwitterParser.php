<?php
/**
 * @author Eremin Ivan
 * @email coding.ebola@gmail.com
 */
namespace App\Api\Parser;

use App\Api\TwitterAPI;

class TwitterParser extends Parser implements ParserInterface
{
    private $last_id;
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

        $iteration = 0;
        do {
            ++$iteration;

            $params = ['screen_name' => $screenName,];

            if ($iteration > 1 && $this->last_id) {
                $params = ['screen_name' => $screenName, 'max_id' => $this->last_id];
            }

            list ($result, $failed) =
                $this->processResult(
                    $this->request($handler, $params),
                    $keywords,
                    $this->source->executed_at,
                    $result
                );
        } while ($failed);
        $this->last_id = null;
        unset($iteration, $failed);

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
        return $handler->statuses_userTimeline(array_merge($requestParams = [
            'exclude_replies'   => 'true',  //may be turned off
            'include_rts'       => 'false', //may be turned off
            'count'             => self::REQUEST_LIMIT,
        ], $params));
    }

    /**
     * @param \StdClass $items
     * @param array $keywords
     * @param string $time
     * @param $result
     *
     * @return array
     */
    public function processResult($items, $keywords, $time, &$result)
    {
        $statement = true;
        /** @var \StdClass $item */
        $iteration = 0;
        foreach ($items as $item) {
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

            $this->last_id = $item->id_str;
        }
        unset($item, $iteration);
        return [$result, $statement];
    }

    /**
     * @param \StdClass $item
     * @param array     $keywords
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
            'id'            => (string) $item->id_str,
            'title'         => '',
            'description'   => (string) $item->text,
            'text'          => (string) $item->text,
            'link'          => "https://twitter.com/{$item->user->id}/status/{$item->id_str}",
            'created_at'    => date('Y-m-d H:i:s', strtotime($item->created_at)),
            'user'          => [
                'id'    => (string) $item->user->id,
                'name'  => (string) $item->user->name
            ],
        ];
    }
}