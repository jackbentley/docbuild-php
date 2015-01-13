<?php

namespace Vivait\DocBuild\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\TooManyRedirectsException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Message\Response;

class GuzzleAdapter implements HttpAdapter
{
    /**
     * @var Client
     */
    private $guzzle;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $key;

    /**
     * @var Response
     */
    private $response;


    /**
     * @param Client $guzzle
     */
    public function __construct(Client $guzzle = null)
    {
        if(!$guzzle){
            $this->guzzle = new Client();
        } else {
            $this->guzzle = $guzzle;
        }
    }

    /**
     * @param $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    public function get($resource, $request = [], $headers = [], $json = true)
    {
        $this->sendRequest('get', $this->url . $resource, $request, $headers);
    }

    public function post($resource, $request = [], $headers = [], $json = true)
    {
        $this->sendRequest('post', $request, $headers);
    }

    public function sendRequest($method, $url, array $options = [])
    {
        $options['exceptions'] = false;

        try{
            $request = $this->guzzle->createRequest($method, $url, $options);
            $this->response = $this->guzzle->send($request);
        } catch (TooManyRedirectsException $e) {

        } catch (RequestException $e){
            // dns/connection timeout
        } catch (TransferException $e) {
        }
    }

    public function getResponseCode()
    {
        return $this->response->getStatusCode();
    }

    public function getResponseContent()
    {
        return $this->response->getBody()->getContents();
    }

    public function getResponseHeaders()
    {
        return $this->response->getHeaders();
    }
}
