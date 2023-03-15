<?php
	
	namespace PhpSqlBuilder;

	use PhpSqlBuilder\BuilderAssist;
	
	/**
	 *	@author ahmed hassan
	 *	@link https://91ahmed.github.io
	 */
	class Builder extends BuilderAssist
	{
		protected static $table;

		public function all (): Builder
		{
			$this->addSelectKeyword('*');
			$this->addFromTableKeyword();

			return $this;
		}

		public function select (array $columns): Builder
		{
			// Check the parameter is a sequential array or not
			if (!$this->isSequential($columns)) {
				throw new \Exception("select() method accepts 1 parameter should be a sequential array with columns names ['column1', 'column2']", 1);
			}

			// Convert columns array to string
			$columns = (string) implode(', ', $columns);

			$this->addSelectKeyword($columns);
			$this->addFromTableKeyword();

			return $this;
		}

		/**
		 *	Support (SQL Server, MS Access)
		 */
		public function selectTop (int $number, array $columns): Builder
		{
			// Check the parameter is a sequential array or not
			if (!$this->isSequential($columns)) {
				throw new \Exception("select() method accepts 1 parameter should be a sequential array with columns names ['column1', 'column2']", 1);
			}

			// Convert columns array to string
			$columns = (string) implode(', ', $columns);

			$this->addSelectKeyword('TOP '.$number.' '.$columns);
			$this->addFromTableKeyword();

			return $this;
		}

		public function distinct (array $columns): Builder
		{
			// Check the parameter is a sequential array or not
			if (!$this->isSequential($columns)) {
				throw new \Exception("distinct() method accepts 1 parameter should be a sequential array with columns names ['column1', 'column2']", 1);
			}

			// Convert columns array to string
			$columns = (string) implode(', ', $columns);

			$this->addSelectKeyword(' DISTINCT '.$columns);
			$this->addFromTableKeyword();

			return $this;
		}

		public function alias (array $columns): Builder
		{
			// Check the array is associative or not
			if (!$this->isAssoc($columns)) {
				throw new \Exception('alias() method accepts 1 parameter should be an associative array with key and value ["column" => "alias name"]', 1);
			}

			// Convert columns array to string
			$aliases = null;
			foreach ($columns as $key => $value) {
				$aliases .= $key.' AS '.$value.', ';
			}

			// Trim comma
			$aliases = (string) trim($aliases, ', ');

			$this->addSelectKeyword($aliases);
			$this->addFromTableKeyword();

			return $this;
		}

		public function count (string $column, string $alias = ''): Builder
		{
			$count = null;

			if (empty($alias)) {
				$count = 'COUNT('.$column.'), ';
			} else {
				$count = 'COUNT('.$column.') AS '.$alias.', ';
			}

			// Trim comma
			$count = (string) trim($count, ', ');

			$this->addSelectKeyword($count);
			$this->addFromTableKeyword();

			return $this;
		}

		public function max (string $column, string $alias = ''): Builder
		{
			$max = null;

			if (empty($alias)) {
				$max = 'MAX('.$column.'), ';
			} else {
				$max = 'MAX('.$column.') AS '.$alias.', ';
			}

			// Trim comma
			$max = (string) trim($max, ', ');

			$this->addSelectKeyword($max);
			$this->addFromTableKeyword();

			return $this;
		}

		public function min (string $column, string $alias = ''): Builder
		{
			$min = null;

			if (empty($alias)) {
				$min = 'MIN('.$column.'), ';
			} else {
				$min = 'MIN('.$column.') AS '.$alias.', ';
			}

			// Trim comma
			$min = (string) trim($min, ', ');

			$this->addSelectKeyword($min);
			$this->addFromTableKeyword();

			return $this;
		}

		public function sum (string $column, string $alias = ''): Builder
		{
			$sum = null;

			if (empty($alias)) {
				$sum = 'SUM('.$column.'), ';
			} else {
				$sum = 'SUM('.$column.') AS '.$alias.', ';
			}

			// Trim comma
			$sum = (string) trim($sum, ', ');

			$this->addSelectKeyword($sum);
			$this->addFromTableKeyword();

			return $this;
		}

		public function avg (string $column, string $alias = ''): Builder
		{
			$avg = null;

			if (empty($alias)) {
				$avg = 'AVG('.$column.'), ';
			} else {
				$avg = 'AVG('.$column.') AS '.$alias.', ';
			}

			// Trim comma
			$avg = (string) trim($avg, ', ');

			$this->addSelectKeyword($avg);
			$this->addFromTableKeyword();

			return $this;
		}

		public function where (string $column): Builder
		{
			$this->query .= ' WHERE '.$column;

			return $this;
		}

		public function whereNot (string $column): Builder
		{
			$this->query .= ' WHERE NOT '.$column;

			return $this;
		}

		public function isNull (): Builder
		{
			$this->query .= ' IS NULL';

			return $this;
		}

		public function isNotNull (): Builder
		{
			$this->query .= ' IS NOT NULL';

			return $this;
		}

		public function like (string $pattern): Builder
		{
			array_push($this->data, $pattern);

			$this->query .= ' LIKE ?';

			return $this;
		}

		public function in (array $values): Builder
		{
			// Check the parameter is a sequential array or not
			if (!$this->isSequential($values)) {
				throw new \Exception("in() method accepts 1 parameter should be a sequential array with values ['value1', 'value2']", 1);
			}

			$this->data = array_merge($this->data, $values);

			$in  = str_repeat('?,', count($values) - 1) . '?';

			$this->query .= " IN ($in)";

			return $this;
		}

		public function between ($value1, $value2): Builder
		{
			if (!is_scalar($value1) || !is_scalar($value2)) {
				throw new \Exception("between() method has invalid parameter value.", 1);
			}

			array_push($this->data, $value1);
			array_push($this->data, $value2);

			$this->query .= " BETWEEN ? AND ?";

			return $this;
		}

		public function and (string $column): Builder
		{
			$this->query .= ' AND '.$column;

			return $this;
		}

		public function or (string $column): Builder
		{
			$this->query .= ' OR '.$column;

			return $this;
		}

		public function not (): Builder
		{
			$this->query .= ' NOT ';

			return $this;
		}

		public function value (string $operator, $value): Builder
		{
			if (!is_scalar($value)) {
				throw new \Exception("value() method has invalid parameter value.", 1);
			}

			$opt = ['=', '<', '>', '!=', '<=', '>=', '<>'];	

			array_push($this->data, $value);

			if (in_array($operator, $opt)){
				$this->query .= ' '.$operator.' ?';
			} else {
				$this->query .= ' = ?';
			}

			return $this;
		}

		public function limit (int $value): Builder
		{
			array_push($this->data, $value);

			$this->query .= ' LIMIT ?';

			return $this;
		}

		public function innerJoin (string $table2, string $column1, string $operator, string $column2): Builder
		{
			$this->query .= ' INNER JOIN '.$table2.' ON '.$column1.' '.$operator.' '.$column2;

			return $this;
		}

		public function leftJoin (string $table2, string $column1, string $operator, string $column2): Builder
		{
			$this->query .= ' LEFT JOIN '.$table2.' ON '.$column1.' '.$operator.' '.$column2;

			return $this;
		}

		public function rightJoin (string $table2, string $column1, string $operator, string $column2): Builder
		{
			$this->query .= ' RIGHT JOIN '.$table2.' ON '.$column1.' '.$operator.' '.$column2;

			return $this;
		}

		public function fullJoin (string $table2, string $column1, string $operator, string $column2): Builder
		{
			$this->query .= ' FULL OUTER JOIN '.$table2.' ON '.$column1.' '.$operator.' '.$column2;

			return $this;
		}

		public function crossJoin (string $table2): Builder
		{
			$this->query .= ' CROSS JOIN '.$table2;

			return $this;
		}

		public function union (array $columns, string $table2): Builder
		{
			// Check the parameter is a sequential array or not
			if (!$this->isSequential($columns)) {
				throw new \Exception("union() method first parameter should be a sequential array with columns names ['column1', 'column2']", 1);
			}

			// Convert columns array to string
			$columns = (string) implode(', ', $columns);

			$this->query .= ' UNION SELECT '.$columns.' FROM '.$table2;

			return $this;
		}

		public function unionAll (array $columns, string $table2): Builder
		{
			// Check the parameter is a sequential array or not
			if (!$this->isSequential($columns)) {
				throw new \Exception("union() method first parameter should be a sequential array with columns names ['column1', 'column2']", 1);
			}

			// Convert columns array to string
			$columns = (string) implode(', ', $columns);

			$this->query .= ' UNION ALL SELECT '.$columns.' FROM '.$table2;

			return $this;
		}

		public function groupBy (array $columns): Builder
		{
			// Check the parameter is a sequential array or not
			if (!$this->isSequential($columns)) {
				throw new \Exception("groupBy() parameter should be a sequential array with columns names ['column1', 'column2']", 1);
			}

			// Convert columns array to string
			$columns = (string) implode(', ', $columns);

			$this->query .= 'GROUP BY '.$columns;

			return $this;
		}

		public function having (string $column): Builder
		{
			$this->query .= ' HAVING '.$column;

			return $this;
		}

		public function orderBy (array $columns, string $sort = 'DESC'): Builder
		{
			if (!$this->isSequential($columns)) {
				throw "orderBy() method first parameter should be sequential array.";
			}

			$order = ['DESC', 'ASC'];

			// Convert columns array to string
			$columns = (string) implode(', ', $columns);

			if (in_array($sort, $order)) {
				$this->query .= ' ORDER BY '.$columns.' '.$sort;
			} else {
				$this->query .= ' ORDER BY '.$columns.' DESC';
			}
			
			return $this;
		}

		public function delete (): Builder
		{
			$this->query = 'DELETE FROM '.static::$table;

			return $this;
		}

		public function truncate (): Builder
		{
			$this->query = 'TRUNCATE TABLE '.static::$table;
			
			return $this;
		}

		public function insert (array $data): Builder
		{
			// Check the array is associative or not
			if (!$this->isAssoc($data)) {
				throw new \Exception('insert() method accepts 1 parameter should be an associative array with key and value ["column" => "value"]', 1);
			}

			$this->data = array_merge($this->data, array_values($data));

			$this->query = 'INSERT INTO '. static::$table . ' (';

			foreach ($data as $column => $value) {
				$this->query .= $column.', ';
			}

			$this->query = trim($this->query, ', ');
			$this->query .= ') VALUES (';

			foreach ($data as $column => $value) {
				$this->query .= '?, ';
			}

			$this->query = trim($this->query, ', ');
			$this->query .= ')';

			return $this;
		}

		public function update (array $data): Builder
		{
			// Check the array is associative or not
			if (!$this->isAssoc($data)) {
				throw new \Exception('update() method accepts 1 parameter should be an associative array with key and value ["column" => "value"]', 1);
			}
			
			$this->data = array_merge($this->data, array_values($data));

			$this->query = 'UPDATE ' . static::$table . ' SET ';
			
			foreach ($data as $column => $value) {
				$this->query .= $column . " = ?, ";
			}

			$this->query = trim($this->query, ', ');
			
			return $this;
		}
	}
?>