<?php
    require_once 'general.inc.php';
    class Database {
        public $pdo;
        public $general;
	
        public function __construct() {
            // load the general class
            $this->general = new General();
            // automatic load the pdo connection
            $this->pdo = $this->PDOConnectDB ();
        }
        
        function PDOConnectDB() {
            $key_value_array = array();
            $key_value_array['class'] = __CLASS__;
            $key_value_array['method'] = __METHOD__;
            $key_value_array['action'] = "Method called";
            $this->general->logger(3, $key_value_array);

            try{
                $pdo = new PDO('sqlite:' . $_SERVER['DOCUMENT_ROOT'] . '/database/teleboticz.db');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $key_value_array = array();
                $key_value_array['class'] = __CLASS__;
                $key_value_array['method'] = __METHOD__;
                $key_value_array['result'] = "Succesfully connected";
                $this->general->logger(2, $key_value_array);
                
                return $pdo;
            } catch ( PDOException $e ) {
			    echo $e->getMessage ();
                $key_value_array = array();
                $key_value_array['class'] = __CLASS__;
                $key_value_array['method'] = __METHOD__;
                $key_value_array['error'] = $e->getMessage();
                $this->general->logger(0, $key_value_array);
			    return $e->getMessage();
		    }
        }
        
        function prepareStatementDo($q, $a_parameters = null) {
            $q_for_log = preg_replace( "/\r|\n/", "", $q);
            $q_for_log = preg_replace( "/\s{2,}/", " ", $q_for_log);
            
            $key_value_array = array();
            $key_value_array['class'] = __CLASS__;
            $key_value_array['method'] = __METHOD__;
            $key_value_array['action'] = "Method called";
            $key_value_array['query'] = $q_for_log;
            $this->general->logger(3, $key_value_array);
            
            if (empty ( $a_parameters )) {
                echo "No parameters received";
                $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
                $key_value_array = array();
                $key_value_array['class'] = __CLASS__;
                $key_value_array['method'] = __METHOD__;
                $key_value_array['error'] = "No parameters received";
                $key_value_array['query'] = $q_for_log;
                $key_value_array['execution_time'] = $time;
                $this->general->logger(1, $key_value_array);
                return false;
            }
            
            // ------------ Run Query -----------------
            
            try {
                // prepare statement
                $stmt = $this->pdo->prepare ( $q );
                
                // loop to bind parameters
                foreach ( $a_parameters as $placeholder => $values ) {
                    $stmt->bindParam ( $placeholder, $values ['value'], $values ['type'] );
                }
                
                $stmt->execute ();
                
                $stmt = null;
                
                $key_value_array = array();
                $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
                $key_value_array['class'] = __CLASS__;
                $key_value_array['method'] = __METHOD__;
                $key_value_array['result'] = "Query OK";
                $key_value_array['query'] = $q_for_log;
                $key_value_array['execution_time'] = $time;
                $this->general->logger(2, $key_value_array);
                return true;
            } catch ( PDOException $e ) {
                $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
                $key_value_array = array();
                $key_value_array['class'] = __CLASS__;
                $key_value_array['method'] = __METHOD__;
                $key_value_array['result'] = "Query Failed";
                $key_value_array['error'] = $e->getMessage();
                $key_value_array['execution_time'] = $time;
                $this->general->logger(0, $key_value_array);
                echo $e->getMessage ();
                return $e->getMessage ();
            }
        }

        public function prepareStatementSelect($q, $a_parameters = null) {
            // fix query for logging purposes
            $q_for_log = preg_replace( "/\r|\n/", "", $q);
            $q_for_log = preg_replace( "/\s{2,}/", " ", $q_for_log);
            
            $key_value_array = array();
            $key_value_array['class'] = __CLASS__;
            $key_value_array['method'] = __METHOD__;
            $key_value_array['action'] = "Method called";
            $key_value_array['query'] = $q_for_log;
            $this->general->logger(3, $key_value_array);

            //print "<p>" . $q . "</p>";
            //print_r($a_parameters);

            // ------------ Run Query -----------------
            try {
                // prepare statement
                $stmt = $this->pdo->prepare($q);
                
                // loop to bind parameters
                if (! empty ( $a_parameters )) {
                    foreach ( $a_parameters as $placeholder => $values ) {
                        $stmt->bindParam ( $placeholder, $values ['value'], $values ['type'] );
                    }
                }
                
                $stmt->execute ();
                
                $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
                $key_value_array = array();
                $key_value_array['class'] = __CLASS__;
                $key_value_array['method'] = __METHOD__;
                $key_value_array['result'] = "Query successfull";
                $key_value_array['execution_time'] = $time;
                $key_value_array['number_of_rows'] = count($row);
                $this->general->logger(2, $key_value_array);
                
                return $row;
                
            } catch ( PDOException $e ) {
                echo $e->getMessage ();
                $key_value_array = array();
                $key_value_array['class'] = __CLASS__;
                $key_value_array['method'] = __METHOD__;
                $key_value_array['error'] = $e->getMessage();
                $this->general->logger(0, $key_value_array);
                return $e->getMessage ();
            }
        }

    }
?>