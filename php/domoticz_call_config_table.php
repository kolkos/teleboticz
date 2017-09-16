<h2>Domoticz call config</h2>
<p>This table shows the configuration per dervice type. This table contains data necessary for:</p>
<ul>
    <li>The API call to Domoticz</li>
    <li>The required fields from the API respons</li>
    <li>Icon for the status message</li>
    <li>The fields in the status message</li>
</ul>
<p>
    <button type="button" id="add_config_call_item">Add call config element</button>
</p>
<?php
    require_once 'inc/config.inc.php';
    $site = new Config();
    $results = $site->prepare_query_domoticz_call_config($_POST);
    echo $site->prepare_results_table($results['fields'], $results['form_id'], $results['table_id'], $results['filter_class']);
    echo $site->create_jquery_filter_script($results['filter_class'], $results['form_id'], $results['table_id'], $results['results_file']);
?>
<script>
    $('#add_config_call_item').click(function(){
        openOverlayWindow(300, 'php/home.php', null);
    });
</script>