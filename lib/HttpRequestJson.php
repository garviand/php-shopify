<?php
/**
 * Created by PhpStorm.
 * @author Tareq Mahmood <tareqtms@yahoo.com>
 * Created at: 8/24/16 9:03 AM UTC+06:00
 */

namespace PHPShopify;

/**
 * Class HttpRequestJson
 *
 * Prepare the data / headers for JSON requests, make the call and decode the response
 * Accepts data in array format returns data in array format
 *
 * @uses CurlRequest
 *
 * @package PHPShopify
 */
class HttpRequestJson
{

    /**
     * HTTP request headers
     *
     * @var array
     */
    private static $httpHeaders;

    /**
     * Prepared JSON string to be posted with request
     *
     * @var string
     */
    private static $postDataJSON;


    /**
     * Prepare the data and request headers before making the call
     *
     * @param array $dataArray
     * @param array $httpHeaders
     *
     * @return void
     */
    protected static function prepareRequest($httpHeaders = array(), $dataArray = array())
    {

        self::$postDataJSON = json_encode($dataArray);

        self::$httpHeaders = $httpHeaders;

        if (!empty($dataArray)) {
            self::$httpHeaders['Content-type'] = 'application/json';
            self::$httpHeaders['Content-Length'] = strlen(self::$postDataJSON);
        }
    }

    /**
     * Implement a GET request and return json decoded output
     *
     * @param string $url
     * @param array $httpHeaders
     *
     * @return string
     */
    public static function get($url, $httpHeaders = array())
    {

        self::prepareRequest($httpHeaders);

        ShopifySDK::checkApiCallLimit();

        $params = parse_url($url);

        $basic = base64_encode($params['user'] . ':' . $params['pass']);

        $auth = array(
            'Authorization' => 'Basic ' . $basic
        );

        $args = array(
            'headers' => $auth
        );

        // $streams = new \WP_Http_Streams();

        // $response = $streams->request($url, $args);

        $response = wp_remote_get($url, $args);

        if(isset($response->error_data)){
            throw new \Exception("The request you made was not valid. Please recheck your Store URL.");
        }

        return self::processResponse($response['body']);
    }

    /**
     * Implement a POST request and return json decoded output
     *
     * @param string $url
     * @param array $dataArray
     * @param array $httpHeaders
     *
     * @return string
     */
    public static function post($url, $dataArray, $httpHeaders = array())
    {
        self::prepareRequest($httpHeaders, $dataArray);

        ShopifySDK::checkApiCallLimit();
        $response = wp_remote_get($url, self::$httpHeaders);

        return self::processResponse($response['body']);
    }

    /**
     * Implement a PUT request and return json decoded output
     *
     * @param string $url
     * @param array $dataArray
     * @param array $httpHeaders
     *
     * @return string
     */
    public static function put($url, $dataArray, $httpHeaders = array())
    {
        self::prepareRequest($httpHeaders, $dataArray);

        ShopifySDK::checkApiCallLimit();
        $response = wp_remote_get($url, self::$httpHeaders);

        return self::processResponse($response['body']);
    }

    /**
     * Implement a DELETE request and return json decoded output
     *
     * @param string $url
     * @param array $httpHeaders
     *
     * @return string
     */
    public static function delete($url, $httpHeaders = array())
    {
        self::prepareRequest($httpHeaders);

        ShopifySDK::checkApiCallLimit();
        $response = wp_remote_get($url, self::$httpHeaders);

        return self::processResponse($response['body']);
    }

    /**
     * Decode JSON response
     *
     * @param string $response
     *
     * @return array
     */
    protected static function processResponse($response)
    {
        return json_decode($response, true);
    }

}