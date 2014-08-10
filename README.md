##About

This is a library with some tools to aid in performing common task.

##Examples:

### How to use the Request object:

```PHP
<?php
// Initialize a request object.
$request = new \Kshabazz\Slib\Request();

// Set headers
$request->setHeaders([
    'Content-Type' => 'application/json; charset=utf-8'
]);

print_r( $request->headers() );

// Output currently set headers:
// Please note that header keys will be converted to lowercase.
(
    content-type => application/json; charset=utf-8
)
?>
```

###Functions

```php
<?php
echo \Kshabazz\Slib\camelCase( 'test-me' );
echo \Kshabazz\Slib\camelCase( 'test-me', TRUE );

// Output:
TestMe
testMe
?>
```