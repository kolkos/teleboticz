<div id="log">Loading log file...</div>
<p><a href="logs/teleboticz.log" target="_blank">Download log file</a></p>
<script>
    $(function() {
        load_logfile();
        setInterval(function()
        { 
            load_logfile();
        }, 5000);//time in milliseconds 
    });
    
</script>
