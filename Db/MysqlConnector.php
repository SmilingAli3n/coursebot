<?php
namespace Db;

class MysqlConnector
{
	private static $instance;

	private string $dbUser;
	private string $dbPass;
	private string $dbName;
	private string $dbHost;
	private string $dbType;
	private string $dbEncoding;
	
	private $dataSourceName;
	
	private function __construct()
	{
        $this->dbUser     = getenv("DB_USER");
	    $this->dbPass     = getenv("DB_PASS");
	    $this->dbName     = getenv("DB_NAME");
	    $this->dbHost     = getenv("DB_HOST");
	    $this->dbType     = getenv("DB_TYPE");
	    $this->dbEncoding = getenv("DB_ENCODING");
		$this->dataSourceName = "{$this->dbType}:host={$this->dbHost};dbname={$this->dbName};charset={$this->dbEncoding}";
		$this->dbConn = new \PDO($this->dataSourceName, $this->dbUser, $this->dbPass, $this::getOptions());
	}
	
	protected function __clone() {}
	
	public function __wakeup()
	{
		throw new Exception("Cannot serialize a singleton");
	}
	
	public static function getInstance(): self
	{
		if(!isset(self::$instance))
		{
			self::$instance = new static();
		}
		return self::$instance;
	}
	
	public function getConnection()
	{
		return $this->dbConn;
	}
	
	private static function getOptions()
	{
		$options = array(
			\PDO::MYSQL_ATTR_FOUND_ROWS => true,
			\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
			//\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
		);
		return $options;
	}
}
