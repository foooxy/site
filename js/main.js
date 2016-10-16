$(document).ready(function(){
	$('#addProduct').on('shown.bs.modal', function(event){
		$.get('/admin/products/addProduct', function(data){
			$('#addProductResult').html(data);
		});
	});
	$('#submitAddProduct').click(function(e){
		var productName = $('#addProductResult #productName').val();
		var alias = $('#addProductResult #alias').val();
		var category = $('#addProductResult #category option:selected').val();	
		$.get('/admin/products/addProduct?pn=' + productName
			+ '&a=' + alias
			+ '&c=' + category,
		function(data){
			alert(data);
			if(data > 0){
				$.ajax({
					url: '/admin?reload=1',
					cached: false,
					success: function(data){
						$('.productsTable').html(data);
					}
				});
			}
			$('#addProduct').modal('hide');
		});
	});

	$('#editProduct').on('shown.bs.modal', function(event){
		var button = $(event.relatedTarget);
		var id = button.data('whatever');
		button.attr('data-selected', 'true');
		$.get('/admin/products/editProduct?id=' + id, function(data){
			$('#productResult').html(data);
		});
	});
	$('#editProduct').on('hidden.bs.modal', function(event){
		var button = $('#editProductBtn[data-selected = true]');
		button.attr('data-selected', 'false');
	});
	$('#submitProduct').click(function(e){
		var id = $('#editProductBtn[data-selected = true]').data('whatever');
		var productName = $('#editProduct #productName').val();
		var alias = $('#editProduct #alias').val();
		var category = $('#editProduct #category option:selected').val();
		$.get('/admin/products/editProduct?id=' + id
			+ '&pn=' + productName
			+ '&a=' + alias
			+ '&c=' + category,
		function(data){
			if(data > 0){
				$.ajax({
					url: '/admin?reload=1',
					cache: false,
					success: function(data){
						$('.productsTable').html(data);
					}
				});	
			}
			$('#editProduct').modal('hide');
		});
	});

	$('#editParam').on('shown.bs.modal', function(event){
		var button = $(event.relatedTarget);
		var id = button.data('whatever');
		button.attr('data-selected', 'true');
		$.get('/admin/products/editParam/' + id, function(data){
			$('#paramResult').html(data);
		});
	});
	$('#editParam').on('hidden.bs.modal', function(event){
		var button = $('#editParamBtn[data-selected = true]');
		button.attr('data-selected', 'false');
	});
	$('#submitParam').click(function(e){
		var id = $('#editParamBtn[data-selected = true]').data('whatever');
		var param = $('#param').val();
		var value = $('#value').val();
		var type = $('#type').val();

		$.get('/admin/products/editParam?id=' + id
			+ '&p=' + param
			+ '&v=' + value
			+ '&t=' + type,
		function(data){
			if(data > 0){
				$.ajax({
					url: '/admin?reload=1',
					cache: false,
					success: function(data){
						$('.productsTable').html(data);
					}
				});
			}
			$('#editParam').modal('hide');	
		});
	});
});