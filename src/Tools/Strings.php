<?php namespace Kshabazz\Slib\Tools;
/**
 * Tools to help simplify repetitive task.
 * @copyright (c) 2013-2017 Khalifah Khalil Shabazz
 */

/**
 * Miscellaneous traits for dealing with strings.
 */
trait Strings
{
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
				if ( \is_int($pRelease) && $version[2] < $pRelease )
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
		// remove any chars unqualified for a class name.
		$className = \str_replace( '-', '', $pString );
		// strip off the forward slash and extension.
		$className = \basename( $className, '.php' );
		// Camel Case any words left.
		$className = camelCase( $className, TRUE );

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
}