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

	$('.delProductBtn').click(function(e){
		var id = $(e.currentTarget).data('whatever');
		$.get('/admin/products/delProduct?id=' + id, function(data){
			$.ajax({
				url: '/admin?reload=1',
				cache: false,
				success: function(data){
					$('.productsTable').html(data);
				}
			});	
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
			var type = $('#paramResult #param option:selected').data('type');
			$('#paramResult #type').val(type);
		});
	});
	$('#editParam').on('hidden.bs.modal', function(event){
		var button = $('#editParamBtn[data-selected = true]');
		button.attr('data-selected', 'false');
	});
	$('#submitParam').click(function(e){
		var id = $('#editParamBtn[data-selected = true]').data('whatever');
		var param = $('#paramResult #param').val();
		var value = $('#paramResult #value').val();
		var type = $('#paramResult #type').val();

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
	$('#paramResult #param').change(function(){
		var type = $('#paramResult #param option:selected').data('type');
		$('#paramResult #type').val(type);	
	});

	$('#addParam').on('shown.bs.modal', function(event){
		var button = $(event.relatedTarget);
		button.attr('data-selected', 'true');
		var id = button.data('whatever');
		$.get('/admin/products/addParam?id=' + id, function(data){
			$('#addParamResult').html(data);
			var type = $('#addParamResult #param option:selected').data('type');
			$('#addParamResult #type').val(type);
		});
	});
	$('#addParam').on('hidden.bs.modal', function(e){
		$('.addParamBtn[data-selected = true]').attr('data-selected', 'false');
	});
	$('#submitAddParam').click(function(e){
		var id = $('.addParamBtn[data-selected = true]').data('whatever');
		var param = $('#addParamResult #param').val();
		var value = $('#addParamResult #value').val();
		var type = $('#addParamResult #type').val();

		$.get('/admin/products/addParam?id=' + id
			+ '&p=' + param
			+ '&v=' + value
			+ '&t=' + type
			, function(data){
			$.ajax({
				url: '/admin?reload=1',
				cache: false,
				success: function(data){
					$('.productsTable').html(data);
				}
			});
		});
	});

	$('.delParamBtn').click(function(event){
		var id = $(event.currentTarget).data('whatever');
		$.get('/admin/products/delParam?id=' + id, function(){
			$.ajax({
				url: '/admin?reload=1',
				cache: false,
				success: function(data){
					$('.productsTable').html(data);
				}
			});
		});
	});

	$('#paramsAddParam').on('shown.bs.modal', function(event){
		$.get('/admin/params/addParam', function(data){
			$('#paramsAddParam #paramsAddParamResult').html(data);
		});
	});
	$('#paramsSubmitAddParam').click(function(){
		var name = $('#paramsAddParam #paramsAddParamResult #param').val();
		var type = $('#paramsAddParam #paramsAddParamResult #type').val();
		$('#paramsAddParam').modal('hide');
		$.get('/admin/params/addParam?n=' + name + '&t=' + type, function(data){
			$.ajax({
				url: '/admin/params?reload=1',
				cache: false,
				success: function(data){
					$('.productsTable').html(data);
				}
			});
		});
	});
	$('#paramsEditParam').on('shown.bs.modal', function(event){
		var paramId = $(event.relatedTarget).data('whatever');
		$(event.relatedTarget).attr('data-selected', 'true');
		$.get('/admin/params/editParam?id=' + paramId, function(data){
			$('#paramsEditParamResult').html(data);
		});
	});
	$('#paramsEditParam').on('hidden.bs.modal', function(){
		var button = $('#paramsEditParamBtn[data-selected = true]');
		button.attr('data-selected', 'false');	
	});
	$('#paramsSubmitEditParam').click(function(){
		var paramId = $('#paramsEditParamBtn[data-selected = true]').data('whatever');
		var param = $('#paramsEditParamResult #param').val();
		var type = $('#paramsEditParamResult #type').val();
		$('#paramsEditParam').modal('hide');
		$.get('/admin/params/editParam?id=' + paramId
			+ '&n=' + param
			+ '&t=' + type, function(data){
				$.ajax({
					url: '/admin/params?reload=1',
					cache: false,
					success: function(data){
						$('.productsTable').html(data);
					}
				});
		});
	});

	$('#catsAddCat').on('shown.bs.modal', function(event){
		$.get('/admin/cats/addCat', function(data){
			$('#catsAddCatResult').html(data);
		});
	});
	$('#catsSubmitAddCat').click(function(){
		var catName = $('#catsAddCatResult #name').val();
		var catAlias = $('#catsAddCatResult #alias').val();

		$('#catsAddCat').modal('hide');
		$.get('/admin/cats/addCat?n=' + catName
			+ '&a=' + catAlias, function(){
				$.ajax({
					url: '/admin/cats?reload=1',
					cache: false,
					success: function(data){
						$('.productsTable').html(data);
					}
				});
		});
	});
});

function paramsDelParam(id){
	$.get('/admin/params/delParam?id=' + id, function(){
		$.ajax({
			url: '/admin/params?reload=1',
			cache: false,
			success: function(data){
				$('.productsTable').html(data);
			}
		});
	});	
}