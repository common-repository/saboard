;
(function ($) {
    $.saboard = function ($element, opts) {
        var $board = $element;

        var $btn_write = opts.board_write;
        var $btn_modify = opts.board_modify;
        var $btn_delete = opts.board_delete;
        var $btn_read = opts.board_read;
        var $btn_list = opts.board_list;
        var $btn_file_delete = opts.board_file_delete;
        var $btn_comment = opts.board_comment;

        var $btn_reply = opts.board_reply;
        var $btn_reply_open = opts.board_reply_open;
        var $btn_reply_delete = opts.board_reply_delete;
        var $btn_reply_pw_check = opts.board_reply_pw_check;

        var $btn_reply_delete_open = opts.board_reply_delete_open;
        var $btn_reply_modify_open = opts.board_reply_modify_open;

        var $btn_pw_check = opts.board_pw_check;

        var now_page = $('#now_page').val();
        var page_id = SA_GLOBAL.PAGE_ID;
        var board_id = SA_BOARD_GLOBAL.board_id;
        
        if (typeof $btn_reply != 'undefined') {
            $btn_reply.click(function (e) {
                e.preventDefault();

                var board_action = $board.find('#board_reply_mode').val();
                board_action = 'board_reply_' + board_action;

                var index = $('#board_index').val();
                var loc = '?board_mode=board_read&board_id=' + board_id + '&board_index=' + index + '&board_action=' + board_action+'&page_id='+page_id;

                $board.attr('action', loc);
                $board.submit();
            });
        }

        if (typeof $btn_reply_open != 'undefined') {
            $btn_reply_open.click(function (e) {
                e.preventDefault();

                $.fancybox({
                    'type': 'inline',
                    'href': '#' + $board.attr('id')
                });

                var board_reply_index = $(this).attr('data-board_reply_index');
                var board_reply_order = $(this).attr('data-board_reply_order');
                var board_reply_depth = $(this).attr('data-board_reply_depth');
                var board_reply_grp = $(this).attr('data-board_reply_grp');

                $board.find('#board_reply_grp').val(board_reply_grp);
                $board.find('#board_reply_order').val(board_reply_order);
                $board.find('#board_reply_depth').val(board_reply_depth);
                $board.find('#board_reply_index').val(board_reply_index);
                $board.find('#board_reply_mode').val('insert');
            });
        }

        if (typeof $btn_reply_pw_check != 'undefined') {
            $btn_reply_pw_check.click(function (e) {
                e.preventDefault();

                var data = $board.serialize();
                data += '&action=board_reply_password_check';

                $.ajax({
                    url: SA_GLOBAL.AJAX_URL,
                    data: data,
                    type: 'post',
                    success: function (response) {
                        if (!response.result) {
                            alert('비밀번호를 확인하세요');
                        } else {
                            var board_index = $('#board_index').val();
                            var board_action = $board.find('#board_pw_mode').val();

                            if (board_action == 'modify') {
                                var r = response.boardReplyDomain;

                                $.fancybox.close();

                                $.fancybox({
                                    'type': 'inline',
                                    'href': '#boardReplyDomain'
                                });

                                $replyForm = $('#boardReplyDomain');

                                $replyForm.find('#board_reply_user_nm').val(r.board_reply_user_nm);
                                $replyForm.find('#board_reply_content').val(r.board_reply_content);
                                $replyForm.find('#board_reply_password').val(r.board_reply_password);
                                $replyForm.find('#board_reply_title').val(r.board_reply_title);
                                $replyForm.find('#board_reply_index').val(r.board_reply_index);
                                $replyForm.find('#board_reply_mode').val('modify');
                            } else if (board_action == 'delete') {
                            	if(confirm('정말로 삭제하시겠습니까?')){
                                    var loc = '?board_id=' + board_id + '&board_index=' + board_index + '&board_action=board_reply_useyn&board_reply_use_yn=N&page_id='+page_id;

                                    $board.attr('action', loc);
                                    $board.submit();                            		
                            	}
                            }
                        }
                    }
                });
            });
        }
        
        if (typeof $btn_reply_delete != 'undefined') {
            $btn_reply_delete.click(function (e) {
                e.preventDefault();
                
                if(confirm('정말로 삭제하시겠습니까?')){
                    var board_index = $('#board_index').val();
                    var board_reply_index = $(this).attr('data-board_reply_index');

                    var loc = '?board_id=' + board_id + '&board_index=' + board_index + '&board_action=board_reply_useyn&board_reply_use_yn=N&board_reply_index=' + board_reply_index+'&page_id='+page_id;

                    location.href = loc;                            		
            	}
            });
        }

        if (typeof $btn_reply_delete_open != 'undefined') {
            $btn_reply_delete_open.click(function (e) {
                e.preventDefault();

                var board_reply_index = $(this).attr('data-board_reply_index');
                var board_reply_write_me = $(this).attr('data-write_me');

                $board.find('#board_reply_password').val('');
                $board.find('#board_reply_index').val(board_reply_index);
                $board.find('#board_pw_mode').val('delete');
                $board.find('.bd_reply_pw_check').text('삭제');
                
                if (board_reply_write_me == 'true') {
                    if (confirm('정말로 삭제하시겠습니까?')) {
                        $btn_reply_pw_check.trigger('click');
                    }
                } else {
                    $.fancybox({
                        'type': 'inline',
                        'href': '#' + $board.attr('id')
                    });
                }
            });
        }

        if (typeof $btn_reply_modify_open != 'undefined') {
            $btn_reply_modify_open.click(function (e) {
                e.preventDefault();

                var board_reply_index = $(this).attr('data-board_reply_index');
                var board_reply_write_me = $(this).attr('data-write_me');
                	
                $board.find('#board_reply_password').val('');
                $board.find('#board_reply_index').val(board_reply_index);
                $board.find('#board_pw_mode').val('modify');
                $board.find('.bd_reply_pw_check').text('수정');
                
                if (board_reply_write_me == 'true') {
                    $btn_reply_pw_check.trigger('click');
                } else {
                    $.fancybox({
                        'type': 'inline',
                        'href': '#' + $board.attr('id')
                    });
                }
            });
        }

        if (typeof $btn_list != 'undefined') {
            $btn_list.click(function (e) {
                e.preventDefault();

                var loc = '?board_mode=board_list&board_id=' + board_id + '&now_page=' + now_page+'&page_id='+page_id;

                location.replace(loc);
            });
        }

        if (typeof $btn_read != 'undefined') {
            $btn_read.css('cursor', 'pointer');

            $btn_read.click(function (e) {
                e.preventDefault();

                var index = $(this).attr('data-index');

                var loc = '?board_mode=board_read&board_index=' + index + '&board_id=' + board_id + '&now_page=' + now_page+'&page_id='+page_id;

                location.replace(loc);
            });
        }

        if (typeof $btn_file_delete != 'undefined') {
            $btn_file_delete.click(function (e) {
                e.preventDefault();

                var seq = $(this).attr('data-seq');

                $('#board_view').val('process');
                $('#mode').val('board_delete_file');
                $('#board_file_seq').val(seq);
                $('#board_file_id').val($('#board_index').val());

                $board.submit();
            });
        }

        if (typeof $btn_write != 'undefined') {
            $btn_write.click(function (e) {
                e.preventDefault();

                $('#board_mode').val('board_edit');

                var pw = $('#board_password').val();
                var pw_check = $('#board_password_check').val();

                if (typeof pw != 'undefined') {
                    if (pw != pw_check) {
                        alert('비밀번호를 확인하세요');
                        $('#board_password_check').focus();
                        return false;
                    }
                }

                $board.attr('action', '');
                $board.attr('method', 'post');
                $board.submit();
            });
        }

        if (typeof $btn_comment != 'undefined') {
            $btn_comment.click(function (e) {
                e.preventDefault();

                $('#board_mode').val('board_edit');

                $board.attr('action', '?board_mode=board_edit&is_comment=1&page_id='+page_id);
                $board.attr('method', 'post');
                $board.submit();
            });
        }

        if (typeof $btn_modify != 'undefined') {
            $btn_modify.click(function (e) {
                e.preventDefault();

                var index = $(this).attr('data-index');

                $board.attr('action', '?board_mode=board_edit&board_index=' + index+'&page_id='+page_id);

                $('#board_mode').val('board_edit');
                $('#board_index').val(index);

                $board.submit();
            });
        }
        
        if (typeof $btn_pw_check != 'undefined') {
        	 $btn_pw_check.click(function (e) {
                e.preventDefault();

                var index = $(this).attr('data-index');

                $board.attr('action', '?board_mode=board_read&board_index=' + index+'&page_id='+page_id+'&board_id='+board_id);
                $('#board_action').val('');
                $('#board_mode').val('board_read');
                $('#board_index').val(index);
                
                $board.submit();
            });
        }

        if (typeof $btn_delete != 'undefined') {
            $btn_delete.click(function (e) {
                e.preventDefault();
                
                if(confirm('정말로 삭제하시겠습니까?')){
                    var index = $(this).attr('data-index');

                    $('#board_mode').val('board_delete');
                    $('#board_action').val('delete');
                    $('#board_index').val(index);

                    $board.attr('method', 'post');
                    $board.submit();                	
                }
            });
        }
    };

    $.fn.saboard = function (opts) {
        return this.each(function () {
            $.saboard($(this), opts);
        });
    };
    
    $(document).ready(function () {
    	if(typeof SA_BOARD_GLOBAL !== 'undefined'){
    		var page_id = SA_GLOBAL.PAGE_ID;
        	var board_id = SA_BOARD_GLOBAL.board_id;
        	
        	$('.sa_board .board_filedown').click(function(e){
        		e.preventDefault();
        		
        		var fileName = $(this).attr('data-fileName');
        		
        		var loc = '?downloadView=load&fileName='+fileName;
        		
        		location.href = loc;
        	});
        	
            $('.sa_board .sa-pagination a').click(function (e) {
                e.preventDefault();

                var loc = '?board_mode=board_list&board_id=' + board_id + '&now_page=' + $(this).attr('data-page')+'&page_id='+page_id;

                location.replace(loc);
            });

            $('.sa_board').animate({
                'opacity': 1
            }, 600);
    	}
    });
})(jQuery);