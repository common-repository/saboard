;(function($){
	$(document).ready(function(){
		$('.sa-pagination a').click(function (e) {
            e.preventDefault();

            var loc = '?page=SABoardAdminController&pageMode=board_backup&now_page=' + $(this).attr('data-page');

            if (typeof page_id != 'undefined') {
                loc += '&page_id=' + page_id;
            }

            location.replace(loc);
        });
	});
})(jQuery);
