//common jquery plugins
(function($){
	/**
	 * (function($){
			$(document).ready(function(){
				var openHelper = new $.OpenHelper({ 
						href : 'http://test.co.kr/wp/wp-content/themes/sabootstrap/admin/iframe.header.php',
						width:1150,
						height:200,
						openListener : function(){
							this.get('#testform').submit();
						}
					}).open();
			});
		})(jQuery);
	 */
	$.OpenHelper = function(options){
		var that = this;
		
		var def = {
				toolbar : 'yes',
				scrollbars : 'yes',
				resizable :'yes',
				width : 600,
				height : 600,
				left : 0,
				top  : 0,
				name : 'dummy',
				align : 'basic',
				openListener : function(){}
		};
		
		var opts = jQuery.extend(def,options);
		
		function openStateCheck(){
			var dummy = $('#dummy').attr('data-ready');
			
			if(typeof dummy != 'undefined'){
				opts.openListener.apply(that);
			}else{
				setTimeout(function(){
					openStateCheck();
				},500);
			}
		}
		
		this.open = function(){
			$('body').append('<div id="dummy"></div>');
			
			openStateCheck();
			
			if(opts.align == 'center'){
				opts.left = screen.width/2 - opts.width;
				opts.top = screen.height/2 - opts.height;
			}
			
			var open_option = '';
			open_option += 'width='+opts.width;
			open_option += ',height='+opts.height;
			open_option += ',left='+opts.left;
			open_option += ',top='+opts.top;
			
			this.window = window.open(opts.href,opts.name,open_option);
			
			return this;
		}
		
		this.close = function(){
			if(typeof this.window !='undefined'){
				this.window.close();
			}
		}
		
		this.get = function(name){
			return $(name,this.window.document);
		}
	}
	
	$.request = function(){
	    return {
	    	getParamater : function (name) {
		        var rtnval = '';
		        var nowAddress = unescape(location.href);
		        var parameters = (nowAddress.slice(nowAddress.indexOf('?') + 1, nowAddress.length)).split('&');
		        for (var i = 0; i < parameters.length; i++) {
		            var varName = parameters[i].split('=')[0];
		            if (varName.toUpperCase() == name.toUpperCase()) {
		                rtnval = parameters[i].split('=')[1];
		                break;
		            }
		        }
		        return rtnval;
		    } 
	    };
	}();
    
	$.fn.outerHTML = function(){
	    return (!this.length) ? this : (this[0].outerHTML || (
	      function(el){
	          var div = document.createElement('div');
	          div.appendChild(el.cloneNode(true));
	          var contents = div.innerHTML;
	          div = null;
	          return contents;
	    })(this[0]));
	};
	
    $.sa_mediaButton = function($element, options){
        $element.each(function(i, item){
            var $this = $(item);
            
            var opts = {
                title: $this.data('choose'),
                button: {
                    text: $this.data('update'),
                    close: false
                },
                multiple: true,
                'selectListener': function(attributes){
                }
            };
            
            opts = $.extend(opts, options);
            
            var frame = null;
            
            $this.click(function(e){
                e.preventDefault();
                
                if (frame) {
                    frame.open();
                    return;
                }
                
                frame = wp.media(opts);
                
                frame.on('select', function(){
                    var attachment = frame.state().get('selection').first();
                    frame.close();
                    
                    if (opts.multiple) {
                        opts.selectListener.apply(this, [frame.state().get('selection').models]);
                    }
                    else {
                        opts.selectListener.apply(this, [attachment.attributes]);
                    }
                });
                
                frame.open();
            });
        });
    };
    
    $.fn.sa_mediaButton = function(options){
        return $.sa_mediaButton($(this), options);
    };
    
    $.sa_confirmLink = function($element,options){
    	  $element.each(function(i, item){
              var $this = $(item);
              
              var opts = {
            	message : '계속 진행하시겠습니까?'
              };
              
              opts = $.extend(opts, options);
              
              if($this.attr('data-message')){
            	  opts.message = $this.attr('data-message');
              }
              
              var href = $this.attr('href');
              
              $this.click(function(e){
            	  e.preventDefault();
            	  
            	  if(confirm(opts.message)){
            		  location.href= href;
            	  }
              });
    	  });
    };
    
    $.fn.sa_confirmLink = function(options){
        return $.sa_confirmLink($(this), options);
    };
})(jQuery);


//basic modules
(function($){
    $(document).ready(function(){
        $('.user-confirm').sa_confirmLink();
        
        if (typeof $.fn.fancybox !== 'undefined') {
            $(".fancybox").fancybox();
        }
        
        if (typeof $.fn.iCheck !== 'undefined') {
            $('input').iCheck({
                checkboxClass: 'icheckbox_minimal',
                radioClass: 'iradio_minimal',
                labelHover: false,
                handle: ''
            });
        }
        
        if (typeof $.fn.powerTip !== 'undefined') {
            $('.tooltips').powerTip({});
            
            if (typeof $.fn.fancybox !== 'undefined') {
                $a = $('#contents img').parent('.fancybox');
                $a.attr('title', '크게보시려면 클릭');
                
                $a.powerTip({
                    followMouse: true
                });
            }
        }
        
        if (typeof $.datepicker !== 'undefined') {
            $.datepicker.regional['ko'] = {
                closeText: '닫기',
                prevText: '이전달',
                nextText: '다음달',
                currentText: '오늘',
                monthNames: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
                monthNamesShort: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
                dayNames: ['일', '월', '화', '수', '목', '금', '토'],
                dayNamesShort: ['일', '월', '화', '수', '목', '금', '토'],
                dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
                weekHeader: 'Wk',
                dateFormat: 'yy/mm/dd',
                firstDay: 0,
                isRTL: false,
                showMonthAfterYear: true,
                yearSuffix: '년'
            };
            
            $.datepicker.setDefaults($.datepicker.regional['ko']);
            
            $('.datePicker').datepicker();
        }
        
        if (typeof $.fn.prettyPhoto !== 'undefined') {
            $("a[rel^='prettyPhoto']").prettyPhoto();
        }
        
        if (typeof $.fn.watermark !== 'undefined') {
            $('.water-mark').each(function(i, item){
                var mark = $(item).attr('data-mark');
                
                $(item).watermark(mark);
            });
        }
        
        if (typeof Messi !== 'undefined') {
            $.messi = {};
            $.messi.confirm = function(message, call){
                new Messi(message, {
                    title: '알림',
                    buttons: [{
                        id: 0,
                        label: 'Yes',
                        val: true
                    }, {
                        id: 1,
                        label: 'No',
                        val: false
                    }],
                    callback: function(val){
                        call.apply(this, [val]);
                    }
                });
            };
            
            $.messi.alert = function(message){
                new Messi(message, {
                    title: '알림',
                    buttons: [{
                        id: 0,
                        label: '닫기',
                        val: 'X'
                    }]
                });
            };
            
            $.messi.alertCallback = function(message, call){
                new Messi(message, {
                    title: '알림',
                    buttons: [{
                        id: 0,
                        label: '닫기',
                        val: 'X'
                    }],
                    callback: function(){
                        call.apply(this);
                    }
                });
            };
        }
        
        if (typeof $.validator !== 'undefined') {
            $.extend(jQuery.validator.messages, {
                required: "반드시 입력해야 합니다.",
                remote: "수정 바랍니다.",
                email: "이메일 주소를 올바로 입력하세요.",
                url: "URL을 올바로 입력하세요.",
                date: "날짜가 잘못 입력됐습니다.",
                dateISO: "ISO 형식에 맞는 날짜로 입력하세요.",
                number: "숫자만 입력하세요.",
                digits: "숫자(digits)만 입력하세요.",
                creditcard: "올바른 신용카드 번호를 입력하세요.",
                equalTo: "값이 서로 다릅니다.",
                accept: "승낙해 주세요.",
                maxlength: jQuery.validator.format("{0}글자 이상은 입력할 수 없습니다."),
                minlength: jQuery.validator.format("적어도 {0}글자는 입력해야 합니다."),
                rangelength: jQuery.validator.format("{0}글자 이상 {1}글자 이하로 입력해 주세요."),
                range: jQuery.validator.format("{0}에서 {1} 사이의 값을 입력하세요."),
                max: jQuery.validator.format("{0} 이하로 입력해 주세요."),
                min: jQuery.validator.format("{0} 이상으로 입력해 주세요.")
            });
            
            if (typeof Messi !== 'undefined') {
                jQuery.validator.setDefaults({
                    onkeyup: false,
                    onclick: false,
                    onfocusout: false,
                    showErrors: function(errorMap, errorList){
                        if (typeof errorList[0] !== 'undefined') {
                            var caption = $(errorList[0].element).attr('data-caption') ||
                            			  $(errorList[0].element).attr('name');
                            			  
                            //$.messi.alert(caption + "은(는) " + errorList[0].message); //use messi
                            alert(caption + "은(는) " + errorList[0].message);
                        }
                    }
                });
            }
            
            $('.validateForm').validate({});
        }
        
        setTimeout(function(){
			$('.session_message').hide('slow');
		},3500);
    });
    
})(jQuery);
