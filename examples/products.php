<?php

/*
 * Copyright 2014 LemonStand eCommerce Inc.
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

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../src/LemonStand/sdk/Client.php';
require_once '../src/LemonStand/sdk/Request.php';
require_once '../src/LemonStand/sdk/Http.php';

$client = new \LemonStand\sdk\Client(array(
	'shop' => 'store9.local.io',
	'key' => 'g6WwrZFYkxowur9VxNxuME5ZamgxXpqMYIRsfhLz',
	'secret' => 'g6WwrZFYkxowur9VxNxuME5ZamgxXpqMYIRsfhLz',
	'token' => 'g6WwrZFYkxowur9VxNxuME5ZamgxXpqMYIRsfhLz'
));

$product = $client->get('/shop/products');
echo var_dump($product['body']);