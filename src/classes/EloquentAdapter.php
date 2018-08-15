<?php
namespace Feathers;

use \Illuminate\Database\Eloquent\Model;
use \Feathers\ModelAdapter;
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
*/
class EloquentAdapter extends ModelAdapter
{
	protected $model;
	protected $attributes = [];
	protected $backupModel;

	public function __construct(Model $model)
	{
		$this->model = $model;
		$this->backupModel = clone $model;
	}
	public function restore()
	{
		$this->model = clone $this->backupModel;
	}
	public function find(array $query = [])
	{
		$this->querying($query);
		$result = $this->model->get();
		//print_r($result->toArray());
		//return [];
		$result = $result->toArray();
		$this->restore();
		return $result;
	}
	public function remove(int $id, array $query = [])
	{
		$this->__equal("id",$id);
		$this->querying($query);
		$result = $this->model->get();
		$this->model->delete();
		$result = $result->toArray();
		$this->restore();
		return $result;
	}
	public function get(int $id, array $query = [])
	{
		$this->__equal("id",$id);
		$this->querying($query);
		$result = $this->model->get();//not found - null
		$result = $result->toArray();
		$this->restore();
		return $result;
	}
	public function create(array $data)
	{
		$this->model->fill($data);
		$result = $this->model->save();
		if (!$result) {
			return $result;
		}
		$result = $this->model->toArray();
		$this->restore();
		return $result;
		//var_dump($result);
		//$this->model->restore();
		//$this->model
	}
	public function patch(int $id, array $data, array $query = [])
	{
		//$this->__equal("id",$id);
		$this->querying($query);
		$model = $this->model->find($id);
		//print_r(sizeof($model));
		$model->fill($data);
		$model->save();
		$result = $model->toArray();
		$this->restore();
		return $result;
	}
	public function update(int $id, array $data, array $query = [])
	{
		return $this->patch($id, $data, $query);
		//$this->__equal("id",$id);
		//$this->querying($query);
		//$model = $this->model->update($data);
		//ret
	}

	protected function _limit(int $count)
	{
		$this->model = $this->model->take($count);
	}
	protected function _skip(int $count)
	{
		$this->model = $this->model->skip($count);
	}
	protected function _sort(string $column, int $type)
	{
		if($type === -1){
			$this->model = $this->model->orderBy($name, 'desc');
		}elseif($type === 1) {
			$this->model = $this->model->orderBy($name, 'asc');
		}
	}
	protected function _select(array $select)
	{
		$this->model = $this->model->select($select);
	}
	protected function _in(string $column, array $in)
	{
		$this->model = $this->model->whereIn($column, $in);
	}
	protected function _nin(string $column, array $nin)
	{
		$this->model = $this->model->whereNotIn($column, $in);
	}
	protected function _lt(string $column, int $lt)
	{
		$this->model = $this->where($column, ">", $lt);
	}
	protected function _lte(string $column, int $lte)
	{
		$this->model = $this->where($column, ">=", $lte);
	}
	protected function _gt(string $column, int $gt)
	{
		$this->model = $this->where($column, "<", $gt);
	}
	protected function _gte(string $column, int $gte)
	{
		$this->model = $this->where($column, "<=", $gte);
	}	
	protected function _ne(string $column, int $ne)
	{
		$this->model = $this->where($column, "!=", $ne);
	}	
	// protected function _or(array $columns)
	// {

	// }

	protected function __equal(string $key, $value)
	{
		$this->model = $this->model->where($key, "=", $value);
	}

}