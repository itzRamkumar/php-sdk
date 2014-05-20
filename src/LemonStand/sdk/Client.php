<?php namespace LemonStand\sdk;

/**
 * @author LemonStand <chris@lemonstand.com>
 */
class Client
{
	public $protocol = "http://";
	public $base = null;
	public $version = 'v2';
	private $methods = array('GET', 'POST', 'PUT', 'DELETE');

	function __construct($options) {
		$this->shop = stripslashes($options['shop']);
		$this->key = $options['key'];
		$this->secret = $options['secret'];
		$this->token = $options['token'];
		$this->base = $this->protocol . stripslashes($this->shop) . "/api/" . $this->version;
	}

	function __call ($name, $arguments) {

	}

	public function get ($path, $params = []) {
		$options = array(
			'uri' => $this->base . $path,
			'method' => Http::GET,
			'headers' => array(
				'Content-Type' => 'Application/JSON',
				'Authorization' => $this->token
			)
		);

		$req = new \LemonStand\sdk\Request($options);
		$res = $req->send();

		$data = array(
			'body' => (array) json_decode($res[0], true), 
			'headers' => (array) $res[1],
			'status' => (int) $res[2]
		);

		return $data;
	}
}