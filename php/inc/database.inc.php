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

                $stmt->debugDumpParams();
                print_r($a_parameters);
                
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
                    $this->general->logger(1, $key_value_array);
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

        public function prepare_results_table($fields, $form_id, $table_id, $filter_class){
            /*
            Requires an array of arrays
            'Title for table'
                'column'   => name of column in database
                'type'     => text / icon
                'icon'     => if type is icon, then this field will contain the icon
                'function' => the javascript function for the icon
            */
            $key_value_array = array();
            $key_value_array['class'] = __CLASS__;
            $key_value_array['method'] = __METHOD__;
            $key_value_array['action'] = "Method called";
            $this->general->logger(3, $key_value_array);

            // create the table
            $html = "<form id='" . $form_id . "'>";
            $html .= "<table class='results' id='" . $table_id . "'>";
            $html .= "<thead>";

            // first row contains the field names
            $html .= "<tr>";
            // now loop the fields
            foreach($fields as $key => $value){
                $width_string = "";
                if(isset($value['width'])){
                    $width_string = " width='" . $value['width'] . "' ";
                }
                $html .= sprintf("<td %s>" . $key . "</td>", $width_string);
            }
            
            $html .= "</tr>";

            // now create the filter fields
            $html .= "<tr>";
            foreach($fields as $key => $value){
                if($value['filter']){
                    $html .= "<td><input type='text' name='" . $value['column'] . "' class='result-filter " . $filter_class . "'/></td>";
                }else{
                    $html .= "<td>&nbsp;</td>";
                }
            }
            $html .= "</tr>";

            // finish the table head
            $html .= "</thead>";
            $html .= "<tbody/>";
            $html .= "</table>";
            $html .= "</form>";
            
            $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
            $key_value_array = array();
            $key_value_array['class'] = __CLASS__;
            $key_value_array['method'] = __METHOD__;
            $key_value_array['result'] = "Method finished";
            $key_value_array['execution_time'] = $time;

            $this->general->logger(3, $key_value_array);

            return $html;

        }

        public function fill_results_table($fields, $results){
            $key_value_array = array();
            $key_value_array['class'] = __CLASS__;
            $key_value_array['method'] = __METHOD__;
            $key_value_array['action'] = "Method called";
            $this->general->logger(3, $key_value_array);

            if(!$results){
                $html = "<tr><td colspan='" . count($fields) . "'>No results found</td></tr>";
                return $html;
            }
            // run the query and handle the results
            $html = "";
            foreach($results as $row){
                // create the row
                $html .= "<tr>";
                // now loop through the columns
                foreach($fields as $key => $value){
                    // split the column name at by a dot, last item in the array is the column name
                    $column_name_array = explode('.', $value['column']);
                    $column_name = $column_name_array[count($column_name_array) -1];
                    
                    
                    if($value['type'] == 'text'){
                        $html .= "<td>" . $row[$column_name] . "</td>";
                    }else{
                        $html .= "<td>";
                        $html .= "<a href='#' onclick='" . $value['function'] . "(\"" . $row[$column_name] . "\")'>";
                        $html .= "<img src='" . $value['icon'] . "'/>";
                        $html .= "</a>";
                        $html .= "</td>";
                    }
                    
                    
                }
                $html .= "</tr>";
            }

            $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
            $key_value_array = array();
            $key_value_array['class'] = __CLASS__;
            $key_value_array['method'] = __METHOD__;
            $key_value_array['result'] = "Method finished";
            $key_value_array['execution_time'] = $time;

            $this->general->logger(3, $key_value_array);

            return $html;

        }

        public function convert_fields_to_query_fields_string($fields){
            // prepare the fields in the query
            $fields_string = "";
            $i = 0;
            foreach($fields as $key => $value){
                if($i != 0){
                    $fields_string .= ", ";
                }
                $fields_string .= $value['column'];
                $i++;
            }
            return $fields_string;
        }

        public function create_jquery_filter_script($filter_class, $form_id, $table_id, $file){
            $html  = "<script>\n";
            $html .= "
                var delay = (function(){
                    var timer = 0;
                    return function(callback, ms){
                        clearTimeout (timer);
                        timer = setTimeout(callback, ms);
                    };
                })();\n";
            $html .= "$('." . $filter_class . "').keyup(function() {\n";
            $html .= "  delay(function(){\n";
            $html .= "    var source = '" . $form_id . "';\n";
            $html .= "    var file = '" . $file . "';\n";
            $html .= "    var target = '#" . $table_id . " > tbody';\n";
            $html .= "    sendFormSimple(source, file, target);\n";
            $html .= "  }, 1000 );\n";
            $html .= "});\n";
            $html .= "</script>\n";

            return $html;

        }

        public function prepare_query_domoticz_call_config($post){
            $key_value_array = array();
            $key_value_array['class'] = __CLASS__;
            $key_value_array['method'] = __METHOD__;
            $key_value_array['action'] = "Method called";
            $this->general->logger(3, $key_value_array);

            // first prepare the fields
            $fields = array(
                'Device Type' => array(
                    'column' => "domoticz_device_types.name",
                    'type' => "text",
                    'filter' => TRUE
                ),
                'Edit' => array(
                    'column' => "domoticz_call_config.domoticz_device_type_id",
                    'type' => "icon",
                    'icon' => "img/edit.png",
                    'function' => "edit_domoticz_call_config_device",
                    'width' => '100px',
                    'filter' => FALSE
                ),
                'Delete' => array(
                    'column' => "domoticz_call_config.domoticz_device_type_id",
                    'type' => "icon",
                    'icon' => "img/trash.png",
                    'function' => "delete_domoticz_call_config_device",
                    'width' => '100px',
                    'filter' => FALSE
                ),
            );
            // prepare the fields in the query
            $fields_string = $this->convert_fields_to_query_fields_string($fields);
            
            if(!empty($post)){
                $q = sprintf("SELECT DISTINCT %s FROM domoticz_call_config, domoticz_device_types
                        WHERE domoticz_call_config.domoticz_device_type_id = domoticz_device_types.id
                        AND domoticz_device_types.name LIKE :device_type
                        ORDER BY name ASC;", $fields_string);
                $a_params = array(
                    ':device_type' => array(
                        'value' => $post['domoticz_device_types_name'] . "%",
                        'type' => PDO::PARAM_STR
                    ),
                );
            }else{
                $q = sprintf("SELECT DISTINCT %s FROM domoticz_call_config, domoticz_device_types 
                                WHERE domoticz_call_config.domoticz_device_type_id = domoticz_device_types.id
                                ORDER BY name ASC;", $fields_string);
                $a_params = array();
            }

            // now add the prepared values to an array
            $results['fields'] = $fields;
            $results['query'] = $q;
            $results['a_params'] = $a_params;
            $results['form_id'] = "domoticz_call_config_form";
            $results['table_id'] = "domoticz_call_config_table";
            $results['filter_class'] = "domoticz_call_config_filter";
            $results['results_file'] = "php/domoticz_call_config_results.php";

            $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
            $key_value_array = array();
            $key_value_array['class'] = __CLASS__;
            $key_value_array['method'] = __METHOD__;
            $key_value_array['result'] = "Method finished";
            $key_value_array['execution_time'] = $time;

            $this->general->logger(3, $key_value_array);

            return $results;
        }

        public function prepare_query_domoticz_device_types($post){
            $key_value_array = array();
            $key_value_array['class'] = __CLASS__;
            $key_value_array['method'] = __METHOD__;
            $key_value_array['action'] = "Method called";
            $this->general->logger(3, $key_value_array);

            // first prepare the fields
            $fields = array(
                'ID' => array(
                    'column' => "id",
                    'type' => "text",
                    'filter' => FALSE
                ),
                'Name' => array(
                    'column' => "name",
                    'type' => "text",
                    'filter' => TRUE
                ),
                'Description' => array(
                    'column' => "description",
                    'type' => "text",
                    'filter' => TRUE
                ),
                'Edit' => array(
                    'column' => "id",
                    'type' => "icon",
                    'icon' => "img/edit.png",
                    'function' => "edit_domoticz_device_type",
                    'width' => '100px',
                    'filter' => FALSE
                ),
                'Delete' => array(
                    'column' => "id",
                    'type' => "icon",
                    'icon' => "img/trash.png",
                    'function' => "delete_domoticz_device_type",
                    'width' => '100px',
                    'filter' => FALSE
                ),
            );
            // prepare the fields in the query
            $fields_string = $this->convert_fields_to_query_fields_string($fields);
            
            if(!empty($post)){
                $q = sprintf("SELECT %s FROM domoticz_device_types
                        WHERE name LIKE :name
                        AND description LIKE :description
                        ORDER BY name ASC;", $fields_string);
                $a_params = array(
                    ':name' => array(
                        'value' => $post['name'] . "%",
                        'type' => PDO::PARAM_STR
                    ),
                    ':description' => array(
                        'value' => $post['description'] . "%",
                        'type' => PDO::PARAM_STR
                    ),
                );
            }else{
                $q = sprintf("SELECT %s FROM domoticz_device_types ORDER BY name ASC;", $fields_string);
                $a_params = array();
            }

            // now add the prepared values to an array
            $results['fields'] = $fields;
            $results['query'] = $q;
            $results['a_params'] = $a_params;
            $results['form_id'] = "domoticz_device_types_form";
            $results['table_id'] = "domoticz_device_types_table";
            $results['filter_class'] = "domoticz_device_type_filter";
            $results['results_file'] = "php/domoticz_device_types_results.php";

            $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
            $key_value_array = array();
            $key_value_array['class'] = __CLASS__;
            $key_value_array['method'] = __METHOD__;
            $key_value_array['result'] = "Method finished";
            $key_value_array['execution_time'] = $time;

            $this->general->logger(3, $key_value_array);

            return $results;
        }

        public function prepare_query_domoticz_excluded_items($post){
            $key_value_array = array();
            $key_value_array['class'] = __CLASS__;
            $key_value_array['method'] = __METHOD__;
            $key_value_array['action'] = "Method called";
            $this->general->logger(3, $key_value_array);

            // first prepare the fields
            $fields = array(
                'ID' => array(
                    'column' => "domoticz_excluded_items.id",
                    'type' => "text",
                    'filter' => FALSE
                ),
                'Device type' => array(
                    'column' => "domoticz_device_types.name",
                    'type' => "text",
                    'filter' => TRUE
                ),
                'IDX' => array(
                    'column' => "domoticz_excluded_items.idx",
                    'type' => "text",
                    'filter' => FALSE
                ),
                'Description' => array(
                    'column' => "domoticz_excluded_items.description",
                    'type' => "text",
                    'filter' => TRUE
                ),
                'Edit' => array(
                    'column' => "domoticz_excluded_items.id",
                    'type' => "icon",
                    'icon' => "img/edit.png",
                    'function' => "edit_domoticz_device_type",
                    'width' => '100px',
                    'filter' => FALSE
                ),
                'Delete' => array(
                    'column' => "domoticz_excluded_items.id",
                    'type' => "icon",
                    'icon' => "img/trash.png",
                    'function' => "delete_domoticz_device_type",
                    'width' => '100px',
                    'filter' => FALSE
                ),
            );
            // prepare the fields in the query
            $fields_string = $this->convert_fields_to_query_fields_string($fields);
            
            if(!empty($post)){
                $q = sprintf("SELECT %s FROM domoticz_device_types, domoticz_excluded_items
                        WHERE domoticz_excluded_items.domoticz_device_type_id = domoticz_device_types.id
                        AND domoticz_device_types.name LIKE :name
                        AND domoticz_excluded_items.description LIKE :description
                        ORDER BY name ASC;", $fields_string);
                $a_params = array(
                    ':name' => array(
                        'value' => $post['domoticz_device_types_name'] . "%",
                        'type' => PDO::PARAM_STR
                    ),
                    ':description' => array(
                        'value' => $post['domoticz_excluded_items_description'] . "%",
                        'type' => PDO::PARAM_STR
                    ),
                );
            }else{
                $q = sprintf("SELECT %s FROM domoticz_device_types, domoticz_excluded_items WHERE domoticz_excluded_items.domoticz_device_type_id = domoticz_device_types.id ORDER BY name ASC;", $fields_string);
                $a_params = array();
            }

            // now add the prepared values to an array
            $results['fields'] = $fields;
            $results['query'] = $q;
            $results['a_params'] = $a_params;
            $results['form_id'] = "domoticz_excluded_items_form";
            $results['table_id'] = "domoticz_excluded_items_table";
            $results['filter_class'] = "domoticz_excluded_items_filter";
            $results['results_file'] = "php/domoticz_excluded_items_results.php";

            $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
            $key_value_array = array();
            $key_value_array['class'] = __CLASS__;
            $key_value_array['method'] = __METHOD__;
            $key_value_array['result'] = "Method finished";
            $key_value_array['execution_time'] = $time;

            $this->general->logger(3, $key_value_array);

            return $results;
        }
    }
?>