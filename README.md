## PHP SQL BUILDER
PHP query builder to create and execute SQL queries for different database systems.

The query builder support ``mysql``  ``postgresql``  ``sql server``  ``sqlite`` and provide plenty of methods to cover the most of SQL statements and apply restrictions and parameters to the data to verify and filter the user inputs in order to prevent SQl injection.

### Install via composer
``` bash
composer require sqlbuilder/phpsqlbuilder
```

### Example
``` php
require ('vendor/autoload.php');

// Connect to database
$sql = new PhpSqlBuilder\SQL([
	'driver' => 'mysql', // support (mysql, pgsql, sqlsrv, sqlite)
	'host' => '127.0.0.1',
	'database' => 'test',
	'user' => 'root',
	'password' => '',
	'port' => 3306,
	'charset' => 'utf8',
]);

// Build your SQL Query
$result = $sql->table('users')
              ->select(['id', 'name'])
              ->where('id')->value('>', 16)
              ->orderBy('id')
              ->limit(5)
              ->get();
```

### Available Methods

**Method** | **Describe** | **Parameters** | **Output**
:--- | :--- | :--- | :---
[table()](#table) | Define the table name. | 1. _(string)_ table name | ___
[get()](#get) | Return an object with the result of sql query, You have to call this method at the end of the query builder. | no | ___
[all()](#all) | Select all columns. | no | SELECT *
[select()](#select) | Select specific columns. | 1. _(sequential array)_ columns | SELECT columns
[selectTop()](#selectTop) | Select limited rows. (support sql server only) | 1. _(integer)_ rows number<br/><br/>2. _(sequential array)_ columns | SELECT TOP 5 columns
[distinct()](#distinct) | Select distinct rows. |  1. _(sequential array)_ columns | SELECT DISTINCT columns
[alias()](#alias) | Select with alias name. | 1. _(associative array)_ column and alias name | SELECT column AS alias
[max()<br/><br/>min()<br/><br/>sum()<br/><br/>avg()<br/><br/>count()](#aggregate-functions) | Select with aggregate functions. The five methods accepts 2 parameters, first one is the column name and the second is *_optional_ the alias name. | 1. _(string)_ column name<br/><br/>2. _(string)_ alias name *_optional_ | SELECT MAX(column)
[where()](#where) | Add WHERE clause to specify a condition. | 1. _(string)_ column name | WHERE column
[whereNot()](#whereNot) | Add NOT keyword to WHERE clause. | 1. _(string)_ column name | WHERE NOT column
[value()](#where) | Used to specify the operator and the value after WHERE statement. | 1. _(string)_ the operator<br/><br/>2. _(scalar)_ the value | = value
[isNull()](#isNull-isNotNull) | Used with WHERE clause to select null values. | no | IS NULL
[isNotNull()](#isNull-isNotNull) | Used with WHERE clause to select non-null values. | no | IS NOT NULL
[like()](#like-in-between) | Used in a WHERE clause to search for a specified pattern. | 1. _(string)_ pattern | LIKE %pattern%
[in()](#like-in-between) | Used in a WHERE clause to specify multiple values. | 1. _(sequential array)_ values | IN (1,3,6)
[between()](#like-in-between) | Used in a WHERE clause selects values within a given range. | 1. _(scalar)_ value1<br/><br/>2. _(scalar)_ value2 | BETWEEN value1 AND value2
[and()<br/><br/>or()<br/><br/>not()](#and-or-not) | These operetors can be combined with the query to add multiple conditions. | _(string)_ column name | AND column / OR column
[innerJoin()<br/><br/>leftJoin()<br/><br/>rightJoin()<br/><br/>fullJoin()](#joins) | Join multiple tables. | 1. _(string)_ table name<br/><br/>2. _(string)_ column1<br/><br/>3. _(string)_ operator<br/><br/>4. _(string)_ column2 | INNER JOIN table ON column1 = column2
[limit()](#limit) | Retrieve limited rows. | 1. _(interger)_ rows number | LIMIT 5
[union()<br/><br/>unionAll()](#union-unionAll) | Used to combine the result of two tables. | 1. _(sequential array)_ columns<br/><br/>2. _(string)_ table2 | UNION SELECT columns FROM table2
[groupBy()](#groupBy) | Used to arrange identical data into groups. | 1. _(sequential array)_ columns | GROUP BY columns
[having()](#having) | HAVING clause used with GROUP BY to specify a condition. | 1. _(string)_ column name | HAVING column
[orderBy()](#ordeBy) | Used to sort rows according to specific columns. | 1. _(sequential array)_ columns<br/><br/>2. _(string)_ sort ASC/DESC | ORDER BY columns DESC
[delete()](#delete) | Generate sql delete statement. | no | DELETE FROM table
[insert()](#insert) | Generate sql insert statement. | 1. _(associative array)_ column and value | INSERT INTO table (columns) VALUES (values)
[update()](#update) | Generate sql update statement. | 1. _(associative array)_ column and value | UPDATE table SET column = value
[save()](#delete) | You should call this method at the end of insert, update and delete methods to execute the query. | no | ___

#### table
> Define the database table name.
``` php
$result = $sql->table('tablename')
              ->all()
              ->get();
```

#### get
> Return an object with the result of sql query, You have to call this method at the end of the query builder.
``` php
$result = $sql->table('tablename')
              ->all()
              ->get();
```

#### all
> Select all columns.
``` php
$result = $sql->table('tablename')
              ->all()
              ->get();
```

#### select
> Select specific columns.
``` php
$result = $sql->table('tablename')
              ->select(['col1', 'col2', 'col3'])
              ->get();
```

#### selectTop
> Select limited rows. (support sql server only)
``` php
$result = $sql->table('tablename')
              ->selectTop(4, ['col1', 'col2'])
              ->get();
```

#### distinct
> Select distinct rows.
``` php
$result = $sql->table('tablename')
              ->distinct(['col1', 'col2', 'col3'])
              ->get();
```

#### alias
> Select with alias name.
``` php
$result = $sql->table('tablename')
              ->alias([
              	'column1' => 'alias name',
              	'column2' => 'alias name'
              ])
              ->get();
```

#### aggregate functions
> Select with aggregate functions (MAX, MIN, COUNT, SUM, AVG).
``` php
$result = $sql->table('tablename')
              ->count('id')
              ->max('price', 'unitprice')
              ->min('price')
              ->avg('price')
              ->sum('id')
              ->get();
```

#### where
> Add where clause to specify a condition.
``` php
$result = $sql->table('tablename')
              ->all()
              ->where('col')->value('=', 5)
              ->get();
```

#### whereNot
> Add NOT keyword to where clause.
``` php
$result = $sql->table('tablename')
              ->all()
              ->whereNot('col')->value('<', 5)
              ->get();
```

#### isNull isNotNull
``` php
// IS NULL
$result = $sql->table('tablename')
              ->all()
              ->where('col')->isNull()
              ->get();

// IS NOT NULL
$result = $sql->table('tablename')
              ->all()
              ->where('col')->isNotNull()
              ->get();
```

#### like in between
``` php
// LIKE
$result = $sql->table('tablename')
              ->all()
              ->where('col')->like('%pattern%')
              ->get();

// IN
$result = $sql->table('tablename')
              ->all()
              ->where('col')->in([45, 76, 88])
              ->get();

// BETWEEN
$result = $sql->table('tablename')
              ->all()
              ->where('col')->between(2, 10)
              ->get();
```

#### and or not
> These operetors can be combined with the query to add multiple conditions.
``` php
// AND
$result = $sql->table('tablename')
              ->all()
              ->where('col')->value('=', 'value')
              ->and('col2')->value('=', 'value')
              ->get();

// OR
$result = $sql->table('tablename')
              ->all()
              ->where('col')->value('=', 'value')
              ->or('col2')->value('=', 'value')
              ->get();

// NOT (does not accept parameters)
$result = $sql->table('tablename')
              ->all()
              ->where('col')->not()->value('=', 'value')
              ->get();
```

#### joins
``` php
// INNER JOIN
$result = $sql->table('table1')
              ->innerJoin('table2', 'table1.column', '=', 'table2.column')
              ->get();

// LEFT JOIN
$result = $sql->table('table1')
              ->leftJoin('table2', 'table1.column', '=', 'table2.column')
              ->get();

// RIGHT JOIN
$result = $sql->table('table1')
              ->rightJoin('table2', 'table1.column', '=', 'table2.column')
              ->get();

// FULL OUTER JOIN
$result = $sql->table('table1')
              ->fullJoin('table2', 'table1.column', '=', 'table2.column')
              ->get(); 

// CROSS JOIN (accept 1 string parameter)
$result = $sql->table('table1')
              ->crossJoin('table2')
              ->get();
```

#### limit
> Retrieve limited rows.
``` php
$result = $sql->table('tablename')
              ->all()
              ->limit(5)
              ->get();
```

#### union unionAll
> Used to combine the result of two tables.
``` php
// UNION
$result = $sql->table('tablename')
              ->select(['column1', 'column2'])
              ->union(['column1', 'column2'], 'table2')
              ->get();

// UNION ALL
$result = $sql->table('tablename')
              ->select(['column1', 'column2'])
              ->unionAll(['column1', 'column2'], 'table2')
              ->get();
```

#### groupBy
> Used to arrange identical data into groups.
``` php
$result = $sql->table('tablename')
              ->select(['col1', 'col2'])
              ->groupBy(['col1'])
              ->get();
```

#### having
> Used with GROUP BY to specify a condition.
``` php
$result = $sql->table('tablename')
              ->select(['col1', 'col2'])
              ->groupBy(['col1'])
              ->having('col1')->value('=', 'value')
              ->get();
```

#### orderBy
> Used to sort rows according to specific columns.
``` php
$result = $sql->table('tablename')
              ->all()
              ->orderBy(['col'], 'ASC') // default DESC
              ->get();
```

#### delete
> You must use ``save()`` method at the end of insert, update and delete methods to execute the sql query.
``` php
// EX 1
$sql->table('tablename')
    ->delete()
    ->where('col')->value('=', 1)
    ->save(); // execute

// EX 2
$sql->table('tablename')
    ->delete()
    ->where('col')->in(['val1', 'val2', 'val3'])
    ->save();
```

#### insert
``` php
$sql->table('tablename')
	->insert([
        'id' => 84,
        'name' => 'ahmed',
        'email' => 'ahmed@gmail.com'
	])->save(); 
```

#### update
``` php
$sql->table('tablename')
    ->update([
        'name' => 'omar',
        'email' => 'omar@gmail.com'
    ])
    ->where('id')->value('=', 88)
    ->save();
```