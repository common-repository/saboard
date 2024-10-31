<?php
/*
use Guzzle\Http\Client;

sa_require_lib('guzzle/vendor/autoload.php');

if (!function_exists ( 'sa_get_google_fonts' )) {
	function sa_get_google_fonts($apikey) {
		$url 	= 'https://www.googleapis.com/webfonts/v1/webfonts?key={apikey}';
		$option = array ('apikey' => $apikey );
		
		return sa_http_getRemoteJson( $url , $option );
	}
}

if (!function_exists ( 'sa_get_google_fonts_array' )) {
	function sa_get_google_fonts_array($apikey) {
		if(empty($apikey)){
			return false;
		}
		
		$url 	= 'https://www.googleapis.com/webfonts/v1/webfonts?key={apikey}';
		$option = array ('apikey' => $apikey );
		
		$result =  sa_http_getRemoteJson( $url , $option ,true);
		
		$array = array();

		foreach($result->items as $item){
			array_push($array, $item->family);
		}
		
		return $array;
	}
}

if (!function_exists ( 'sa_http_getRemoteJson' )) {
	function sa_http_getRemoteJson($url, $guzzleOptions = array() ,$decode = false){
		try{
			$client = new Client ( $url,$guzzleOptions );
			
			$request = $client->get ();
			$response = $request->send ();

			$json = $response->getBody (true);
					
			if($decode){
				return json_decode ( $json );;
			}	
		}catch(Exception $e){
			return false;		
		}
		
		return $json;
	}
}

if (!function_exists ( 'sa_http_getRequestBody' )) {
	function sa_http_getRequestBody($url, $guzzleOptions = array() ){
		try{
			$client = new Client ( $url , $guzzleOptions );
				
			$request = $client->get ();
			$response = $request->send ();

			return $response->getBody (true);
		}catch(Exception $e){
			return false;
		}
	}
}


if(! function_exists('sa_get_kma_weather')){
	function sa_get_kma_weather($stnId) {
		$key = 'sa_weater_data' . $stnId;

		$today = date ( "Ymd" ) - 1;
		$list = get_option ( $key, array () );

		if (empty ( $list ) || mb_substr ( $list ['header'] ['tm'], 0, 8 ) != $today) {
			$result = simplexml_load_file ( 'http://www.kma.go.kr/weather/forecast/mid-term-xml.jsp?stnId=' . $stnId );

			$list ['header'] = array ();
			$list ['location'] = array ();

			$headers = $result->header;
			$results = $result->body->location;

			foreach ( $headers as $header ) {
				$list ['header'] = array (
						'title' => $header->title . '',
						'tm' => $header->tm . '',
						'wf' => $header->wf . ''
				);
			}

			foreach ( $results as $locations ) {
				$loc = array ();

				$loc ['datas'] = array ();
				$loc ['city'] = $locations->city . '';
				$loc ['province'] = $locations->province . '';

				foreach ( $locations->data as $item ) {
					$num = $item->numEf;
					$wdate = $item->tmEf;
					$wformat = $item->wf;
					$tmin = $item->tmn;
					$tmax = $item->tmx;
					$rainrate = $item->reliability;

					$tmparr = array (
							'numEf' => $num . '',
							'tmEf' => $wdate . '',
							'wf' => $wformat . '',
							'tmn' => $tmin . '',
							'tmax' => $tmax . '',
							'reliability' => $rainrate . ''
					);

					array_push ( $loc ['datas'], $tmparr );
				}

				array_push ( $list ['location'], $loc );
			}

			update_option ( $key, $list );
		}

		return $list;
	}
}
*/