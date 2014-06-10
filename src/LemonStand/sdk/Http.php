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

/*
 * Copyright (c) 2012 Nate Good <me@nategood.com>
 *
 * Some portions of this code were forked from https://github.com/nategood/httpful
 *
 */

class Http
{
	const HEAD = 'HEAD';
	const GET = 'GET';
	const POST = 'POST';
	const PUT = 'PUT';
	const DELETE = 'DELETE';
	const PATCH = 'PATCH';
	const OPTIONS = 'OPTIONS';
	const TRACE = 'TRACE';

	/**
	 * @return array of HTTP method strings
	 */
	public static function allMethods () {
		return array(self::HEAD, self::GET, self::POST, self::PUT, self::DELETE, self::PATCH, self::OPTIONS, self::TRACE);
	}

	/**
	 * @return array of HTTP method strings
	 */
	public static function safeMethods () {
		return array(self::HEAD, self::GET, self::OPTIONS, self::TRACE);
	}

	/**
	 * @return bool
	 * @param string HTTP method
	 */
	public static function isSafeMethod ($method) {
		return in_array($method, self::safeMethods());
	}

	/**
	 * @return array list of (always) idempotent HTTP methods
	 */
	public static function idempotentMethods () {
		// Though it is possible to be idempotent, POST
		// is not guarunteed to be, and more often than
		// not, it is not.
		return array(self::HEAD, self::GET, self::PUT, self::DELETE, self::OPTIONS, self::TRACE, self::PATCH);
	}

	/**
	 * @return bool
	 * @param string HTTP method
	 */
	public static function isIdempotent ($method) {
		return in_array($method, self::safeidempotentMethodsMethods());
	}
}