<?php
    class Database {
        public $pdo;
	
        public function __construct() {
            // automatic load the pdo connection
            $this->pdo = $this->PDOConnectDB ();
        }
        
        function PDOConnectDB() {
            try{
                $pdo = new PDO('sqlite:../database/teleboticz.db');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $pdo;
            } catch ( PDOException $e ) {
			    echo $e->getMessage ();
			    return $e->getMessage ();
		    }
            
        }

        public function prepareStatementSelect($q, $a_parameters = null) {
            // ------------ Run Query -----------------
            echo $q . "<br />";
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
                    #$this->logToFile ( 2, "dblog.txt", "Query OK" );
                    return $row;
                } else {
                    #$this->logToFile ( 1, "dblog.txt", "Error running query" );
                    return false;
                }
            } catch ( PDOException $e ) {
                echo $e->getMessage ();
                #$this->logToFile ( 1, "dblog.txt", $e->getMessage () );
                return $e->getMessage ();
            }
        }
    }
?>