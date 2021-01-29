<?php

namespace OP\Database;

use As247\WpEloquent\Capsule\Manager as Capsule;
use Exception;
use OP\Database\Interfaces\MigrationSchema;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.0.5
 * @access   public
 * @since    1.0.5
 */
abstract class Migration implements MigrationSchema
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        if (!isset($this->table_name) || empty($this->table_name)) {
            throw new Exception(
                sprintf("OP : Migration : You must define table_name protected variable. [%s]", get_class())
            );
        }
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Capsule::schema()->create($this->table_name, function ($table) {
            $table->increments('id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Capsule::schema()->drop($this->table_name);
    }

    /**
     * Get the migration associated table name.
     */
    public function getTableName()
    {
        return $this->table_name;
    }
}
