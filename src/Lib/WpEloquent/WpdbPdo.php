<?php

namespace OP\Lib\WpEloquent;

class WpdbPdo
{
    public function __construct($wpdb)
    {
        $this->db = $wpdb;
    }

    public function lastInsertId()
    {
        return $this->db->insert_id;
    }
}
