<?php
namespace Models;
use \Illuminate\Database\Eloquent\Model;
class Order extends Model
{
	protected $connection = 'payment';
	protected $table = "orders";
	public $timestamps = true;
	protected $fillable = ['name','client_id',"status_id"];
	const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    public $attrs = [];
}