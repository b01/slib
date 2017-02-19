<?php namespace Kshabazz\Slib\Tools;

/**
 * Description of Log
 */
class Log
{
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
}
