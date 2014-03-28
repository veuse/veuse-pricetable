jQuery(function($) {

	$('.wp-list-table tbody').sortable({
		axis: 'y',
		handle: '.order-priceitem',
		placeholder: 'ui-state-highlight',
		forcePlaceholderSize: true,
		update: function(event, ui) {
			var theOrder = $(this).sortable('toArray');

			var data = {
				action: 'veuse_priceitem_update_post_order',
				postType: $(this).attr('data-post-type'),
				order: theOrder
			};
			
			//alert(data.order);

			$.post(ajaxurl, data);
		}
	}).disableSelection();

});