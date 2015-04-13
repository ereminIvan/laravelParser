<?php
/**
 * @author Eremin Ivan
 * @email coding.ebola@gmail.com
 */
namespace App\Api\Parser;

use App\Api\TwitterAPI;
use League\Flysystem\Exception;

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

		//Extract ScreenName
		preg_match("/https?:\/\/(www\.)?twitter\.com\/(#!\/)?@?([^\/]*)/", $this->sourceURI, $matches);

		$requestParams = [
			'screen_name' => !empty($matches[3]) ? $matches[3] : $this->sourceURI,
		];

		$lastCheckedId = null;
		$iteration = 0;
		do {
			echo PHP_EOL . 'Iteration: ' . $iteration . ' | Last id: ' . $lastCheckedId . ' | Count: ' . count($result);
			++$iteration;
			if ($iteration > 1) {
				$requestParams['max_id'] = $lastCheckedId;
			}

			$items = $handler->statuses_userTimeline(array_merge([
				'exclude_replies'   => 'true',  //may be turned off
				'include_rts'       => 'false', //may be turned off
				'count'             => $this->limitPerRequests,
			], $requestParams));

			if(isset($items->errors) && count($items->errors)) {
				throw new \Exception($items->errors[0]->message, $items->errors[0]->code);
			}

			list ($result, $failed, $lastCheckedId) = $this->processResults($items, $result, $lastCheckedId);

		} while ($failed);

		unset($iteration, $failed, $lastCheckedId);

		return $result;
	}

	/**
	 * @param \StdClass $items          Current item of feed for check
	 * @param array     $result         Result set
	 * @param string    $lastCheckedId  Last checked tweet id
	 *
	 * @return array
	 */
	public function processResults($items, &$result, $lastCheckedId)
	{
		$statement = true;
		/** @var \StdClass $item */

		if(count((array)$items) <= 4) {
			$statement = false;
		}

		foreach ($items as $item) {
			if (!isset($item->id_str)) {
				break;
			}
			if ($lastCheckedId == $item->id_str) {
				continue;
			}

			$lastCheckedId = $item->id_str;

			//If tweet created time less then last scheduler execute time - it is old tweet: go out
			if (strtotime($item->created_at) < strtotime($this->executedAt)) {
				$statement = false;
				break;
			}

			if ($this->test($item->text)) {
//                echo PHP_EOL . $item->id_str;
				$result[$item->id_str] = $this->normalize($item);
			}
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