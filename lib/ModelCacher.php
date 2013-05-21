<?php

/**
 * @package ActiveRecord
 */
namespace ActiveRecord;


class ModelCacher
{
	public $models;
	public function __construct()
	{
		
	} 
	
	public function has_model($model)
	{
		$attributes = $model->attributes();
		$pk = $model->get_primary_key(true);
		if($pk && isset($attributes[$pk]))
			return isset($this->models[$model->$pk]);
		else
			return false;
	}
	
	public function remove_model($model)
	{
		if($this->has_model($model))
			unset($this->models[$model->id]);
		return $model;
	}
	
	public function retrieve_model($model)
	{
		return $this->models[$model->id];
	}
	
	public function add_model($model)
	{
		$pk = $model->get_primary_key(true);
		$attributes = $model->attributes();
		if($pk && isset($attributes[$pk]))
		{
			return $this->models[$model->id] = $model;
		}
		else
			return null;
	}
	
}
