<?php
/**
 * Created by PhpStorm.
 * User: Khalifah
 * Date: 4/18/14
 * Time: 10:56 AM
 */
require_once __DIR__ . '/../vendor/autoload.php';
// configure PHP-VCR
\VCR\VCR::configure()->enableLibraryHooks(array('curl'));
\VCR\VCR::configure()->setCassettePath('tests/fixtures');
\VCR\VCR::turnOn();
// Writing below this line can cause headers to be sent before intended ?>