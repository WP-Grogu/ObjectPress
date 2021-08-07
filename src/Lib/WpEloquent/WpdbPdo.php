<?php

namespace OP\Lib\WpEloquent;

class WpdbPdo
{
    /**
     * DB Instance
     */
    protected $db;
    
    public function __construct($wpdb)
    {
        $this->db = $wpdb;
    }

    public function lastInsertId()
    {
        return $this->db->insert_id;
    }
    
    public function prefix()
    {
        return $this->db->prefix;
    }
}
