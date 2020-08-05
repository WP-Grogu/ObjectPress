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
}
