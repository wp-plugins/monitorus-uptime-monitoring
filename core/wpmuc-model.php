<?php
class WPMUC_Model
{
    protected $wpdb = null;
    
    function __construct()
    {
        global $wpdb;

        $this->wpdb = $wpdb;
    }
}
?>