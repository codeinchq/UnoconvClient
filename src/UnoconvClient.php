<?php
//
// +---------------------------------------------------------------------+
// | CODE INC. SOURCE CODE                                               |
// +---------------------------------------------------------------------+
// | Copyright (c) 2019 - Code Inc. SAS - All Rights Reserved.           |
// | Visit https://www.codeinc.fr for more information about licensing.  |
// +---------------------------------------------------------------------+
// | NOTICE:  All information contained herein is, and remains the       |
// | property of Code Inc. SAS. The intellectual and technical concepts  |
// | contained herein are proprietary to Code Inc. SAS are protected by  |
// | trade secret or copyright law. Dissemination of this information or |
// | reproduction of this material is strictly forbidden unless prior    |
// | written permission is obtained from Code Inc. SAS.                  |
// +---------------------------------------------------------------------+
//
// Author:   Joan Fabrégat <joan@codeinc.fr>
// Date:     2019-02-28
// Project:  unoconv-webservice-client
//
declare(strict_types=1);
namespace CodeInc\UnoconvClient;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;


/**
 * Class UnoconvClient
 *
 * @package CodeInc\UnoconvClient
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class UnoconvClient
{
    /**
     * @var string
     */
    private $apiUri;

    /**
     * @var Client
     */
    private $client;

    /**
     * UnoconvService constructor.
     *
     * @param string $apiUri
     * @param Client|null $client
     */
    public function __construct(string $apiUri = 'http://unoconv:3000', ?Client $client = null)
    {
        $this->apiUri = $apiUri;
        $this->client = $client ?? new Client();
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $options
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function request(string $method, string $path, array $options = []):ResponseInterface
    {
        return $this->client->request($method, $this->apiUri.$path, $options);
    }

    /**
     * List all supported formats.
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getFormats():array
    {
        return json_decode((string)$this->request('GET', '/unoconv/formats')->getBody(), true);
    }

    /**
     * Returns the Unoconv service's health.
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getHealth():array
    {
        return json_decode((string)$this->request('GET', '/healthz')->getBody(), true);
    }

    /**
     * Returns Unoconv versions and dependencies lookup.
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getVersions():array
    {
        return json_decode((string)$this->request('GET', '/unoconv/versions')->getBody(), true);
    }

    /**
     * Converts a stream
     *
     * @param StreamInterface $sourceFile
     * @param string $format
     * @return StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function convert(StreamInterface $sourceFile, string $format):StreamInterface
    {
        return $this->request('POST', '/unoconv/'.urlencode($format), ['multipart' => [[
            'name'     => 'file',
            'contents' => $sourceFile,
        ]]])->getBody();
    }
}