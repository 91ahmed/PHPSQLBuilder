<?php
	
	namespace PhpSqlBuilder;

	/**
	 *	@author ahmed hassan
	 *	@link https://91ahmed.github.io
	 */
	class BuilderAssist
	{
	
		protected $query;
		protected $select;
		protected $data = array();

		public function addFromTableKeyword ()
		{
			$from = ' FROM '.static::$table;

			$this->query = str_replace($from, '', $this->query);
			$this->query .= $from;
		}

		public function addSelectKeyword (string $var)
		{
			// Check if "SELECT" keyword exists
			if ($this->select == null || empty($this->select)) {
				$this->select = "select";
				$this->query .= strtoupper($this->select)." {$var}";
			} else {
				$this->query .= ", {$var}";
			}
		}

		public function isAssoc (array $arr)
		{
			return count(array_filter(array_keys($arr), 'is_string')) > 0;
		}

		public function isSequential (array $arr)
		{
			return count(array_filter(array_keys($arr), 'is_string')) <= 0;
		}
	}
?>