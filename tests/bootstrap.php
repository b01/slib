<?php
/**
 * Load files necessary to run test.
 */
require_once __DIR__ . '/../vendor/autoload.php';
\VCR\VCR::configure()->enableLibraryHooks([ 'curl' ]);
\VCR\VCR::turnOn();
// Writing below this line can cause headers to be sent before intended ?>