<?php
	
	namespace PhpSqlBuilder;

	use PhpSqlBuilder\SQL;
	
	class Model extends SQL
	{
		public static $connect = [
			'driver' => 'mysql',
			'host' => '127.0.0.1',
			'database' => 'test',
			'user' => 'root',
			'password' => '',
			'port' => 3306,
			'charset' => 'utf8',
		];

		public static function query ()
		{
			$query = new SQL(static::$connect);

			return $query->table(static::$table);
		}
	}
?>