<?php
    class General{
        public $log_raw;
        public $log_html;
        public $config;
        public $navigation;

        public function __construct() {
		    $this->config = parse_ini_file(realpath("config/config.ini"), true);
            $this->declare_navigation();
        }

        public function declare_navigation(){
            $this->navigation = array (
				'Home' => array (
                    'file' => "php/home.php",
                    'title' => "Home"
				),
				'Log' => array (
                    'file' => "php/log.php",
                    'title' => "Log"
				),
                'Config' => array (
                    'file' => "php/config.php",
                    'title' => "Config"
				),
                'Queries' => array (
                    'file' => "php/queries.php",
                    'title' => "Queries"
				),
                'About' => array (
                    'file' => "php/about.php",
                    'title' => "About"
				),
                'Help' => array (
                    'file' => "php/help.php",
                    'title' => "Help"
				),
		    );
        }

        public function build_menu($current_page){
            $key_value_array = array();
            $key_value_array['class'] = __CLASS__;
            $key_value_array['method'] = __METHOD__;
            $key_value_array['action'] = "Method called";
            $this->logger(3, $key_value_array);

            $menu = "<ul>";
            # loop through the navigation items
            foreach($this->navigation as $key => $value){
                $class = "";
                if($key == $current_page){
                    $class = " class='active' ";
                }
                $menu .= "<li><a " . $class . " href='?page=" . $key . "'>" . $key . "</a></li>";
            }
            $menu .= "</ul>";

            $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
            
            $key_value_array = array();
            $key_value_array['class'] = __CLASS__;
            $key_value_array['method'] = __METHOD__;
            $key_value_array['result'] = "Method finished";
            $key_value_array['execution_time'] = $time;

            $this->logger(3, $key_value_array);

            return $menu;
        }

        public function pageSwitcher($page) {
            $key_value_array = array();
            $key_value_array['class'] = __CLASS__;
            $key_value_array['method'] = __METHOD__;
            $key_value_array['action'] = "Method called";
            $key_value_array['page'] = $page;
            $this->logger(3, $key_value_array);
            
            // display page title
            echo "<h1>" . $this->navigation[$page]['title'] . "</h1>";
            
            // get the required file
            $file = $this->navigation[$page]['file'];
            
            $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];

            $key_value_array = array();
            $key_value_array['class'] = __CLASS__;
            $key_value_array['method'] = __METHOD__;
            $key_value_array['result'] = "Method finished";
            $key_value_array['file'] = $file;
            $key_value_array['execution_time'] = $time;

            $this->logger(3, $key_value_array);

            require_once $file;
        }

        public function get_log_contents($offset=-1){

            $logfile = realpath('../logs/teleboticz.log');
            $handle = fopen($logfile, 'r');
            
            $data = stream_get_contents($handle, -1, $offset);
            $this->log_raw = $data;

            fseek($handle, 0, SEEK_END);
            $new_offset = ftell($handle);

            $data = $this->format_log_contents($data);
            return $new_offset;

        }
        public function format_log_contents($log_contents){
            $formatted_log = "";
            
            // split the contents at lines
            $lines = split("\n",$log_contents);
            // loop the lines
            foreach($lines as $line){
                // now split the line in key/value pairs
                $key_value_pairs = split(', ', $line);
                // now loop the key value pairs and create a named array
                $values = array();
                foreach($key_value_pairs as $key_value_pair){
                    #list($key, $value) = explode("=", $key_value_pair);
                    list($key, $value) = array_pad(explode("=", $key_value_pair),2, null);
                    // strip the quotes from the value
                    $value = str_replace('"', '', $value);
                    $values[$key] = $value;
                }
                // check if the priority is given
                if(isset($values['priority'])){
                    switch($values['priority']){
                        case 'Error':
                            $color = '#ff3300';
                            break;
                        case 'Warning':
                            $color = '#ff9900';
                            break;
                        case 'Info':
                            $color = '#33cc33';
                            break;
                        default:
                            $color = '#0099ff';
                    }
                    // now replace the value in the line
                    $replacement = "<span style='color: " . $color . "'>" . $values['priority'] . "</span>";
                    $line = str_replace($values['priority'], $replacement, $line);
                }
                // color the source key
                if(isset($values['source'])){
                    switch($values['source']){
                        case 'python':
                            $color = '#ffcc66';
                            break;
                        default:
                            $color = '#6666ff';
                    }
                    // now replace the value in the line
                    $replacement = "<span style='color: " . $color . "'>" . $values['source'] . "</span>";
                    $line = str_replace($values['source'], $replacement, $line);
                }

                // add the line to the new formatted log
                $formatted_log .= $line . '<br/>';
            }
            return $formatted_log;
        }

        public function logger($priority, $key_value_array){
            $logfile = realpath('logs/teleboticz.log');
            
            # check if the line is important enough to log
            if ($priority <= $this->config['Teleboticz']['log_level']) {
                $t = microtime ( true );
                $micro = sprintf ( "%06d", ($t - floor ( $t )) * 1000000 );
                $d = new DateTime ( date ( 'Y-m-d H:i:s.' . $micro, $t ) );
                
                $timestamp = $d->format ( "Y-m-d H:i:s.u" ); // note at point on "u"

                $priorities = array();
                $priorities[0] = "Error";
                $priorities[1] = "Warning";
                $priorities[2] = "Info";
                $priorities[3] = "Debug";

                $source = "web";

                // loop through the key value pairs to create the sub_log_string
                $sub_log_string = "";
                foreach($key_value_array as $key => $value){
                    $sub_log_string .= sprintf(', %s="%s"', $key, $value);
                }


                $log_string = sprintf('%s, priority="%s", source="%s"%s', $timestamp, $priorities[$priority], $source, $sub_log_string);

                file_put_contents ( $logfile, $log_string . "\n", FILE_APPEND );
            }
        }

        public function test_log(){
            $key_value_array = array();
            $key_value_array['class'] = __CLASS__;
            $key_value_array['method'] = __METHOD__;
            $key_value_array['action'] = "Method called";

            $prio = rand(0, 3);
            echo $prio;

            $this->logger($prio, $key_value_array);
        }



        
    }
?>