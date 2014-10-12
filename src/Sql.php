<?php namespace kshabazz\slib;
/**
 * Generic methods for retrieving data from a database.
 *
 */

class Sql
{
	protected
		$pdoh,
		$ipAddress;

	/**
	 * Constructor - Get an SQL connection object.
	 */
	public function __construct( \PDO $pPdo, $pIpAddress = NULL )
	{
		$this->ipAddress = $pIpAddress;
		$this->pdoh = $pPdo;
	}

	/**
	 * Destructor
	 */
	public function __destruct()
	{
		// close the DB connection.
		$this->pdoh = null;
		unset(
			$this->ipAddress
		);
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
	 * Get the PDO object.
	 */
	public function pdo()
	{
		return $this->pdoh;
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
		    logError( $pError, 'Sql::pdoQuery: Failed to run PDO statement on\n\tin %s on line %s",' );
			// Friendly message to the user.
			throw new \Exception( 'A PDO Error has occurred.' );
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
			$stmt = $this->pdoh->prepare( $pSqlQuery );
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
			logError(
				$pError,
				"Bad query {$pSqlQuery} in %s on line %s."
			);
			// Friendly message to the user.
			throw new \Exception( 'A PDO Error has occurred.' );
		}
		return $returnValue;
	}

	/**
	 * Perform a simple SELECT that does NOT have any parameters.
	 *
	 * @param string $pSelectStatement
	 * @throws \Exception
	 * @return mixed
	 */
	public function select( $pSelectStatement )
	{
		$returnValue = NULL;
		try
		{
			// Set the select.
			$stmt = $this->pdoh->prepare( $pSelectStatement );
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
		catch ( \Exception $p_error )
		{
			logError(
				$p_error,
				"Select statement failed '{$pSelectStatement}' in %s on line %s"
			);
			// Friendly message to the user.
			throw new \Exception( 'A PDO Error has occurred.' );
		}
		return $returnValue;
	}
}
// Writing below this line can cause headers to be sent before intended ?>