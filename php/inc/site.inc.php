<?php
    require_once 'general.inc.php';
    require_once 'database.inc.php';
    class Site{
        public $general;
        public $database;

        public function __construct() {
            // load the general class
            $this->general = new General();

            // load the database class
            $this->database = new Database();
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
            $key_value_array = array();
            $key_value_array['class'] = __CLASS__;
            $key_value_array['method'] = __METHOD__;
            $key_value_array['action'] = "Method called";
            $this->general->logger(3, $key_value_array);
            
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

            $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
            $key_value_array = array();
            $key_value_array['class'] = __CLASS__;
            $key_value_array['method'] = __METHOD__;
            $key_value_array['result'] = "Method finished";
            $key_value_array['execution_time'] = $time;
            $key_value_array['fields_string'] = $fields_string;
            $this->general->logger(3, $key_value_array);

            return $fields_string;
        }

        public function create_jquery_filter_script($filter_class, $form_id, $table_id, $file){
            $key_value_array = array();
            $key_value_array['class'] = __CLASS__;
            $key_value_array['method'] = __METHOD__;
            $key_value_array['action'] = "Method called";
            $this->general->logger(3, $key_value_array);
            
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

            $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
            $key_value_array = array();
            $key_value_array['class'] = __CLASS__;
            $key_value_array['method'] = __METHOD__;
            $key_value_array['result'] = "Method finished";
            $key_value_array['execution_time'] = $time;
            $this->general->logger(3, $key_value_array);

            return $html;

        }

        public function create_form($form_name, $title, $form_elements, $file){
            /*
            array
                array
                    'type' => text/textarea/select
                    'name' => name of the element
                    'id' => id of the element
                    'label' => label for this element
                    'value' => if set, this field will contain the value of this field
                    'options' => if type is select, this item contains the options, seperated by a comma (,)
            */
            $key_value_array = array();
            $key_value_array['class'] = __CLASS__;
            $key_value_array['method'] = __METHOD__;
            $key_value_array['action'] = "Method called";
            $key_value_array['form_name'] = $form_name;
            $key_value_array['title'] = $title;
            $key_value_array['file'] = $file;
            $this->general->logger(3, $key_value_array);
            
            
            
            $form_id   = $form_name . "_form";
            $button_id = $form_name . "_button";
            $result_id = $form_name . "_result";
            
            // first create the div for the response
            $html  = "<div id='" . $result_id . "'></div>";
            // now create the form
            $html .= "<form id='" . $form_id . "'>\n";
            $html .= "<table class='form'>\n";
            $html .= "<thead>\n";
            $html .= "<tr><td colspan='2'>" . $title . "</td></tr>\n";
            $html .= "</thead>\n";
            $html .= "<tbody>\n";
            // loop through the form elements
            foreach($form_elements as $form_row){
                // create a row
                $html .= "<tr>";
                // create the label
                $html .= "<td><label for='" . $form_row['id'] . "'>" . $form_row['label'] . '</label></td>';

                // this part depends on the type
                $html .= "<td>";
                switch($form_row['type']){
                    case 'text':
                    case 'pass':
                        $html_buffer = "<input type='" . $form_row['type'] . "' id='" . $form_row['id'] . "' name='" . $form_row['name'] . "' %s %s/>";
                        $value = "";
                        if(isset($form_row['value'])){
                            $value = "value='" . $form_row['value'] . "'";
                        }
                        $required = "";
                        if(isset($form_row['required'])){
                            $required = "class='required'";
                        }
                        
                        break;
                    case 'textarea':
                        $html_buffer = "<textarea id='" . $form_row['id'] . "' name='" . $form_row['name'] . "' %s>%s</textarea>";
                        $value = "";
                        if(isset($form_row['value'])){
                            $value = $form_row['value'];
                        }
                        $required = "";
                        if(isset($form_row['required'])){
                            $required = "class='required'";
                        }
                        break;
                }

                $html .= sprintf($html_buffer, $value, $required);
                
                $html .= "</td>";
                // end row
                $html .= "</tr>";
            }

            // add the button
            $html .= "<tr><td colspan='2'><button type='button' id='" . $button_id . "'>Submit</button></td></tr>";

            $html .= "</tbody>\n";
            $html .= "</table>\n";
            $html .= "</form>\n";
            
            // now create the jQuery script to handle the form
            $html .= "<script>\n";
            $html .= "$('#" . $button_id . "').click(function(){\n";
            $html .= "  var check = checkRequiredFields('#" . $form_id . "');\n";
            $html .= "  if(check){\n";
            $html .= "    console.log('check ok');";
            $html .= "    var source = '" . $form_id . "';";
            $html .= "    var file = '" . $file . "';";
            $html .= "    var target = '#" . $result_id . "';";
            $html .= "    sendFormSimple(source, file, target);\n";
            $html .= "  }\n";
            $html .= "});\n";
            $html .= "</script>\n";
            

            $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
            $key_value_array = array();
            $key_value_array['class'] = __CLASS__;
            $key_value_array['method'] = __METHOD__;
            $key_value_array['result'] = "Method finished";
            $key_value_array['execution_time'] = $time;
            $this->general->logger(3, $key_value_array);
            
            return $html;

        }
        
        public function handle_form_simple($query, $a_parameters, $results_file, $results_target_id){
            $key_value_array = array();
            $key_value_array['class'] = __CLASS__;
            $key_value_array['method'] = __METHOD__;
            $key_value_array['action'] = "Method called";
            $key_value_array['query'] = $query;
            $this->general->logger(3, $key_value_array);
            
            $result = $this->database->prepareStatementDo($query, $a_parameters);
            
            $html  = "<div class='alert %s'>\n";
            $html .= "<table>\n";
            $html .= "<tr>\n";
            $html .= "<td><img class='statusIcon' src='%s'/></td>\n";
            $html .= "<td>%s</td>\n";
            $html .= "</tr>\n";
            $html .= "</table>\n";
            $html .= "</div>\n";
            
            // now create the script to fade out the form
            // and reload the results
            $html .= "<script>";
            $html .= "  $(function() {\n";
            $html .= "    var source='%s';\n";
            $html .= "    var target='%s';\n";
            $html .= "    setTimeout(function(){\n";
            $html .= "      loadPageSimple(source, target);\n";
            $html .= "      $('#overlayBG').fadeOut();\n";
            $html .= "      $('#overlayWindow').fadeOut();\n";
            $html .= "    }, 5000);\n";
            $html .= "  });\n";
            $html .= "</script>\n";
            
            if($result){
                $class = "success";
                $message = "Task executed successfully.";
                $img = "img/success.png";
            }else{
                $class = "error";
                $message = $result;
                $img = "img/multiply.png";
            }
            
            $html = sprintf($html, $class, $img, $message, $results_file, $results_target_id);
            
            $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
            $key_value_array = array();
            $key_value_array['class'] = __CLASS__;
            $key_value_array['method'] = __METHOD__;
            $key_value_array['result'] = "Method finished";
            $key_value_array['execution_time'] = $time;
            $this->general->logger(3, $key_value_array);
            
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
        
        public function domoticz_device_type_add_handler($post){
            
        }
    }
?>