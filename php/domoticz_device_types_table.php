<h2>Device types</h2>
<p>The table below shows the registered device types.</p>
<p>
    <button type="button">Add device type</button>
</p>
<?php
    require_once 'inc/database.inc.php';
    $db = new Database();
    $results = $db->prepare_query_domoticz_device_types($_POST);
    echo $db->prepare_results_table($results['fields'], $results['form_id'], $results['table_id'], $results['filter_class']);
    echo $db->create_jquery_filter_script($results['filter_class'], $results['form_id'], $results['table_id'], $results['results_file']);
?>
