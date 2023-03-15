<?php
	
	namespace PhpSqlBuilder;

	/**
	 *	Database connection using PDO extension.
	 *
	 *	@author ahmed hassan
	 *	@link https://91ahmed.github.io
	 */
	class Connect
	{
		/**
		 *	@var array, database information
		 */
		private $config = array(
			'driver'   => 'mysql', // mysql - sqlsrv - pgsql - sqlite
			'host'     => 'localhost', // localhost - 127.0.0.1 - https://www.example.com
			'database' => 'test', // database name
			'user'     => 'root', // database username
			'password' => '', // database password
			'port'     => 3306, // mysql (3306) - pgsql (5432) - sqlsrv (1433)
			'charset'  => 'utf8',
			'sslmode'  => 'disable',
			'sqlite'   => 'storage/sqlite/restful.db'
		);

		/**
		 *	@var array $options, PDO mysql options.
		 */
		private $options = [
			\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8',
		];

		/**
		 *	@var array $attributes, PDO attributes.
		 */
		private $attributes = [
			\PDO::ATTR_EMULATE_PREPARES => false,
			\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
			\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
			\PDO::ATTR_CASE => \PDO::CASE_NATURAL
		];

		/**
		 *	@var object $connect, Holds PDO connection object.
		 */
		private $connect;

		/**
		 *	@var string $sqlite, store the sqlite file path.
		 */
		private $sqlite = 'storage/sqlite/restful.db';

		/**
		 *	Connect to MySQL.
		 *
		 *	@return void
		 */
		private function mysql ()
		{
			$this->connect = new \PDO("mysql:host={$this->config['host']};port={$this->config['port']};dbname={$this->config['database']};charset={$this->config['charset']}", $this->config['user'], $this->config['password'], $this->options);
		}

		/**
		 *	Connect to PostgreSQL.
		 *
		 *	@return void
		 */
		private function postgresql ()
		{
			$this->connect = new \PDO("pgsql:host={$this->config['host']};port={$this->config['port']};dbname={$this->config['database']};sslmode={$this->config['sslmode']}", $this->config['user'], $this->config['password']);
		}

		/**
		 *	Connect to Microsoft SQL Server.
		 *
		 *	@return void
		 */
		private function sqlserver ()
		{
			$this->connect = new \PDO("sqlsrv:Server=".$this->config['host'].";Database=".$this->config['database']."", $this->config['user'], $this->config['password']);
		}

		/**
		 *	Connect to SQLite
		 *
		 *	@return void
		 */
		private function sqlite ()
		{
			$this->connect = new \PDO("sqlite:".$this->config['sqlite']);
		}

		/**
		 *	Set connection information.
		 *
		 *	@param string $key, database config key
		 *	@param mixed $value, database config value
		 *	@return void
		 */
		public function set ($key, $value)
		{
			$this->config[$key] = $value;
		}

		/**
		 *	Set PDO attributes.
		 *
		 *	@param mixed $key, attribute key
		 *	@param mixed $value, attribute value
		 *	@return void
		 */
		public function attr ($key, $value)
		{
			$this->attributes[$key] = $value;
		}

		/**
		 *	Check the database driver to execute PDO connection.
		 *
		 *	@return void
		 */
		private function detectDriver ()
		{
			switch ($this->config['driver'])
			{
				case 'mysql':
					$this->mysql();
					break;
				case 'pgsql':
					$this->postgresql();
					break;
				case 'sqlsrv':
					$this->sqlserver();
					break;
				case 'sqlite':
					$this->sqlite();
					break;
				default:
					throw new \Exception("Undefined database driver ({$this->config['driver']})", 1);
			}
		}

		/**
		 *	Open database connection
		 *
		 *	@return object, PDO connection
		 */
		public function pdo ()
		{
			try 
			{
				$this->detectDriver();
			}
			catch (\PDOException $e) 
			{
				throw new \Exception($e->getMessage(), 1);
			}

			// Loop on PDO attributes array
			foreach ($this->attributes as $key => $value) 
			{
				$this->connect->setAttribute($key, $value); // set attribute
			}

			return $this->connect;
		}

		public function __destruct ()
		{
			$this->connect = null;
			unset($this->connect);
		}
	}
?>