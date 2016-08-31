$(document).ready(function(){
	$('#productForm').submit(function(e){
		e.preventDefault();
		var m_method = $(this).attr('method');
		var m_action = $(this).attr('action');
		var m_data = $(this).serialize();
		$.ajax({
			type: m_method,
			url: m_action,
			data: m_data,
			success: function(result){
				location.reload(true);
			}
		});
	});	
});

$(document).ready(function(){
	$('#catSelect').change(function(e){
		var data = 'val=' + $(this).val();
		alert(data);
		$.ajax({
			type: 'post',
			url: 'getParams.php',
			data: data,
			success: function(result){
				$('#paramSelect').empty();
				var arr = $.parseJSON(result);
				console.log(arr);
				arr.forEach(function(item, i, arr){
					$('#paramSelect').append("<option value='" + item[0] + "'>" + item[1] + "</option>");
				});
			}
		});
	});	
});