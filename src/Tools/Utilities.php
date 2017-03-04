<?php namespace Kshabazz\Slib\Tools;
/**
 * Tools to help simplify repetitive task.
 * @copyright (c) 2013-2017 Khalifah K. Shabazz
 */

/**
 * Description of Functions
 */
trait Utilities
{
	/**
	 * Capture the output of an include statement.
	 * Note: Taken from PHP example of include function.
	 *
	 * @param string $pFilename Name of a PHP file to include.
	 * @return mixed
	 */
	function includeContents( $pFilename )
	{
	    $returnValue = false;

		if ( \is_file($pFilename) )
		{
			\ob_start();

			include $pFilename;

            $returnValue = \ob_get_clean();
		}

		return $returnValue;
	}

	/**
	 * Check if a variable is an array of length greater than 0.
	 *
	 * @param mixed $pVariable to be checked.
	 * @return bool TRUE is yes, false otherwise.
	 */
	function isArray( $pVariable )
	{
		return ( \is_array($pVariable) && \count($pVariable) > 0 );
	}

	/**
	 * Check if a variable is a string of length greater than 0.
	 *
	 * @param mixed $pVariable to be checked.
	 * @return bool TRUE is yes, false otherwise.
	 */
	function isString( $pVariable )
	{
		return ( \is_string($pVariable) && \strlen($pVariable) > 0 );
	}

	/**
	 * Load the attribute map from file.
	 *
	 * @param string $pFile attribute map file contents.
	 * @throw \Exception
	 * @return array
	 */
	function loadJsonAsArray( $pFile )
	{
		$contents = \file_get_contents( $pFile );
		$returnValue = \json_decode( $contents, true );

		if ( !$this->isArray($returnValue) )
		{
			$returnValue = [];
		}

		return $returnValue;
	}

	/**
	 * Random x elements from an array.
	 *
	 * @param array $pSource Source, array which to pull elements from.
	 * @param int $pQuantity Number of elements to retrieve from the array.
	 * @return array
	 */
	function randomElementsFromArray( $pSource, $pQuantity = 1 )
	{
		$returnAry = null;

		if ( isArray($pSource) ) {
			shuffle( $pSource );
			$slices = array_slice( $pSource, 0, $pQuantity );

			if ( isArray($slices) )
			{
				$returnAry = $slices[ 0 ];
			}
		}

		return $returnAry;
	}

	/**
	 * Save content to a file; but will also make the directory if it does not exists.
	 *
	 * @param string $pFileName path.
	 * @param string $pContent data to save in the file.
	 * @throws \Exception
	 * @return bool
	 */
	function saveFile( $pFileName, $pContent )
	{
		$directory = \dirname( $pFileName );

        if ( !is_dir($directory) ) {
			$madeDir = \mkdir( $directory, 0755, TRUE );
			if ( $madeDir === FALSE )
			{
				throw new \Exception( "mkdir: Unable make directory '{$directory}'." );
			}
		}

        // Save data to a file.
		$fileSaved = \file_put_contents( $pFileName, $pContent ); //, \LOCK_EX );

		if ( $fileSaved === FALSE )
		{
			throw new \Exception( "file_put_contents: Unable to save file '{$pFileName}'." );
		}

		return $fileSaved;
	}

	/**
	 * Generate an array of random numbers within a specified range.
	 * @credit Taken from a Stack Overflow answer:
	 *  http://stackoverflow.com/questions/5612656/generating-unique-random-numbers-within-a-range-php
	 *
	 * @param int $pMin bottom range.
	 * @param int $pMax top range.
	 * @param int $pQuantity number of random elements to return.
	 * @throws \Exception
	 * @return array of random numbers.
	 */
	function UniqueRandomNumbersWithinRange( $pMin, $pMax, $pQuantity )
	{
		$numbersAry = range( $pMin, $pMax );

        if ( count($numbersAry) < $pQuantity)
		{
			throw new \Exception( 'Quantity of random numbers requested has to be <= ((max - min) + 1).' );
		}

        shuffle( $numbersAry );

		return array_slice( $numbersAry, 0, $pQuantity );
	}

	/**
	 * Print the debug backtrace in the following line format.
	 * Format: [className::]functionName( parameters )
	 */
	function print_debug_trace()
	{
		$backtrace = \debug_backtrace();

		foreach ( $backtrace as $trace )
		{
			$functionName = $traceStr = $trace[ 'function' ];
			$className = empty( $trace['class'] ) ? '' : $traceStr = $trace[ 'class' ] . '::' . $traceStr;
			$args = \json_encode( $trace['args'] );
			if ( \count($trace['args']) > 0)
			{
				$parameters = '( ' . substr($args, 1, -1) . ' )';
			}
			else
			{
				$parameters = '()';
			}
			echo "\n" . $className . $functionName . $parameters;
		}
		echo "\n";
	}
}
