<?php

namespace Acroweb\Mage\Service;

use Bitrix\Main\Web\HttpClient;

class Request
{
    /**
     * @param $url
     * @param $post
     * @param $token
     * @param $postFieldArray
     * @return array|false|mixed|\stdClass|null
     */
    public static function requestApi($url, $post = false, $token = false, $postFieldArray = false)
    {
        $options = array(
            "redirect" => true,
            "redirectMax" => 5,
            "waitResponse" => true,
            "socketTimeout" => 30,
            "streamTimeout" => 60,
            "version" => HttpClient::HTTP_1_0,
            "proxyHost" => "",
            "proxyPort" => "",
            "proxyUser" => "",
            "proxyPassword" => "",
            "compress" => false,
            "charset" => "",
            "disableSslVerification" => true,
        );
        $http = new HttpClient($options);
        $http->setHeader("Accept", "application/json");
        if ($post && $postFieldArray) {
            if ($token)
                $http->setHeader("Authorization", "Token " . $token);

            $http->setHeader("Content-Type", "application/json");
            $data = $http->post($url, json_encode($postFieldArray));
        } else {
            $data = $http->get($url);
        }

        return json_decode($data, true);
    }
}