<?php
namespace App\Api;

/**
 * Class RssAPI
 *
 * @author  Ivan Eremin <coding.ebola@gmail.com>
 * @package App\Api
 */
class RssAPI
{
    /**
     * @param $uri
     *
     * @return \SimpleXmlElement
     */
    public static function get($uri)
    {
        $handler = curl_init();
        curl_setopt($handler, CURLOPT_URL, $uri);
        curl_setopt($handler, CURLOPT_HEADER, 0);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($handler);
        curl_close($handler);
        return new \SimpleXmlElement($data, LIBXML_NOCDATA);
    }
}