<?php
namespace RedditApiClient\Test\Client\Subscriber;

use Guzzle\Common\Event;
use Mockery as m;
use PHPUnit_Framework_TestCase;
use RedditApiClient\Client\Subscriber\Session;

class SessionTest extends PHPUnit_Framework_TestCase
{
	private $session;
	private $event;
	private $request;

	public function setUp()
	{
		parent::setUp();
		$this->session = new Session;
		$this->event = new Event;
		$this->client = m::mock('RedditApiClient\Client');
		$this->request = m::mock('Guzzle\Http\Message\Request');
		$this->response = m::mock('Guzzle\Http\Message\Response');
		$this->event['request'] = $this->request;
		$this->event['response'] = $this->response;
	}

	/**
	 * @test
	 */
	public function onRequestBeforeSend()
	{
		$this->session->onRequestBeforeSend($this->event);
	}

	/**
	 * @test
	 */
	public function onRequestAfterSend()
	{
		$this->request
			->shouldReceive('getPath')
			->andReturn('/api/login/asdfg')
			->once();

		$this->response
			->shouldReceive('getBody')
			->andReturn('{
				"json":  {
					"errors":  [],
						"data":  {
							"modhash": "e17aznbup819e98e407734a18ef5a38e4b808dcd3c307ae919",
							"cookie": "23636817,2013-11-25T16:21:14,2ab7f75beab690d42276b3d747d587c7a2bc0e27"
						}
					}
				}
			')
			->once();

		$this->request
			->shouldReceive('getClient')
			->andReturn($this->client)
			->once();

		$this->client
			->shouldReceive('setModHash')
			->with('e17aznbup819e98e407734a18ef5a38e4b808dcd3c307ae919')
			->once();

		$this->session->onRequestAfterSend($this->event);
	}
}
