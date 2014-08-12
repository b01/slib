<?php
/**
 * Load files necessary to run test.
 */
require_once __DIR__
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'vendor'
	. DIRECTORY_SEPARATOR . 'autoload.php';

// Set fixture path constant.
define( 'Kshabazz\\Tests\\Slib\\FIXTURES_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'fixtures' );

// Writing below this line can cause headers to be sent before intended ?>