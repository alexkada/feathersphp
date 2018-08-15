<?php
namespace Feathers;


/*
special query 
$limit
$skip
$sort
$select
$in, $nin
$lt $lte
$gt $gte
$ne
$or


{
}

*/
abstract class ModelAdapter
{
	protected $model;
	
	abstract public function find(array $query = []);
	abstract public function remove(int $id, array $query = []);
	abstract public function get(int $id, array $query = []);
	abstract public function create(array $data);
	abstract public function patch(int $id, array $data, array $query = []);
	abstract public function update(int $id, array $data, array $query = []);

	abstract protected function _limit(int $count);
	abstract protected function _skip(int $count);
	abstract protected function _sort(string $column, int $type);
	abstract protected function _select(array $select);
	abstract protected function _in(string $column, array $in);
	abstract protected function _nin(string $column, array $nin);
	abstract protected function _lt(string $column, int $lt);
	abstract protected function _lte(string $column, int $lte);
	abstract protected function _gt(string $column, int $gt);
	abstract protected function _gte(string $column, int $gte);	
	abstract protected function _ne(string $column, int $ne);	
	//abstract protected function _or(array $columns);
	abstract protected function __equal(string $key, $value);

	protected function querying(array $query, string $column = null)
	{
		if (sizeof($query) === 0) {
			return;
		}
		foreach ($query as $key => $value) {
			if(strpos($key, "$") !== false) {
				$method = "_".str_replace("$","",$key);
				if(method_exists($this, $method)) {
					call_user_method_array($method,$this,$value);
				}
			}else {
				if(is_array($value)) {
					$this->querying($value, $key);
				} else {
					$this->__equal($key, $value);
				}
			}
		}
	}
}