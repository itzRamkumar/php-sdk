<?php namespace LemonStand\sdk;

/*
 * Copyright (c) 2014 LemonStand eCommerce Inc.
 * @author LemonStand <chris@lemonstand.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

class Client extends Http
{
	public $protocol = "https://";
	public $base = null;
	public $shop = null;
	public $version = 'v2';

	function __construct ($attrs = null) {
		if (!is_array($attrs)) {
			return;
		}

		foreach ($attrs as $attr => $value) {
			$this->$attr = $value;
		}

		// Set the base URI of the API
		$this->base = $this->protocol . stripslashes($this->shop) . "/api/" . $this->version;
	}

	public function __call ($method, $arguments) {
		if (in_array(strtoupper($method), $this->allMethods())) {
			list($path, $data, $parameters) = array_pad($arguments, 3, null);
			return $this->request($method, $path, $data, $parameters);
		}
		throw new \BadMethodCallException(strtoupper($method) . ' is not supported');
	}

	public function setBase ($base) {
		$this->base = $base;
	}

	public function getBase ($base) {
		return $this->base;
	}

	public function getProtocol ($protocol) {
		return $this->protocol;
	}

	public function setProtocol ($protocol) {
		if (in_array($protocol, array('http://', 'https://'), true)) {
			$this->protocol = $protocol;
		}
	}

	private function buildErrorMessage ($error) {
		$type = gettype($error["message"]);

		if ($type == "array") {
			$messages = array();
			foreach ($error["message"] as $f => $m) {
				$etype = gettype($m);
				if ($etype == "array") {
					foreach ($m as $e) {
						$messages[] = $f . ": " . $e;
					}
				} elseif ($etype == "string") {
					$messages[] = $f . ": " . $m;
				}

			}
			$message = implode(' ', $messages);
		} elseif ($type == "string") {
			$message = $error["message"];
		}

		return $message;

	}

	private function request ($method, $path, $data = null, $params = null) {
		$options = array(
			'uri' => $this->base . $path,
			'method' => strtoupper($method),
			'payload' => isset($data) ? json_encode($data) : null,
			'headers' => array(
				'Content-Type' => 'application/json',
				'Authorization' => $this->token
			)
		);

		$req = new Request($options);

		try {
			$res = $req->send();
			$body = (array) json_decode($res[0], true);
			$headers = (array) $res[1];
			$status = (int) $res[2];
			$meta = (array_key_exists('meta', $body)) ? $body['meta'] : null;

			$data = array(
				'data' => (array_key_exists('data', $body)) ? $body['data'] : null,
				'error' => (array_key_exists('error', $body)) ? array(
					"raw" => $body['error'], 
					"message" => $this->buildErrorMessage($body['error'])
				) : null,
				'headers' => $headers,
				'status' => $status,
				'success' => (!is_null($meta)) ? $meta['success'] : false
			);

		} catch (\Exception $e) {
			$data = array(
				'data' => null,
				'error' => array(
					"raw" => null, 
					"message" => $e->getMessage()
				),
				'headers' => null,
				'status' => null,
				'success' => false
			);
		}

		return $data;
	}
}