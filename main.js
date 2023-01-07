let currentPage = 1;
jQuery('#load-more').on('click', function () {
	currentPage++; // Do currentPage + 1, because we want to load the next page

	jQuery.ajax({
		type: 'POST',
		url: '/wp-admin/admin-ajax.php',
		dataType: 'json',
		data: {
			action: 'ajax_load_more',
			paged: currentPage,
		},
		success: function (res) {
			// console.log(res);
			// console.log(res.data.max);
			console.log(res);


			if (currentPage >= res.data.max) {
				jQuery('#load-more').hide();
			}
			jQuery('.post-list').append(res.data.html);
		}
	});
});


jQuery('#ajax_category').change(function (e) {

	currentPage++; // Do currentPage + 1, because we want to load the next page

	jQuery.ajax({
		type: 'POST',
		url: '/wp-admin/admin-ajax.php',
		dataType: 'json',
		data: {
			action: 'ajax_sortby_category',
			category: e.target.value,
		},
		success: function (res) {
			// console.log(res);
			// console.log(res.data.max);
			console.log(res);

			jQuery('.post-list').html(res.data.html);
		}
	});
});

