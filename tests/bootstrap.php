<?php
/**
 * Load files necessary to run test.
 */
require_once __DIR__ . '/../vendor/autoload.php';
\VCR\VCR::configure()->enableLibraryHooks([ 'curl' ]);
\VCR\VCR::turnOn();
define( 'Kshabazz\\Tests\\Slib\\FIXTURES_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'fixtures' );
// Writing below this line can cause headers to be sent before intended ?>