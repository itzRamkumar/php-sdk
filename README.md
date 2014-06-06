Official PHP SDK
===

https://travis-ci.org/lemonstand/php-sdk.svg?branch=master

The [LemonStand](https://lemonstand.com) php-sdk is a simple to use interface for the API to help you get off the ground quickly and efficiently.

Authorization
---

Before you can use the API you will need to create credentials from your store backend. You can do this as follows:

1. Login into the `/backend` of your store.
2. Navigate to the **API** section under the **Settings** menu tab.
3. Add a new API key and do not share the `secret` or `access token` with anyone.

 
Installation
---

Add this line to your composer.json file:

```
{
  "require": {
    "lemonstand/php-sdk": "dev-master"
  }
}
```

Requirements 
---

- PHP >=5.3 with the cURL extension installed

Usage 
---

First, you need to  create a client.

```php
$config = array(
    'shop' => 'store.lemonstand.com',
    'key' => 'hyfdNt0buc1ENxfec06YOGJQHh8bwqb3dMuBHMXI',
    'secret' => 'hyfdNt0buc1ENxfec06YOGJQHh8bwqb3dMuBHMXI',
    'token' => 'hyfdNt0buc1ENxfec06YOGJQHh8bwqb3dMuBHMXI'
);

$client= new \LemonStand\sdk\Client($config);
```

Then you can call specific endpoints of the API.

```
$products = $client->get('/products');
```

To send data to the API, pass a second argument as an array.

```php
$data = array(
    "in_stock_amount" => 500,
    "is_on_sale" => 1,
    "sale_price_or_discount" => 39.99
);

$res = $client->patch('/product/1', $data);

// Check to see if the request returned successfully
if (!$res['success']) {
        throw new \Exception($res["error"]["message"]);
}

echo var_dump($res["body"]);
```

###Response 
Each request returns a response array with the following data structure:

```php
array(
	"data" => array,
	"headers" => array,
	"status" => int,
	"success" => bool,
	"error" => array(
		"raw" => array, 
		"message" => string
	),
);
```

`data` - This is the body of the response. If you made a request to a single resource the output will be an array. If you made a request to a collection this will be an array of array's.  
`headers` - Contains an associative array of the headers returned with the response.  
`status` - An integer containing the response code. Eg. `200`.  
`success` - A boolean value to indicate whether the request was successfull or not. The possible values are `true` or `false`.  
`error` - On a successful request this will be `null`. Otherwise the `raw` value will contain the error object sent from the server, and the `message` will contain a human readable `string`. If the `success` of a request is `false` you could display the `error["message"]` to the end user.  

### Available Methods

- `get`  
- `put`  
- `post`  
- `patch`  
- `head`  
- `delete`  

License
---

[Apache 2.0](https://github.com/lemonstand/php-sdk/blob/master/LICENSE)