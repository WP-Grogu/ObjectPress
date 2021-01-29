<?php

namespace OP\Database\Interfaces;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.0.5
 * @access   public
 * @since    1.0.5
 */
interface MigrationSchema
{
    public function up();
    public function down();
    public function getTableName();
}
