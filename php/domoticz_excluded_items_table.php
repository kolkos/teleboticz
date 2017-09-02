<h2>Excluded items</h2>
<p>This table shows the devices which are excluded from the results.</p>
<?php
    require_once 'inc/database.inc.php';
    $db = new Database();
    $results = $db->prepare_query_domoticz_excluded_items($_POST);
    echo $db->prepare_results_table($results['fields'], $results['form_id'], $results['table_id'], $results['filter_class']);
    echo $db->create_jquery_filter_script($results['filter_class'], $results['form_id'], $results['table_id'], $results['results_file']);
?>
