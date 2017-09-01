function load_logfile(){
    $.ajax({
        type:"POST",
        url:"php/get_log.php",
        datatype:"html",
        success:function(data)
        {
            //do something with response data
            $('#log').html(data);
            $('#log').scrollTop($('#log')[0].scrollHeight);
        }
    });
}
function loadPageSimple(source, target){
    // just load the source to the target
    // source: the page to load
    // target: the destination to put the source in
    $.ajax({
        type: "POST",
        url: source,
        datatype: "html",
        success: function(data){
            $(target).html(data);
        }
    });
}


function sendFormSimple(source, file, target){
    var data = $('#' + source).serialize();
	$.ajax({
		type: "POST",
		url: file,
		data: data,
		success: function(html){
			$(target).html(html);
		}
	});
}