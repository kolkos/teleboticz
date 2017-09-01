<h2>Device types</h2>

<?php
    require_once 'inc/database.inc.php';
    $db = new Database();
    $results = $db->prepare_query_domoticz_device_types($_POST);
    echo $db->prepare_results_table($results['fields'], $results['form_id'], $results['table_id']);
?>
<script>
    // when changing the domoticz_call_config fields
    var delay = (function(){
        var timer = 0;
        return function(callback, ms){
            clearTimeout (timer);
            timer = setTimeout(callback, ms);
        };
    })();

    $('.domoticz_call_config').keyup(function() {
        delay(function(){
            var source = 'domoticz_device_types_form';
            var file = 'php/domoticz_device_types_results.php';
            var target = '#domoticz_device_types_table > tbody';
            sendFormSimple(source, file, target);
        }, 1000 );
    });
</script>