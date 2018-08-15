<?php
namespace Feathers;
use \Illuminate\Database\Eloquent\Model as DBModel;

class Model extends DBModel
{
	protected $attributes = [];
		protected $connection = 'console';
	protected $table = "clients_booking_dbk39";
	public $timestamps = false;
}