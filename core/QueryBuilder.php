<?php

namespace Core;

use Core\Database;

class QueryBuilder
{
    /**
     * Final query which is to be executed in PDO.
     *
     * @var string
     */
    protected $query;

    /**
     * SELECT clause for database.
     *
     * @var string
     */
    protected $select;

    /**
     * WHERE clause for database.
     *
     * @var string
     */
    protected $where;

    /**
     * ORDER BY clause for database.
     *
     * @var string
     */
    protected $order_by;

    /**
     * INSERT clause for database.
     *
     * @var string
     */
    protected $insert;

    /**
     * VALUES string for INSERT clause in database.
     *
     * @var string
     */
    protected $values;

    /**
     * UPDATE clause for database.
     *
     * @var string
     */
    protected $update;

    /**
     * SET string for UPDATE clause in database.
     *
     * @var string
     */
    protected $set;

    /**
     * "DELETE" query for database.
     * This is an UPDATE clause which soft deletes records.
     *
     * @var string
     */
    protected $delete;

    /**
     * Relationships which are to be fetched for the current query.
     *
     * @var array
     */
    protected $with = [];

    /**
     * Values which are to be bound for the current query.
     *
     * @var array
     */
    protected $binds = [];

    /**
     * Current database instance.
     *
     * @var \PDO
     */
    protected $db;

    /**
     * Current model instance.
     *
     * @var Model
     */
    public $model;

    /**
     * Table name to select from, if specified.
     * If not get table name from Model.
     *
     * @var string
     */
    public $from;

    /**
     * Final results returned from the executed query.
     *
     * @var App\Models\Model
     */
    protected $results;

    /**
     * Results returned for intermediate table in case of Many-to-Many relationship from the executed query.
     *
     * @var App\Models\Model
     */
    protected $intermediate_results;

    /**
     * Final relations returned from the executed query.
     *
     * @var array
     */
    protected $relations = [];

    /**
     * Get current database instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Concat properties and create final query for SELECT clause.
     *
     * @return void
     */
    protected function makeSelectQuery()
    {
        $this->query = $this->select . $this->where . $this->order_by;
    }

    /**
     * Concat properties and create final query for INSERT clause.
     *
     * @return void
     */
    protected function makeInsertQuery()
    {
        $this->query = $this->insert . $this->values;
    }

    /**
     * Concat properties and create final query for UPDATE clause.
     *
     * @return void
     */
    protected function makeUpdateQuery()
    {
        $this->query = $this->update . $this->set . $this->where;
    }

    /**
     * Concat properties and create final query for DELETE clause.
     *
     * @return void
     */
    protected function makeDeleteQuery()
    {
        $this->query = $this->delete . $this->where;
    }

    /**
     * Sends final SELECT query for execution along with relations.
     *
     * @return App\Models\Model
     */
    public function get()
    {
        $this->results = $this->sendToExecute('select')->fetchAll();
        $this->fetchRelations();

        return sanitizeArray($this->results);
    }

    /**
     * Sends final SELECT query for execution along with relations.
     *
     * @return mixed Model|Single object
     */
    public function first()
    {
        $this->results = $this->sendToExecute('select')->fetch();

        if ($this->results) {
            $this->results = [$this->results];
            $this->fetchRelations();
        }

        return isset($this->results[0]) ? sanitizeArray($this->results[0]) : (object) [];
    }

    /**
     * Generates response structure which is to be returned after executing SELECT queries.
     *
     * @param sting $relation Name of the method declared in Model Class for relationship
     * @return void
     */
    protected function generateResponse($relation)
    {
        foreach ($this->results as $result) {

            $array = [];

            $rel = $this->model->$relation();

            $foreign_key = $rel['foreign_key'];

            switch ($rel['relation']) {
                case 'hasMany':
                    $array = $this->getHasManyForResponse($result, $foreign_key);
                    break;
                case 'belongsTo':
                    $array = $this->getBelongsToForResponse($result, $foreign_key);
                    break;
                case 'belongsToMany':
                    $primary_table_key = $rel['primary_table_key'];
                    $array = $this->getBelongsToManyForResponse($result, $foreign_key, $primary_table_key);
                    break;
            }

            $result->relations[$relation] = $array;
        }
    }

    /**
     * Filter the relationship results and return records only for the current object.
     * For One-To-Many.
     *
     * @param App\Model $obj        Object of the current Model Class
     * @param string $foreign_key   Foreign key for the relationship defined in Model Class
     * @return array
     */
    protected function getHasManyForResponse($obj, $foreign_key)
    {
        $results = array_filter($this->relations, function ($elem) use ($obj, $foreign_key) {
            return $elem->$foreign_key == $obj->id;
        });

        return $results;
    }

    /**
     * Filter the relationship results and return records only for the current object.
     * For Many-To-One or One-To-One.
     *
     * @param App\Model $obj        Object of the current Model Class
     * @param string $foreign_key   Foreign key for the relationship defined in Model Class
     * @return App\Model
     */
    protected function getBelongsToForResponse($obj, $foreign_key)
    {
        $results = array_filter($this->relations, function ($elem) use ($obj, $foreign_key) {
            return $elem->id == $obj->$foreign_key;
        });

        return isset($results[0]) ? $results[0] : (object) null;
    }

    /**
     * Filter the relationship results and return records only for the current object.
     * For Many-To-Many.
     *
     * @param App\Model $obj            Object of the current Model Class
     * @param string $foreign_key       Foreign key for the relationship defined in Model Class
     * @param string $primary_table_key Column name of the primary table in intermediate table
     * @return array
     */
    protected function getBelongsToManyForResponse($obj, $foreign_key, $primary_table_key)
    {
        $results = array_filter($this->relations, function ($elem) use ($obj, $foreign_key, $primary_table_key) {

            $value = array_filter($this->intermediate_results, function ($res) use ($elem, $obj, $foreign_key, $primary_table_key) {
                return $obj->id == $res->$primary_table_key && $elem->id == $res->$foreign_key;
            });

            return count($value);

        });
        return $results;
    }

    /**
     * Fetch relationship records for One-To-Many relationship.
     *
     * @param Model $obj            Object of the current Model Class
     * @param string $foreign_key   Foreign key for the relationship defined in Model Class
     * @param array $ids            IDs of records whose relationship is to be fetched
     * @return mixed Model|Array of objects
     */
    protected function processHasMany($obj, $foreign_key, $ids)
    {
        return $obj->select()->whereIn($foreign_key, $ids)->get();
    }

    /**
     * Fetch relationship records for Many-To-One, One-To-One relationship.
     *
     * @param Model $obj            Object of the current Model Class
     * @param string $foreign_key   Foreign key for the relationship defined in Model Class
     * @param array $ids            IDs of records whose relationship is to be fetched
     * @return mixed Model|Array of objects
     */
    protected function processBelongsTo($obj, $foreign_key, $ids)
    {
        return $obj->select()->whereIn($foreign_key, $ids)->get();
    }

    /**
     * Fetch relationship records for Many-To-Many relationship.
     *
     * @param App\Models\Model $obj         Object of the current Model Class
     * @param string $intermediate_table    Table name of the intermediate tablet for Many-to-Many
     * @param string $primary_table_key     Column name of the primary table in intermediate table
     * @param string $foreign_key           Foreign key for the relationship defined in Model Class
     * @param array $ids                    IDs of records whose relationship is to be fetched
     * @return mixed Model|Array of objects
     */
    protected function processBelongsToMany($obj, $intermediate_table, $primary_table_key, $foreign_key, $ids)
    {
        $this->intermediate_results = $obj->select('*', $intermediate_table)
            ->whereIn($primary_table_key, $ids)
            ->get();

        $foreign_ids = array_column($this->intermediate_results, $foreign_key);

        $results = $obj->select('*')
            ->whereIn('id', $foreign_ids)
            ->get();

        return $results;
    }

    /**
     * If current execution has results and has requested for relationships, fetch them.
     * Currently only works for One-To-One, One-To-Many and Many-To-On relationships.
     * TO DO Many to Many
     *
     * @return Core\QueryBuilder
     */
    protected function fetchRelations()
    {
        if (count($this->results) && count($this->with)) {

            foreach ($this->with as $key => $relation_name) {

                //Set nested with to false
                $nested_with = false;

                //Set method to execute as current relation name
                $method = $relation_name;

                //Check if relation is nested, meaning related records are to be fetched for inner relations.
                if (strpos($relation_name, '.') !== false) {

                    //Explode relation and assign first relation to be executed.
                    $nested_with = explode('.', $relation_name, 2);
                    $method = $nested_with[0];
                }

                //Execute the method in model class
                $relation = $this->model->$method();

                $class = 'App\Models\\' . $relation['class'];
                $foreign_key = $relation['foreign_key'];
                $relationship = $relation['relation'];

                //Make a new instance of Core\QueryBuilder and the requested App\Models\Model Class
                $builder = new QueryBuilder;
                $obj = new $class;
                $builder->model = $obj;

                /**
                 * If requested for nested with, call with method on object.
                 * This makes a recursively loop of and fetches deeper relationships.
                 */
                if ($nested_with) {
                    $obj = $obj->with([$nested_with[1]]);
                }

                switch ($relationship) {
                    case 'hasMany':
                        $ids = array_column($this->results, 'id');
                        $result = $this->processHasMany($obj, $foreign_key, $ids);
                        break;
                    case 'belongsTo':
                        $ids = array_column($this->results, $foreign_key);
                        $result = $this->processBelongsTo($obj, 'id', $ids);
                        break;
                    case 'belongsToMany':
                        $intermediate_table = $relation['table'];
                        $primary_table_key = $relation['primary_table_key'];
                        $ids = array_column($this->results, 'id');
                        $result = $this->processBelongsToMany($obj, $intermediate_table, $primary_table_key, $foreign_key, $ids);
                        break;
                }

                $this->relations = $result;
                $this->generateResponse($method);
            }

        }

        return $this;
    }

    /**
     * Build the start of SELECT clause.
     *
     * @param mixed string|array $columns   Database columns for SELECT clause
     * @return Core\QueryBuilder
     */
    public function select($columns = null, $from = null)
    {
        if (is_array($columns)) {

            if (!count($columns)) {
                $select = ' * ';
            } else {
                $select = implode(', ', $columns);
            }

        } else {

            if ($columns == null) {
                $select = ' * ';
            } else {
                $select = $columns;
            }

        }

        $this->select .= "Select " . $select . " ";

        $from = !empty($from) ? $from : $this->model->table;

        $this->select .= " FROM " . $from . " ";
        return $this;
    }

    /**
     * Build the WHERE clause.
     *
     * @param string $column    Database column on which WHERE clause is to be used
     * @param string $operator  DB Operators such as =, >=, <, LIKE etc
     * @param mixed $value      Value against which the column is to be checked
     * @return Core\QueryBuilder
     */
    public function where($column, $operator, $value)
    {
        $this->appendWhere()->makeWhereBinds($column, $operator, $value);

        return $this;
    }

    /**
     * Build the OR for WHERE clause.
     *
     * @param string $column    Database column on which WHERE clause is to be used
     * @param string $operator  DB Operators such as =, >=, <, LIKE etc
     * @param mixed $value      Value against which the column is to be checked
     * @return Core\QueryBuilder
     */
    public function orWhere($column, $operator, $value)
    {
        $this->appendWhere(false)->makeWhereBinds($column, $operator, $value);

        return $this;
    }

    /**
     * Build the WHEREIN clause. Bind values for WHEREIN clause.
     *
     * @param string $column    Database column on which WHEREIN clause is to be used
     * @param array $values     Values to be checked for the column
     * @return Core\QueryBuilder
     */
    public function whereIn($column, $values)
    {
        $this->appendWhere();

        $where_in_prepare = implode(', ', array_fill(0, count($values), ' ? '));

        $this->where .= $column . " IN ( " . $where_in_prepare . " )";

        $this->binds = array_merge($this->binds, $values);

        return $this;
    }

    /**
     * Append or Initiate WHERE clause.
     *
     * @param boolean $and  Indicates if its AND / OR for WHERE. AND if ture, OR if false.
     * @return Core\QueryBuilder
     */
    protected function appendWhere($and = true)
    {
        if (strpos($this->where, "WHERE") === false) {
            $this->where = " WHERE ";
        } elseif ($and) {
            $this->where .= " AND ";
        } else {
            $this->where .= " OR ";
        }

        return $this;
    }

    /**
     * Assign columnd and Bind values for WHER clause.
     *
     * @param string $column    Database column on which WHERE clause is to be used
     * @param string $operator  DB Operators such as =, >=, <, LIKE etc
     * @param mixed $value      Value against which the column is to be checked
     * @return Core\QueryBuilder
     */
    public function makeWhereBinds($column, $operator, $value)
    {
        $this->where .= $column . " " . $operator . " " . ' ? ';

        $this->binds[] = $value;

        return $this;
    }

    /**
     * Build the start of INSERT clause.
     *
     * @return Core\QueryBuilder
     */
    public function insert()
    {
        $this->insert = "INSERT INTO " . $this->model->table . " ";

        return $this;
    }

    /**
     * Build the VALEUS query for INSERT clause.
     *
     * @param array $columns    Database Columns on which INSERT clause is to be used
     * @return Core\QueryBuilder
     */
    protected function columns($columns)
    {
        $db_columns = implode(', ', $columns);
        $insert_prepare = implode(', ', array_fill(0, count($columns), '?'));

        $this->values .= " ($db_columns) VALUES ($insert_prepare)";

        return $this;
    }

    /**
     * Bind values for INSERT clause.
     *
     * @param array $value  Values against columns for INSERT clause
     * @return Core\QueryBuilder
     */
    protected function values($values)
    {
        $this->binds = $values;

        return $this;
    }

    /**
     * Send final INSERT query for execution.
     *
     * @return void
     */
    public function save()
    {
        $insert_columns = array_keys((array) $this->model->attributes);
        $insert_values = array_values((array) $this->model->attributes);

        $result = $this->insert()
            ->columns($insert_columns)
            ->values($insert_values)
            ->sendToExecute('insert');
    }

    /**
     * Get the final database query and send it to PDO for execution.
     *
     * @param string $operation     Database operation such as insert, select, update, delete
     * @return mixed
     */
    protected function sendToExecute($operation)
    {
        try {
            switch ($operation) {
                case 'insert':
                    $this->makeInsertQuery();
                    break;
                case 'select':
                    $this->makeSelectQuery();
                    break;
                case 'update':
                    $this->makeUpdateQuery();
                    break;
                case 'delete':
                    $this->makeDeleteQuery();
                    break;
            }

            $sql = $this->db->query($this->query, $this->binds);
            $sql->setFetchMode(\PDO::FETCH_CLASS, get_class($this->model));

            return $sql;

        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
        }
    }

    /**
     * Set requested relationships.
     *
     * @param array $relations  Contains relationships declared in the incoming Model Class
     * @return Core\QueryBuilder
     */
    public function with($relations = [])
    {
        $this->with = $relations;

        return $this;
    }

    /**
     * Send final UPDATE query for execution.
     *
     * @return void
     */
    public function update()
    {
        $result = $this->set()
            ->where('id', '=', $this->model->id)
            ->sendToExecute('update');
    }

    /**
     * Begin UPDATE query.
     *
     * @return Core\QueryBuilder
     */
    protected function set()
    {
        $this->update .= "UPDATE " . $this->model->table . " SET ";

        $attributes = $this->model->attributes;
        unset($attributes['id']);
        unset($attributes['relations']);

        $insert_columns = array_keys((array) $attributes);
        $insert_values = array_values((array) $attributes);

        $set = [];
        foreach ($insert_columns as $key => $value) {
            $set[] = $value . " = ? ";
        }
        $this->set = implode(', ', $set);
        $this->binds = $insert_values;

        return $this;
    }

    /**
     * Send final "DELETE" query for execution.
     *
     * @return Core\QueryBuilder
     */
    public function delete()
    {
        $result = $this->setDelete()
            ->where('id', '=', $this->model->id)
            ->sendToExecute('delete');
    }

    /**
     * Begin "DELETE" query.
     * This is an UPDATE clause and will only be used to soft delete records.
     *
     * @return Core\QueryBuilder
     */
    protected function setDelete()
    {
        $this->delete = "UPDATE " . $this->model->table . " SET deleted_at = ?";
        $this->binds[] = date('Y-m-d H:i:s');

        return $this;
    }

    /**
     * Create ORDER BY clause for DB.
     *
     * @param string $column    Column of the database on which sort is to be performed
     * @param string $order     Sort Order for Database - ASC|DESC
     * @return Core\QueryBuilder
     */
    public function orderBy($column, $order = 'ASC')
    {
        $this->order_by = " ORDER BY " . $column . " " . $order . " ";

        return $this;
    }
}
