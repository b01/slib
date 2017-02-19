<?php namespace Kshabazz\Slib\Tools;

/**
 *
 */
trait Configuration
{
    /**
     *
     * @return array
     */
	protected function loadConfig()
	{
		// Load setting from config.
		$configJson = \file_get_contents(
			PROJECT_DIR
			. DIRECTORY_SEPARATOR . 'config'
			. DIRECTORY_SEPARATOR . 'settings.json'
		);

		return \json_decode( $configJson, TRUE );
	}
}
