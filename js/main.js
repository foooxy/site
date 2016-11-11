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
			$.ajax({
				url: '/admin?reload=1',
				cached: false,
				success: function(data){
					$('.productsTable').html(data);
				}
			});
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
			$.ajax({
				url: '/admin?reload=1',
				cache: false,
				success: function(data){
					$('.productsTable').html(data);
				}
			});	
			$('#editProduct').modal('hide');
		});
	});

	$('#editParam').on('shown.bs.modal', function(event){
		var button = $(event.relatedTarget);
		var ppid = button.data('whatever');
		button.attr('data-selected', 'true');
		$.get('/admin/products/editParam?id=' + ppid, function(data){
			$('#paramResult').html(data);
		});
	});
	$('#editParam').on('hidden.bs.modal', function(event){
		var button = $('#editParamBtn[data-selected = true]');
		button.attr('data-selected', 'false');
	});
	$('#submitParam').click(function(e){
		var ppid = $('#editParamBtn[data-selected = true]').data('whatever');
		var value = $('#paramResult #value').val();

		$.get('/admin/products/editParam?id=' + ppid + '&v=' + value,
		function(data){
			$.ajax({
				url: '/admin?reload=1',
				cache: false,
				success: function(data){
					$('.productsTable').html(data);
				}
			});
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
		var parent = $('#catsAddCatResult #parent').val();

		$('#catsAddCat').modal('hide');
		$.get('/admin/cats/addCat?n=' + catName
			+ '&a=' + catAlias
			+ '&p=' + parent,
			 function(){
				$.ajax({
					url: '/admin/cats?reload=1',
					cache: false,
					success: function(data){
						$('.productsTable').html(data);
					}
				});
		});
	});
	$('#catsEditCat').on('shown.bs.modal', function(event){
		var catId = $(event.relatedTarget).data('whatever');
		$(event.relatedTarget).attr('data-selected', 'true');
		$.get('/admin/cats/editCat?id=' + catId, function(data){
			$('#catsEditCatResult').html(data);
		});	
	});
	$('#catsEditCat').on('hidden.bs.modal', function(){
		var button = $('#catsEditCatBtn[data-selected = true]');
		button.attr('data-selected', false);
	});
	$('#catsSubmitEditCat').click(function(){
		var id = $('#catsEditCatBtn[data-selected = true]').data('whatever');
		var name = $('#catsEditCat #name').val();
		var alias = $('#catsEditCat #alias').val();
		var parent = $('#catsEditCat #parent').val();

		$('#catsEditCat').modal('hide');
		$.get('/admin/cats/editCat?id=' + id
			+ '&n=' + name
			+ '&a=' + alias
			+ '&p=' + parent, 
			function(){
				$.ajax({
					url: '/admin/cats?reload=1',
					cache: false,
					success: function(data){
						$('.productsTable').html(data);
					}
				});
		});
	});
	$('#catsEditParam').on('shown.bs.modal', function(event){
		var id = $(event.relatedTarget).data('whatever');
		$(event.relatedTarget).attr('data-selected', 'true');
		$.get('/admin/cats/editParam?id=' + id, function(data){
			$('#catsEditParamResult').html(data);
		});	
	});
	$('#catsEditParam').on('hidden.bs.modal', function(){
		var button = $('#catsEditParamBtn[data-selected = true]');
		button.attr('data-selected', false);
	});
	$('#catsSubmitEditParam').click(function(){
		var catId = $('#catsEditParamBtn[data-selected = true]').data('catid');
		var sPId = $('#catsEditParam #param').val();
		var cid = $('#catsEditParamBtn[data-selected = true]').data('cid');

		$('#catsEditParam').modal('hide');
		$.get('/admin/cats/editParam?id=' + cid
			+ '&p=' + sPId
			+ '&catid=' + catId, function(){
				$.ajax({
					url: '/admin/cats?reload=1',
					cache: false,
					success: function(data){
						$('.productsTable').html(data);
					}
				});
		});
	});

	$('#catsAddParam').on('shown.bs.modal', function(event){
		$(event.relatedTarget).attr('data-selected', 'true');
		$.get('/admin/cats/addParam', function(data){
			$('#catsAddParamResult').html(data);
		});
	});
	$('#catsAddParam').on('hidden.bs.modal', function(){
		var button = $('#catsAddParamBtn[data-selected = true]');
		button.attr('data-selected', 'false');
	});
	$('#catsSubmitAddParam').click(function(){
		var id = $('#catsAddParamBtn[data-selected = true]').data('whatever');
		var param = $('#catsAddParam #param').val();

		$('#catsAddParam').modal('hide');
		$.get('/admin/cats/addParam?id=' + id
			+ '&p=' + param, function(){
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

function catsDelCat(id){
	$.get('/admin/cats/delCat?id=' + id, function(){
		$.ajax({
			url: '/admin/cats?reload=1',
			cache: false,
			success: function(data){
				$('.productsTable').html(data);
			}
		});
	});
}

function catsDelParam(cid, pid){
	$.get('/admin/cats/delParam?cid=' + cid + '&pid=' + pid, function(){
		$.ajax({
			url: '/admin/cats?reload=1',
			cache: false,
			success: function(data){
				$('.productsTable').html(data);
			}
		});
	});
}