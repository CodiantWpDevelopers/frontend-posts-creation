jQuery(document).ready(function($){
	var getValue = '';
	$('.selectPostTypes').on('change',function(){
		getValue = $(this).val(); console.log("post type" + getValue);
		if( getValue == 'page' || getValue=='select' ){
			
		} 
		if( getValue == 'post' || getValue == 'event'){
			var data = {
				'action': 'showPostTypeTerms',
				'postType': getValue,
				'nonce': plugin_ajax_object.nonce
			};

			jQuery.ajax({
				url: ajaxurl,
				type: 'POST',
				data: data,
				beforeSend:function(){ 
					//jQuery( '.table.table-stripped' ).addClass( 'disable' ); 
				},
				success: function (response) {
					jQuery('.hide').html(response);
				},
				error: function(xhr) {
	
				},
				complete: function() {
					setTimeout(function(){	
						jQuery('.hide').addClass('show');
					},2000);
				},
			});
		}
		
	});
	var table = $('#example').DataTable({
		pageLength : 5,
		lengthMenu: [[5, 10, 20, -1], [5, 10, 20, 'All']],
		"columnDefs": [
			{ "orderable": false, "targets": [1] }
		  ]
	});
	
	jQuery('#accordionFlushExample .accordion-item #allposts .accordion-body table tbody tr td').on('click',function(){
		console.log("clicked Edit");
	});
});