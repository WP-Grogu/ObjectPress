<?php

namespace OP\Framework\Helpers;

class DatabaseHelper
{
    /**
     * Check if a table exists.
     *
     * @param string $table  Name of the table to check on
     * @param bool   $prefix Set false if you don't want the function to prefix your table name with wp prefix
     *
     * @return bool
     */
    public static function tableExists(string $table, bool $prefix = true)
    {
        global $wpdb;

        $table_name = $prefix ? $wpdb->base_prefix . $table : $table;

        $query = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table_name));

        return $wpdb->get_var($query) == $table_name;
    }

    /**
     * Return tables's columns.
     *
     * @param string $table  Table's name
     * @param bool   $prefix Set false if you don't want the function to prefix your table name with wp prefix
     *
     * @return array
     */
    public static function getTableColumns(string $table, bool $prefix = true)
    {
        global $wpdb;

        $table_name = $prefix ? $wpdb->base_prefix . $table : $table;

        $query = "SELECT *
                  FROM INFORMATION_SCHEMA.COLUMNS
                  WHERE TABLE_NAME = N'{$table_name}'";

        $results = $wpdb->get_results($query);

        $results = array_map(function ($e) {
            return $e->COLUMN_NAME;
        }, $results);

        return $results;
    }
}
