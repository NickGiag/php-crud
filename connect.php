<?php
    
    class Connection {
        
        protected $database;
            
        public function __construct($MDB_USER, $MDB_PASS, $ATLAS_CLUSTER_SRV) {
            try {
                $client = new MongoDB\Client('mongodb+srv://'.$MDB_USER.':'.$MDB_PASS.'@'.$ATLAS_CLUSTER_SRV.'/?retryWrites=true&w=majority');
                $this->database = $client->announcements;
                error_log("Connection to database announcements");
            }
            catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
                error_log("Problem in connection with database announcements".$e);
            }
        }

        public function connect_to_department(){
            $collection = $this->database->department;
            return $collection;
        }

        public function connect_to_user_category(){
            $collection = $this->database->user_category;
            return $collection;
        }

        public function connect_to_user(){
            $collection = $this->database->users;
            return $collection;
        }

        public function connect_to_announcement(){
            $collection = $this->database->announcement;
            return $collection;
        }
}
?>
