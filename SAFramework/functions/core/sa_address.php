<?php 
if(!function_exists('fetch_page')){
	function fetch_page($url,$param,$cookies=NULL,$referer_url=NULL){
		if(strlen(trim($referer_url)) == 0) $referer_url= $url;
		$curlsession = curl_init ();
		curl_setopt ($curlsession, CURLOPT_URL, $url);
		curl_setopt ($curlsession, CURLOPT_POST, 1);
		curl_setopt ($curlsession, CURLOPT_POSTFIELDS, $param);
		curl_setopt ($curlsession, CURLOPT_TIMEOUT, 60);
		if($cookies && $cookies!=""){
			curl_setopt ($curlsession, CURLOPT_COOKIE, $cookies);
		}
		curl_setopt ($curlsession, CURLOPT_HEADER, 1);
		curl_setopt ($curlsession, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.01; Windows NT 6.0)");
		curl_setopt ($curlsession, CURLOPT_REFERER, "$referer_url");
	
		ob_start();
		$res = curl_exec ($curlsession);
		$buffer = ob_get_contents();
		ob_end_clean();
		$returnVal = array();
		if (!$buffer) {
			$returnVal['error'] = true;
			$returnVal['content'] = "Curl Fetch Error : ".curl_error($curlsession);
		}else{
			$returnVal['error'] = false;
			$returnVal['content'] = $buffer;
		}
		curl_close($curlsession);
		return $returnVal;
	}
}

if(!function_exists('remove_none_xml_word')){
	function remove_none_xml_word($content){
		$content_array = explode("\n", $content);
		foreach ($content_array as $key => $value) {
			if(substr(trim($value),0,1)!='<'){
				$content_array[$key]='';
			}
		}
		unset($content_array[0]);
		$content = implode("\n", $content_array);
		return trim($content);
	}
}

if(!function_exists('add_dash_and_tag_to_postcd')){
	function add_dash_and_tag_to_postcd($postcd){
		$postcd1=substr($postcd,0,3);
		$postcd2=substr($postcd,3,3);
		return "<span class='postcd1'>$postcd1</span>-<span class='postcd2'>$postcd2</span>";
	}
}

if(!function_exists('get_post_code_xml_by_api')){
	function get_post_code_xml_by_api($query){
		$query = iconv('utf-8','euc-kr',$query);
	
		$post_data = array(
				'target' => 'post',
				'regkey' => 'e213ec6cc59667f931372148873259',
				'query' => $query
		);
	
		$url = 'http://biz.epost.go.kr/KpostPortal/openapi';
		$param = http_build_query($post_data);
		$result = fetch_page($url,$param);
		$result['content'] = remove_none_xml_word($result['content']);
	
		return $result;
	}
}

if(!function_exists('get_address_list')){
	function get_address_list($xml){
		$list = array();
		
		foreach ($xml->itemlist->item as $value) {
			$postcd = add_dash_and_tag_to_postcd($value->postcd);
			$array = array('postcd'=>$postcd,'address'=>$value->address.'');
			
			array_push($list, $array);
		}
		
		return $list;
	}
}

if(!function_exists('divide_postcd')){
	function divide_postcd($postcd){
		$array = array();
		$array[]=substr($postcd,0,3);
		$array[]=substr($postcd,3,3);
		return $array;
	}
}

if(!function_exists('print_postcode_json')){
	function print_postcode_json($xml){
		$count = 1;
		
		$list = array();
		foreach ($xml->itemlist->item as $value) {
			$postcd = divide_postcd($value->postcd);
			
			$array = array('address'=>$value->address.'','postcd1'=>$postcd[0],'postcd2'=>$postcd[1]);
			array_push($list, $array);
			$count++;
		}
		
		return json_encode($list);
	}
}

if(!function_exists('print_postcode_json2')){
	function print_postcode_json2($xml){
		$count = 1;
		echo '[';
		foreach ($xml->itemlist->item as $value) {
			$postcd = divide_postcd($value->postcd);
			echo "\"{$value->address}:{$postcd[0]}-{$postcd[1]}\"";
			if(count($xml->itemlist->item) > $count){
				echo ',';
			}
			$count++;
		}
		echo ']';
	}
}