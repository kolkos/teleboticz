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
                $pdo = new PDO('sqlite:database/teleboticz.db');
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

        public function prepareStatementSelect($q, $a_parameters = null) {
            $key_value_array = array();
            $key_value_array['class'] = __CLASS__;
            $key_value_array['method'] = __METHOD__;
            $key_value_array['action'] = "Method called";
            $this->general->logger(3, $key_value_array);

            // ------------ Run Query -----------------
            try {
                // prepare statement
                $stmt = $this->pdo->prepare ( $q );
                
                // loop to bind parameters
                if (! empty ( $a_parameters )) {
                    foreach ( $a_parameters as $placeholder => $values ) {
                        $stmt->bindParam ( $placeholder, $values ['value'], $values ['type'] );
                    }
                }
                
                $stmt->execute ();
                
                $row = $stmt->fetchAll ();
                if (! empty ( $row )) {
                    $key_value_array = array();
                    $key_value_array['class'] = __CLASS__;
                    $key_value_array['method'] = __METHOD__;
                    $key_value_array['result'] = "Query successfull";
                    $this->general->logger(2, $key_value_array);
                    return $row;
                } else {
                    $key_value_array = array();
                    $key_value_array['class'] = __CLASS__;
                    $key_value_array['method'] = __METHOD__;
                    $key_value_array['result'] = "No results found";
                    $this->general->logger(2, $key_value_array);
                    return false;
                }
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

        /*
        array
            key = Titel
            value = database field 
        */

        public function create_results_table($results, $fields){
            $key_value_array = array();
            $key_value_array['class'] = __CLASS__;
            $key_value_array['method'] = __METHOD__;
            $key_value_array['action'] = "Method called";
            $this->general->logger(3, $key_value_array);

            // create the table
            $html  = "<table class='results'>";
            $html .= "<thead>";
            $html .= "<tr>";

            // now loop the fields
            foreach($fields as $title => $column){
                $html .= "<td>" . $title . "</td>";
            }
            // finish the table head
            $html .= "</tr>";
            $html .= "</thead>";

            // create the table body
            $html .= "<tbody>";

            // now loop trough the results
            foreach($results as $row){
                // create the row
                $html .= "<tr>";
                // now loop through the columns
                foreach($fields as $title => $column){
                    $html .= "<td>" . $row[$column] . "</td>";
                }
                $html .= "</tr>";
            }

            // close the table
            $html .= "</tbody>";
            $html .= "</table>";

            $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
            $key_value_array = array();
            $key_value_array['class'] = __CLASS__;
            $key_value_array['method'] = __METHOD__;
            $key_value_array['result'] = "Method finished";
            $key_value_array['execution_time'] = $time;

            $this->general->logger(3, $key_value_array);

            return $html;
        }

    }
?>