<?php

class Db {
    
    var $dbh;
    
    public function __construct($db_host, $db_name, $db_user, $db_pass)
    {
        try {
            $this->dbh = new PDO("mysql:host=${db_host}; dbname=${db_name}", $db_user, $db_pass);
            $this->dbh->query('SET NAMES UTF-8');
        }
        catch (PDOException $e ){
            echo $e;
        }
    }
    
    public function __destruct()
    {
        $this->dbh = null;
    }
    
    public function query($query)
    {
        return $this->dbh->query($query);
    }
    
    public function updateSite($params)
    {
        $query  = '';
        $query .= 'UPDATE site';
        $query .= 'SET';
        $query .= 'name = "'. $params['site_name'] .'",';
        $query .= 'url  = "'. $params['host'] .'",';
        $query .= 'comment = "'. $params['site_comment'] .'"';
        
        return $this->query($query);
    }
    
}
