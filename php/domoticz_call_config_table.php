<h2>Domoticz call config</h2>

<?php
    require_once 'inc/database.inc.php';
    $db = new Database();
    $results = $db->prepare_query_domoticz_call_config($_POST);
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
            var source = 'domoticz_call_config_form';
            var file = 'php/domoticz_call_config_results.php';
            var target = '#domoticz_call_config_table > tbody';
            sendFormSimple(source, file, target);
        }, 1000 );
    });
</script>