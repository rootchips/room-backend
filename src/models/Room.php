<?php
use Illuminate\Database\Eloquent\Model;

class Room extends Model {
    protected $table = 'rooms';
    protected $fillable = ['name', 'description', 'capacity'];
}
