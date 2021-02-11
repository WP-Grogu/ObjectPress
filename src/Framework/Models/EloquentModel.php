<?php

namespace OP\Framework\Models;

use As247\WpEloquent\Database\Eloquent\Model;

abstract class EloquentModel extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'example_table';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * The attributes that are protected from mass assignation.
     *
     * @var array
     */
    protected $protected = [];
}
