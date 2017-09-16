<?php
    require_once 'site.inc.php';
    
    class Config extends Site{
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