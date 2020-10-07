<?php

namespace OP\Database\Interfaces;

interface MigrationSchema
{
    public function up();
    public function down();
    public function getTableName();
}