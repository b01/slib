<?php namespace Kshabazz\Slib;
/**
 * Generic methods for retrieving data from a database.
 *
 */

/**
 * Class SqlClient
 *
 * @package Kshabazz\Slib
 */
class SqlClient
{
	protected
		/** @var \PDO Database PDO handle. */
		$pdo,
		/** @var string Client IP address. */
		$ipAddress;

	/**
	 * Constructor - Get an SQL connection object.
	 */
	public function __construct( \PDO $pPdo, $pIpAddress = NULL )
	{
		$this->ipAddress = $pIpAddress;
		$this->pdo = $pPdo;
	}

	/**
	 * Destructor
	 */
	public function __destruct()
	{
		// Close the DB connection.
		$this->pdo = null;
		unset( $this->ipAddress );
	}

	/**
	* Get currently set user IP address.
	* @return {string|null} IP address or null.
	*/
	public function ipAddress()
	{
		return $this->ipAddress;
	}

	/**
	 * Run a query statement as a prepared PDO statement. Optionally returns the result.
	 *
	 * @param string $pStmt PDO statement
	 * @param bool $pReturnResults TRUE to return the results, or FALSE not to.
	 * @throws \Exception
	 * @return mixed
	 */
	public function pdoQuery( $pStmt, $pReturnResults = TRUE )
	{
		$returnValue = NULL;
		try
		{
			// Call the database routine
            $returnValue = $pStmt->execute();

			if ( $returnValue && $pReturnResults )
			{
				// Fetch all rows into an array.
				$rows = $pStmt->fetchAll( \PDO::FETCH_ASSOC );
				if ( isArray($rows) )
				{
					$returnValue = $rows;
				}
			}
			$pStmt->closeCursor();
		}
		catch ( \Exception $pError )
		{
			// Friendly message to the user.
			throw new \Exception( 'A PDO Error has occurred: ' . $pError->getMessage() );
		}

		return $returnValue;
	}

	/**
	 * Run a prepared SQL statement with an arbitrary number of values.
	 *
	 * Convenience method for building a parametrized statement from a string.
	 * This is done in a generic way. so that any statement can
	 * be parametrized in a generic way.
	 *
	 * @param string $pSqlQuery
	 *  example: 'INSERT INTO `test` ( `column1`, ...) VALUES( `:column1Val`, ... );'
	 * @param array $pBindings 2D-array.
	 *  example: [
	 *		'column1Val' => [ 'test', \PDO::PARAM_STR ],
	 *		'column2Val' => [ 'test2', \PDO::PARAM_STR ],
	 *      ...
	 *	]
	 * @param bool $pReturnResults
	 * @throws \Exception
	 * @return bool Indication of success or failure.
	 */
	public function pdoQueryBind( $pSqlQuery, array $pBindings = NULL, $pReturnResults = TRUE )
	{
		$returnValue = NULL;
		try
		{
			// Bind values to the prepared statement.
			$stmt = $this->pdo->prepare( $pSqlQuery );
			// has to be an array with at least on value.
			if ( isArray($pBindings) )
			{
				foreach ( $pBindings as $parameterName => $data )
				{
					$stmt->bindValue( $parameterName, $data[0], $data[1] );
				}
			}
			// Run the query
			$returnValue = $this->pdoQuery( $stmt, $pReturnResults );
		}
		catch ( \Exception $pError )
		{
			throw new \Exception( 'A PDO Error has occurred.', 500, $pError );
		}
		return $returnValue;
	}

	/**
	 * Perform a simple SELECT that does NOT have any parameters.
	 *
	 * @param string $pSelectStatement
	 * @return mixed
	 * @throws \Exception
	 */
	public function select( $pSelectStatement )
	{
		$returnValue = NULL;
		try
		{
			// Set the select.
			$stmt = $this->pdo->prepare( $pSelectStatement );
			// Call the database routine
			$stmt->execute();
			// Fetch all rows into an array.
			$rows = $stmt->fetchAll( \PDO::FETCH_ASSOC );
			if ( isArray($rows) )
			{
				$returnValue = $rows;
			}
			$stmt->closeCursor();
		}
		catch ( \Exception $pError )
		{
			$message = \sprintf(
				'Select statement failed "%s" in %s on line %s',
				$pSelectStatement,
				$pError->getFile(),
				$pError->getLine()
			);
			throw new \Exception( $message, 501, $pError );
		}
		return $returnValue;
	}
}
?>