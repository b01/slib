## About

[![CircleCI](https://circleci.com/gh/b01/slib.svg?style=svg)](https://circleci.com/gh/b01/slib)

This is a library with some tools to aid in performing common task.

## Examples:

### How to use the HttpClient object to make an HTTP Request

```php
<?php

use \Kshabazz\Slib\HttpClient;

// Initialize a new HTTP client object.
$httpClient = new HttpClient();
$url = 'http://www.example.com';

// Set headers
// Please note that the same header can be set multiple times, just like in the HTTP RFC.
$httpClient->setHeaders([
    'Content-Type: application/json; charset=utf-8',
    'Custom-Header: custom value',
]);

// Make a request and check the response.
$httpClient->send( $url );
$responseCode = $http->responseCode();
if ( $responseCode === 200 ) {
    echo $httpClient->responseBody();

} else { // DEBUG
    // Show what was sent in the last request.
    var_dump( $httpClient->lastRequest() );
    
    // Show the response headers.
    var_dump( $httpClient->responseHeaders() );
}
?>
```

### Functions

```php
<?php
echo \Kshabazz\Slib\camelCase( 'test-me' );
// Output:
TestMe

echo \Kshabazz\Slib\camelCase( 'test-me', TRUE );
// Output:
testMe
?>
```