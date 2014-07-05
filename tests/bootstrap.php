<?php
/**
 * Load files necessary to run test.
 */
require_once __DIR__ . '/../vendor/autoload.php';
// configure PHP-VCR
$fixturesPath = realpath(__DIR__ . DIRECTORY_SEPARATOR . 'fixtures');
\VCR\VCR::configure()->setCassettePath($fixturesPath);
// Writing below this line can cause headers to be sent before intended ?>