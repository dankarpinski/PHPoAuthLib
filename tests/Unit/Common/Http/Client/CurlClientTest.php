<?php

namespace OAuthTest\Unit\Common\Http\Client;

use OAuth\Common\Http\Client\CurlClient;

class CurlClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testConstructCorrectInstance()
    {
        $client = new CurlClient();

        $this->assertInstanceOf('\\OAuth\\Common\\Http\\Client\\AbstractClient', $client);
    }

    /**
     * @covers OAuth\Common\Http\Client\CurlClient::setForceSSL3
     */
    public function testSetForceSSL3()
    {
        $client = new CurlClient();

        $this->assertInstanceOf('\\OAuth\\Common\\Http\\Client\\CurlClient', $client->setForceSSL3(true));
    }

    /**
     * @covers OAuth\Common\Http\Client\CurlClient::retrieveResponse
     */
    public function testRetrieveResponseThrowsExceptionOnGetRequestWithBody()
    {
        $this->setExpectedException('\\InvalidArgumentException');

        $client = new CurlClient();

        $client->retrieveResponse(
            $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface'),
            'foo',
            array(),
            'GET'
        );
    }

    /**
     * @covers OAuth\Common\Http\Client\CurlClient::retrieveResponse
     */
    public function testRetrieveResponseThrowsExceptionOnGetRequestWithBodyMethodConvertedToUpper()
    {
        $this->setExpectedException('\\InvalidArgumentException');

        $client = new CurlClient();

        $client->retrieveResponse(
            $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface'),
            'foo',
            array(),
            'get'
        );
    }

    /**
     * @covers OAuth\Common\Http\Client\CurlClient::retrieveResponse
     */
    public function testRetrieveResponseWithCustomContentType()
    {
        $endPoint = $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('httpbin.org'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('http://httpbin.org/get'));

        $client = new CurlClient();

        $response = $client->retrieveResponse(
            $endPoint,
            '',
            array('Content-type' => 'foo/bar'),
            'get'
        );

        $response = json_decode($response, true);

        $this->assertSame('foo/bar', $response['headers']['Content-Type']);
    }

    /**
     * @covers OAuth\Common\Http\Client\CurlClient::retrieveResponse
     */
    public function testRetrieveResponseWithFormUrlEncodedContentType()
    {
        $endPoint = $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('httpbin.org'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('http://httpbin.org/post'));

        $client = new CurlClient();

        $response = $client->retrieveResponse(
            $endPoint,
            ['foo' => 'bar', 'baz' => 'fab'],
            array(),
            'POST'
        );

        $response = json_decode($response, true);

        $this->assertSame('application/x-www-form-urlencoded', $response['headers']['Content-Type']);
        $this->assertEquals(['foo' => 'bar', 'baz' => 'fab'], $response['form']);
    }

    /**
     * @covers OAuth\Common\Http\Client\CurlClient::retrieveResponse
     */
    public function testRetrieveResponseHost()
    {
        $endPoint = $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('httpbin.org'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('http://httpbin.org/post'));

        $client = new CurlClient();

        $response = $client->retrieveResponse(
            $endPoint,
            ['foo' => 'bar', 'baz' => 'fab'],
            array(),
            'POST'
        );

        $response = json_decode($response, true);

        $this->assertSame('httpbin.org', $response['headers']['Host']);
    }

    /**
     * @covers OAuth\Common\Http\Client\CurlClient::retrieveResponse
     */
    public function testRetrieveResponsePostRequestWithRequestBodyAsString()
    {
        $endPoint = $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('httpbin.org'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('http://httpbin.org/post'));

        $client = new CurlClient();

        $response = $client->retrieveResponse(
            $endPoint,
            'foo',
            array(),
            'POST'
        );

        $response = json_decode($response, true);

        $this->assertSame('foo', $response['data']);
    }

    /**
     * @covers OAuth\Common\Http\Client\CurlClient::retrieveResponse
     */
    public function testRetrieveResponsePutRequestWithRequestBodyAsString()
    {
        $endPoint = $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('httpbin.org'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('http://httpbin.org/put'));

        $client = new CurlClient();

        $response = $client->retrieveResponse(
            $endPoint,
            'foo',
            array(),
            'PUT'
        );

        $response = json_decode($response, true);

        $this->assertSame('foo', $response['data']);
    }

    /**
     * @covers OAuth\Common\Http\Client\CurlClient::retrieveResponse
     */
    public function testRetrieveResponsePutRequestWithRequestBodyAsStringNoRedirects()
    {
        $endPoint = $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('httpbin.org'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('http://httpbin.org/put'));

        $client = new CurlClient();

        $client->setMaxRedirects(0);

        $response = $client->retrieveResponse(
            $endPoint,
            'foo',
            array(),
            'PUT'
        );

        $response = json_decode($response, true);

        $this->assertSame('foo', $response['data']);
    }

    /**
     * @covers OAuth\Common\Http\Client\CurlClient::retrieveResponse
     */
    public function testRetrieveResponseWithForcedSsl3()
    {
        $this->setExpectedException(
            '\\OAuth\\Common\\Http\\Exception\\TokenResponseException',
            "cURL Error # 60: SSL certificate problem, verify that the CA cert is OK. Details:\nerror:14090086:SSL routines:SSL3_GET_SERVER_CERTIFICATE:certificate verify failed"
        );

        $endPoint = $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('httpbin.org'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('https://httpbin.org/get'));

        $client = new CurlClient();

        $client->setForceSSL3(true);

        $response = $client->retrieveResponse(
            $endPoint,
            '',
            array('Content-type' => 'foo/bar'),
            'get'
        );

        $response = json_decode($response, true);

        $this->assertSame('foo/bar', $response['headers']['Content-Type']);
    }
}