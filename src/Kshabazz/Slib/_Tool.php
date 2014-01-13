<?php namespace Kshabazz\Slib;
/**
 * Tools to help simplify repetitive task.
 * Diablo 3 Assistant License is under The MIT License (MIT)
 * [OSI Approved License]. Please read LICENSE.txt, included with this
 * software for the full licensing information. If no LICENSE.txt accompanied
 * this software, then no license is granted.
 *
 * @package kshabazz\d3a\Controller
 * @copyright (c) 2012-2013 Khalifah K. Shabazz
 */

/**
 * Convert a dash separated string to lower/upper camel case.
 * Works great on pretty URLs
 *
 * @param string $pString dash separate word.
 * @param bool $upperCaseFirst switch lower/upper mode of first letter.
 * @return string
 */
function camelCase( $pString, $upperCaseFirst = FALSE )
{
	if ( $upperCaseFirst )
	{
		$regEx = '/(?:^|-)(.?)/';
	}
	else
	{
		$regEx = '/(?:-)(.?)/';
	}
	$filter = function ( $p )
	{
		return strtoupper( $p[1] );
	};
	return preg_replace_callback( $regEx, $filter, $pString );
}

	/**
	 * Check the PHP version, and throws an error if it does not meet the minimum version.
	 *
	 * @param int $pMajor Required major version.
	 * @param int $pMinor If set, then the required minor version.
	 * @param int $pRelease If set, then the required release version.
	 * @throws \Exception
	 * @return bool TRUE indicates requirements were met.
	 */
	function checkPhpVersion( $pMajor, $pMinor = NULL, $pRelease = NULL )
	{
		$requirementsMet = TRUE;
		$phpVersion = phpversion();
		$version = explode( '.', phpversion() );
		// fix minor version with tags, ex: 5.6.0-dev
		$version[2] = str_replace( '-dev', '', $version[2] );
		// Check the major version.
		if ( $version[0] < $pMajor )
		{
			$requirementsMet = FALSE;
		}
		else if ( $version[0] == $pMajor )
		{
			// Check the minor version if set.
			if ( is_int($pMinor) && $version[1] < $pMinor )
			{
				$requirementsMet = FALSE;
			}
			else if ( $requirementsMet && is_int($pMinor) && $version[1] == $pMinor )
			{
				// Check the release version if set.
				if ( is_int($pRelease) && $version[2] < $pRelease )
				{
					$requirementsMet = FALSE;
				}
			}
		}
		// Throw the error when the required version is not met.
		if ( !$requirementsMet )
		{
			$versionString = "{$pMajor}.{$pMinor}.{$pRelease}";
			throw new \Exception( "Your PHP version is '{$phpVersion}'. The minimum required PHP version is '{$versionString}'. You'll need to upgrade in order to use this application." );
		}

		return $requirementsMet;
	}

	/**
	 * Turn a string into camel-cased word.
	 *
	 * @param string $pString to convert to a class name.
	 * @return string.
	 */
	function convertToClassName( $pString )
	{
		// strip off the forward slash and extension.
		$className = basename( $pString, '.php' );
		// Camel Case any words left.
		$className = camelCase( $className, TRUE );
		// remove any chars unqualified for a class name.
		$className = str_replace( '-', '', $className );
		return $className;
	}

	/**
	 * Get content between <body></body> tags.
	 *
	 * @param string $pHtml
	 * @return array
	 */
	function getHtmlInnerBody( $pHtml )
	{
		$returnValue = NULL;
		if ( gettype($pHtml) === "string" )
		{
			$start = strpos( $pHtml, "<body" );
			$start = strpos( $pHtml, '>', $start + 5 ) + 1;
			$end = strpos( $pHtml, "</body>", $start ) - $start;
			$returnValue = substr( $pHtml, $start, $end );
		}
		return $returnValue;
	}

	/**
	 * Capture the output of an include statement.
	 * Note: Taken from PHP example of include function.
	 *
	 * @param string $pFilename Name of a PHP file to include.
	 * @return mixed
	 */
	function includeContents( $pFilename )
	{
		if ( is_file($pFilename) )
		{
			ob_start();
			include $pFilename;
			return ob_get_clean();
		}
		return FALSE;
	}

	/**
	 * Check if a variable is an array of length greater than 0.
	 *
	 * @param mixed $pVariable to be checked.
	 * @return bool TRUE is yes, false otherwise.
	 */
	function isArray( $pVariable )
	{
		return ( is_array($pVariable) && count($pVariable) > 0 );
	}

	/**
	 * Check if a variable is a string of length greater than 0.
	 *
	 * @param mixed $pVariable to be checked.
	 * @return bool TRUE is yes, false otherwise.
	 */
	function isString( $pVariable )
	{
		return ( is_string($pVariable) && strlen($pVariable) > 0 );
	}

	/**
	 * Load the attribute map from file.
	 *
	 * @param string $pFile attribute map file contents.
	 * @throw \Exception
	 * @return array
	 */
	function loadJsonFile( $pFile )
	{
		$contents = \file_get_contents( $pFile );
		$returnValue = \json_decode( $contents, TRUE );
		if ( !isArray($returnValue) )
		{
			$returnValue = [];
		}
		return $returnValue;
	}

	/**
	 * Check if a variable is a string of length greater than 0.
	 *
	 * @param \Exception $pError
	 * @param string $pDevHintMessage message the developer will see. Usually the error returned from PHP.
	 * @return bool TRUE is yes, false otherwise.
	 */
	function logError( \Exception $pError, $pDevHintMessage )
	{
		$logErrorMessage = $pError->getMessage();
		$logErrorMessage .= "\n". sprintf( $pDevHintMessage, $pError->getFile(), $pError->getLine() );
		$logErrorMessage .= "\n". print_r( $pError->getTrace(), TRUE );
		error_log( $logErrorMessage );
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
		$returnAry = NULL;
		if ( isArray($pSource) )
		{
			shuffle( $pSource );
			$slices = array_slice( $pSource, 0, $pQuantity );
			//
			if ( isArray($slices) )
			{
				$returnAry = $slices[ 0 ];
			}
		}
		return $returnAry;
	}

	/**
	 * Save a file like file_put_contents, but with additional error checking.
	 *
	 * @param string $pFileName path.
	 * @param string $pContent data to save in the file.
	 * @throws \Exception
	 * @return mixed
	 */
	function saveFile( $pFileName, $pContent )
	{
		$fileSaved = FALSE;
		try
		{
			$directory = dirname( $pFileName );
			if ( !is_dir($directory) )
			{
				$madeDir = mkdir( $directory, 0755, TRUE );
				if ( $madeDir === FALSE )
				{
					throw new \Exception( "mkdir: Unable make directory '{$directory}'." );
				}
			}
			// Save data to a file.
			$fileSaved = file_put_contents( $pFileName, $pContent, LOCK_EX );
			if ( $fileSaved === FALSE )
			{
				throw new \Exception( "file_put_contents: Unable to save file '{$pFileName}'." );
			}
		}
		catch ( \Exception $pError )
		{
			throw new \Exception( "There is a problem in %s on line %s." );
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
	 * @param int $pQuantity number of randoms to return.
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

// Writing below this line can cause headers to be sent before intended ?>