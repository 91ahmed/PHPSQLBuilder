<?php
	
	namespace PhpSqlBuilder;

	use PhpSqlBuilder\Builder;
	use PhpSqlBuilder\Connect;

	/**
	 *	@author ahmed hassan
	 *	@link https://91ahmed.github.io
	 */
	class SQL extends Builder
	{
		private $pdo;
		private $connect;
		private $info;

		public function __construct (array $info)
		{
			// Assign parameter info to info property
			$this->info = $info;

			// Create new instance
			$this->connect = new Connect();

			// Define database information
			$this->connect->set('driver', (isset($this->info['driver'])) ? $this->info['driver'] : 'mysql'); 
			$this->connect->set('host', (isset($this->info['host'])) ? $this->info['host'] : 'localhost');
			$this->connect->set('database', (isset($this->info['database'])) ? $this->info['database'] : 'test');
			$this->connect->set('user', (isset($this->info['user'])) ? $this->info['user'] : 'root');
			$this->connect->set('password', (isset($this->info['password'])) ? $this->info['password'] : '');
			$this->connect->set('port', (isset($this->info['port'])) ? $this->info['port'] : 3306);
			$this->connect->set('charset', (isset($this->info['charset'])) ? $this->info['charset'] : 'utf8');

			// Initialize PDO
			$this->pdo = $this->connect->pdo();
		}

		public function save ()
		{
			$stmt = $this->pdo->prepare($this->query);
			$stmt->execute($this->data);			
		}

		public function get ()
		{
			$stmt = $this->pdo->prepare($this->query);
			$stmt->execute($this->data);
			$result = $stmt->fetchAll();

			return (object) $result;
		}

		public function custom (string $customQuery)
		{	
			$stmt = $this->pdo->prepare($customQuery);
			$stmt->execute();
			$result = $stmt->fetchAll();

			return (object) $result;
		}

		public function truncate (): SQL
		{
			if ($this->info['driver'] === 'pgsql') {
				$this->query = 'TRUNCATE '.static::$table;
			} else {
				$this->query = 'TRUNCATE TABLE '.static::$table;
			}

			return $this;
		}

		public function table (string $table): SQL
		{
			if (!empty($table)) {
				static::$table = $table;
			} else {
				static::$table = $this->info['table'];
			}

			return new SQL($this->info);
		}

		public function __destruct ()
		{
			$this->query = '';
			$this->data = [];
			unset($this->pdo, $this->connect, $this->info);
		}
	}

?>