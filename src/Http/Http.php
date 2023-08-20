<?php

namespace KyleWLawrence\Cloudflare\Http;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\LazyOpenStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Utils;
use KyleWLawrence\Cloudflare\Http\Exceptions\ApiResponseException;
use KyleWLawrence\Cloudflare\Http\Exceptions\AuthException;
use Psr\Http\Message\StreamInterface;

/**
 * HTTP functions via curl
 */
class Http
{
    public static $curl;

    /**
     * Use the send method to call every endpoint except for oauth/tokens
     *
     * @param  HttpClient  $client
     * @param  string  $endPoint E.g. "/tickets.json"
     * @param  array  $options
     *                             Available options are listed below:
     *                             array $queryParams Array of unencoded key-value pairs, e.g. ["ids" => "1,2,3,4"]
     *                             array $postFields Array of unencoded key-value pairs, e.g. ["filename" => "blah.png"]
     *                             string $method "GET", "POST", etc. Default is GET.
     *                             string $contentType Default is "application/json"
     * @return \stdClass | null The response body, parsed from JSON into an object. Also returns null if something went wrong
     *
     * @throws ApiResponseException
     * @throws AuthException
     */
    public static function send(
        HttpClient $client,
        $endPoint,
        $options = []
    ) {
        if ( ! isset($options['postFields'] ) ) {
            $options['postFields'] = [];
        }

        $options = array_merge(
            [
                'method' => 'GET',
                'contentType' => 'application/json',
                'postFields' => [],
                'queryParams' => null,
            ],
            $options
        );

        $headers = array_merge([
            'Accept' => 'application/json',
            'Content-Type' => $options['contentType'],
            'User-Agent' => $client->getUserAgent(),
        ], $client->getHeaders());

        $request = new Request(
            $options['method'],
            $client->getApiUrl().$client->getApiBasePath().$endPoint,
            $headers
        );

        $requestOptions = [];

        if (! empty($options['multipart'])) {
            $request = $request->withoutHeader('Content-Type');
            $requestOptions['multipart'] = $options['multipart'];
        } elseif (! empty($options['postFields'])) {
            $request = $request->withBody(Utils::streamFor(json_encode($options['postFields'])));
        } elseif (! empty($options['file'])) {
            if ($options['file'] instanceof StreamInterface) {
                $request = $request->withBody($options['file']);
            } elseif (is_file($options['file'])) {
                $fileStream = new LazyOpenStream($options['file'], 'r');
                $request = $request->withBody($fileStream);
            }
        }

        if (! empty($options['queryParams'])) {
            foreach ($options['queryParams'] as $queryKey => $queryValue) {
                $uri = $request->getUri();
                $uri = $uri->withQueryValue($uri, $queryKey, $queryValue);
                $request = $request->withUri($uri, true);
            }
        }

        try {
            $response = $client->guzzle->send($request, $requestOptions);
        } catch (RequestException $e) {
            $requestException = RequestException::create($e->getRequest(), $e->getResponse(), $e);
            throw new ApiResponseException($requestException);
        } finally {
            $client->setDebug(
                $request->getHeaders(),
                $request->getBody(),
                isset($response) ? $response->getStatusCode() : null,
                isset($response) ? $response->getHeaders() : null,
                isset($e) ? $e : null
            );

            $request->getBody()->rewind();
        }

        return json_decode($response->getBody());
    }
}