<?php

namespace OP\Lib\WpEloquent;

class Connection extends \AmphiBee\Eloquent\Connection
{
    /**
     * Make sur that the gven table name has prefix, and return it.
     *
     * @param string $table_name
     * @return string
     */
    public function prefixTable($table_name)
    {
        if (strpos($table_name, $this->db->prefix) !== 0) {
            $table_name = $this->db->prefix . $table_name;
        }

        return $table_name;
    }
}
