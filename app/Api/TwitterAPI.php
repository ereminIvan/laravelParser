<?php
namespace App\Api;

use Cache;
use Codebird\Codebird;
use Log;

class TwitterAPI
{
    const CONSUMER_KEY = 'lDYvsCzVw0ysXM6nEYBxbOR7r';
    const CONSUMER_SECRET = 'Av2N5hRBRNkjjOTOz98kyTpv2OkbJ8QbAU1RR9W9ujkKU85nao';

    const ACCESS_TOKEN = '2207129125-1WjLfZ92m00B8zfrvPXMiwi8Rq98bnJnmq29zYh';
    const ACCESS_SECRET = 'Ck19BCnZEDVMEYUtRqOFdWhnkrBo0biT36Yr9B71gjQ62';

    const TWITTER_ID = 2207129125;

    /**
     * @var $codebird \Codebird\Codebird
     */
    protected static $codebird;

    public function getFollowersCount() {

        $followers_count = Cache::get('twitter_followers');
        if (!$followers_count) {
            $followers_count = $this->updateFollowersCache();
        }

        return $followers_count;
    }

    public function updateFollowersCache() {

        // fallback followers count
        $followers_count = 9000;

        try {
            $cb   = $this->getInstance();
            $info = $cb->users_show([
                'user_id' => self::TWITTER_ID
            ]);

            if (isset($info->errors) && !empty($info->errors)) {
                Log::warning('Twitter API Error = ' . print_r($info->errors, true));
            } else {
                $followers_count = $info->followers_count;
                if ($followers_count > 0) {
                    Log::info('twitter_followers: .' . $followers_count);
                    Cache::forever('twitter_followers', $followers_count);
                }
            }
        } catch (\Exception $e) {
            Log::error('Twitter API Error = ' . $e->getMessage());
        }

        return $followers_count;
    }

    /**
     * @return Codebird
     */
    protected function getInstance() {
        if (!self::$codebird) {

            Codebird::setConsumerKey(self::CONSUMER_KEY, self::CONSUMER_SECRET);

            $cb = Codebird::getInstance();
            $cb->setToken(self::ACCESS_TOKEN, self::ACCESS_SECRET);

            self::$codebird = $cb;
        }

        return self::$codebird;
    }

    /**
     * @return Codebird
     */
    public static function getCodeBird() {

        Codebird::setConsumerKey(self::CONSUMER_KEY, self::CONSUMER_SECRET);
        $cb = Codebird::getInstance();
        //$cb->setToken(self::ACCESS_TOKEN, self::ACCESS_SECRET);

        return $cb;
    }
}
