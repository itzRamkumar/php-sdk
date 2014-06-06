<?php namespace LemonStand\sdk;


/*
 * Copyright 2014 LemonStand eCommerce Inc.
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

class Request
{
	public $uri;
	public $method;
	public $headers = array();
	public $options = array();
	public $strict_ssl = false;
	public $gzip = true;
	public $content_type;
	public $username;
	public $password;
	public $access_key;
	public $payload;
	public $max_redirects;
	public $follow_redirects = false;

	public function __construct ($attrs = null) {
		if (!is_array($attrs)) {
			return;
		}

		foreach ($attrs as $attr => $value) {
			$this->$attr = $value;
		}
	}

	public function send () {

		$curl = curl_init($this->uri);

		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $this->method);

		if ($this->method === Http::HEAD) {
			curl_setopt($ch, CURLOPT_NOBODY, true);
		}

		if (isset($this->payload)) {
			curl_setopt($curl, CURLOPT_POSTFIELDS, $this->payload);
		}

		if (is_array($this->headers)) {
			$headers = array();

			if (!isset($this->headers['User-Agent'])) {
				$headers[] = $this->buildUserAgent();
			}

			foreach ($this->headers as $k => $v) {
				$headers[] = "$k: $v";
			}

			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		}

		if ($this->follow_redirects) {
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_MAXREDIRS, $this->max_redirects);
		}

		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $this->strict_ssl);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, true);

		if ($this->gzip) {
			curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
		}

		foreach ($this->options as $key => $var) {
			curl_setopt($curl, $key, $var);
		}

		if (isset($this->client_cert) && isset($this->client_key)) {
			if (!file_exists($this->client_key)){
				throw new \Exception('Could not read Client Key');
			}

			if (!file_exists($this->client_cert)) {
				throw new \Exception('Could not read Client Certificate');
			}

			curl_setopt($ch, CURLOPT_SSLCERTTYPE,   $this->client_encoding);
			curl_setopt($ch, CURLOPT_SSLKEYTYPE,    $this->client_encoding);
			curl_setopt($ch, CURLOPT_SSLCERT,       $this->client_cert);
			curl_setopt($ch, CURLOPT_SSLKEY,        $this->client_key);
			curl_setopt($ch, CURLOPT_SSLKEYPASSWD,  $this->client_passphrase);
		}

		$response = curl_exec($curl);

		if ($response === false) {
			throw new \Exception(curl_error($curl));
		}

		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$body = substr($response, $header_size);
		$response_header_string = substr($response, 0, $header_size);
		$headers = $this->getHttpResponseHeaders($response_header_string);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		return array($body, $headers, $code);
	}

	public function buildUserAgent () {
		$user_agent = 'User-Agent: LemonStand/ (cURL/';
		$curl = \curl_version();

		if (isset($curl['version'])) {
			$user_agent .= $curl['version'];
		} else {
			$user_agent .= '?.?.?';
		}

		$user_agent .= ' PHP/'. PHP_VERSION . ' (' . PHP_OS . ')';

		if (isset($_SERVER['SERVER_SOFTWARE'])) {
			$user_agent .= ' ' . \preg_replace('~PHP/[\d\.]+~U', '', $_SERVER['SERVER_SOFTWARE']);
		} else {
			if (isset($_SERVER['TERM_PROGRAM'])) {
				$user_agent .= " {$_SERVER['TERM_PROGRAM']}";
			}

			if (isset($_SERVER['TERM_PROGRAM_VERSION'])) {
				$user_agent .= "/{$_SERVER['TERM_PROGRAM_VERSION']}";
			}
		}

		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$user_agent .= " {$_SERVER['HTTP_USER_AGENT']}";
		}

		$user_agent .= ')';

		return $user_agent;
	}

	public function getHttpResponseHeaders ($raw_headers) {
		if (is_array($raw_headers)) {
			return $this->parseArrayHeaders($raw_headers);
		} else {
			return $this->parseStringHeaders($raw_headers);
		}
	}

	private function parseArrayHeaders ($raw_headers) {
		$header_count = count($raw_headers);
		$headers = array();

		for ($i = 0; $i < $header_count; $i++) {
			$header = $raw_headers[$i];
			// Times will have colons in - so we just want the first match.
			$header_parts = explode(': ', $header, 2);
			if (count($header_parts) == 2) {
				$headers[$header_parts[0]] = $header_parts[1];
			}
		}

		return $headers;
	}

	private function parseStringHeaders ($raw_headers) {
		$headers = array();
		$response_header_lines = explode("\r\n", $raw_headers);

		foreach ($response_header_lines as $header_line) {
			if ($header_line && strpos($header_line, ':') !== false) {
				list($header, $value) = explode(': ', $header_line, 2);
				$header = strtolower($header);

				if (isset($response_headers[$header])) {
					$headers[$header] .= "\n" . $value;
				} else {
					$headers[$header] = $value;
				}
			}
		}
		
		return $headers;
	}
}