(function($){
	$(document).ready(function(){
		$('#boardSearchDomain .btn').click(function(){
			if($('#searchValue').val().length == 0){
				alert('검색어를 입력하세요.');
				
				return false;
			}
			
			$('#boardSearchDomain').submit();
		});
		
		$('#boardSearchDomain #searchDiv').change(function(){
			$('#searchValue').val('');
			$('#searchValue').focus();
		});
	});
})(jQuery);