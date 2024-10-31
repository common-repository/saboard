<?php
if (! function_exists ( 'sa_parse_args' )) {
	function sa_parse_args($args, $defaults = '') {
		if (is_object ( $args )) {
			$r = get_object_vars ( $args );
		} elseif (is_array ( $args )) {
			$r = & $args;
		} else {
			parse_str ( $args, $array );
			if (get_magic_quotes_gpc ())
				$array = stripslashes_deep ( $array );
			
			sa_parse_args ( $array );
		}
		
		if (is_array ( $defaults ))
			return array_merge ( $defaults, $r );
		
		return $r;
	}
}

if (! function_exists ( 'sa_xss_clean' )) {
	function sa_xss_clean($data) {
		if(is_array($data)){
			return array_map('xssfilter', $data);
		}
		
		$data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
		$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
		$data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
		$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
		
		$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);
		
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);
		
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);
		
		$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);
		
		do{
			$old_data = $data;
			$data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|ilayer|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
		}
		
		while ($old_data !== $data);
		
		return $data;
	}
}

/**
 * in kboard 
 * 
 * XSS filter
 *
 * This was built from numerous sources
 * (thanks all, sorry I didn't track to credit you)
 *
 * It was tested against *most* exploits here: http://ha.ckers.org/xss.html
 * WARNING: Some weren't tested!!!
 * Those include the Actionscript and SSI samples, or any newer than Jan 2011
 *
 *
 * TO-DO: compare to SymphonyCMS filter:
 * https://github.com/symphonycms/xssfilter/blob/master/extension.driver.php
 * (Symphony's is probably faster than my hack)
 */
if(!function_exists('sa_xss_clean2')){
	function sa_xss_clean2($data){
		
	}
}


if(!function_exists('sa_get_array_value')){
	function sa_get_array_value($array=array(),$key){
		if(!is_array($array)){
			return false;
		}
		
		return array_key_exists($key, $array) ? $array[$key] : null; 
	}
}

if(!function_exists('sa_cut')){
	function sa_cut($str,$cutlength) {
		if ( mb_strlen( $str ) > $cutlength ) {
			$subex = mb_substr( $str, 0, $cutlength - 5 );

			return $subex.'...';
		}else{
			return $str;
		}
		return null;
	}
}

if(!function_exists('json_decode_nice')){
	function json_decode_nice($json, $assoc = TRUE){
		$a = array('name'=>'a');
		
		$json = str_replace(array("\n","\r"),"",$json);
		$json = preg_replace('/([{,]+)(\s*)([^"]+?)\s*:/','$1"$3":',$json);
		$json = preg_replace('/(,)\s*}$/','}',$json);
		$json = preg_replace('/&quot;/','',$json);
		$json = preg_replace('/:,/',':null,',$json);
		
		$k = explode(',', preg_replace('/{|}/','',$json));
		
		foreach($k as $value){
			//$key = str_$value
			$item  = explode(':', $value);
			$key   = $item[0];
			$value = $item[1];
			
			echo "".$value.'<br/>';
		}
		
		return json_decode($json,$assoc);
	}
}

if(!function_exists('sa_serialize')){
	function sa_serialize($obj){
		return is_array($obj) || is_object($obj) ? base64_encode(serialize($obj)) : false;
	}
}

if(!function_exists('sa_unserialize')){
	function sa_unserialize($obj){
		$obj = base64_decode($obj);
		
		return is_serialized($obj) ? unserialize($obj) : false;
	}
}

if(!function_exists('sa_get_mimetypes')){
	function sa_get_mimetypes(){
		return array("323" => "text/h323",
			"acx" => "application/internet-property-stream",
			"ai" => "application/postscript",
			"aif" => "audio/x-aiff",
			"aifc" => "audio/x-aiff",
			"aiff" => "audio/x-aiff",
			"asf" => "video/x-ms-asf",
			"asr" => "video/x-ms-asf",
			"asx" => "video/x-ms-asf",
			"au" => "audio/basic",
			"avi" => "video/x-msvideo",
			"axs" => "application/olescript",
			"bas" => "text/plain",
			"bcpio" => "application/x-bcpio",
			"bin" => "application/octet-stream",
			"bmp" => "image/bmp",
			"c" => "text/plain",
			"cat" => "application/vnd.ms-pkiseccat",
			"cdf" => "application/x-cdf",
			"cer" => "application/x-x509-ca-cert",
			"class" => "application/octet-stream",
			"clp" => "application/x-msclip",
			"cmx" => "image/x-cmx",
			"cod" => "image/cis-cod",
			"cpio" => "application/x-cpio",
			"crd" => "application/x-mscardfile",
			"crl" => "application/pkix-crl",
			"crt" => "application/x-x509-ca-cert",
			"csh" => "application/x-csh",
			"css" => "text/css",
			"dcr" => "application/x-director",
			"der" => "application/x-x509-ca-cert",
			"dir" => "application/x-director",
			"dll" => "application/x-msdownload",
			"dms" => "application/octet-stream",
			"doc" => "application/msword",
			"dot" => "application/msword",
			"dvi" => "application/x-dvi",
			"dxr" => "application/x-director",
			"eps" => "application/postscript",
			"etx" => "text/x-setext",
			"evy" => "application/envoy",
			"exe" => "application/octet-stream",
			"fif" => "application/fractals",
			"flr" => "x-world/x-vrml",
			"gif" => "image/gif",
			"gtar" => "application/x-gtar",
			"gz" => "application/x-gzip",
			"h" => "text/plain",
			"hdf" => "application/x-hdf",
			"hlp" => "application/winhlp",
			"hqx" => "application/mac-binhex40",
			"hta" => "application/hta",
			"htc" => "text/x-component",
			"htm" => "text/html",
			"html" => "text/html",
			"htt" => "text/webviewhtml",
			"ico" => "image/x-icon",
			"ief" => "image/ief",
			"iii" => "application/x-iphone",
			"ins" => "application/x-internet-signup",
			"isp" => "application/x-internet-signup",
			"jfif" => "image/pipeg",
			"jpe" => "image/jpeg",
			"jpeg" => "image/jpeg",
			"jpg" => "image/jpeg",
			"js" => "application/x-javascript",
			"latex" => "application/x-latex",
			"lha" => "application/octet-stream",
			"lsf" => "video/x-la-asf",
			"lsx" => "video/x-la-asf",
			"lzh" => "application/octet-stream",
			"m13" => "application/x-msmediaview",
			"m14" => "application/x-msmediaview",
			"m3u" => "audio/x-mpegurl",
			"man" => "application/x-troff-man",
			"mdb" => "application/x-msaccess",
			"me" => "application/x-troff-me",
			"mht" => "message/rfc822",
			"mhtml" => "message/rfc822",
			"mid" => "audio/mid",
			"mny" => "application/x-msmoney",
			"mov" => "video/quicktime",
			"movie" => "video/x-sgi-movie",
			"mp2" => "video/mpeg",
			"mp3" => "audio/mpeg",
			"mpa" => "video/mpeg",
			"mpe" => "video/mpeg",
			"mpeg" => "video/mpeg",
			"mpg" => "video/mpeg",
			"mpp" => "application/vnd.ms-project",
			"mpv2" => "video/mpeg",
			"ms" => "application/x-troff-ms",
			"mvb" => "application/x-msmediaview",
			"nws" => "message/rfc822",
			"oda" => "application/oda",
			"p10" => "application/pkcs10",
			"p12" => "application/x-pkcs12",
			"p7b" => "application/x-pkcs7-certificates",
			"p7c" => "application/x-pkcs7-mime",
			"p7m" => "application/x-pkcs7-mime",
			"p7r" => "application/x-pkcs7-certreqresp",
			"p7s" => "application/x-pkcs7-signature",
			"pbm" => "image/x-portable-bitmap",
			"pdf" => "application/pdf",
			"pfx" => "application/x-pkcs12",
			"pgm" => "image/x-portable-graymap",
			"pko" => "application/ynd.ms-pkipko",
			"pma" => "application/x-perfmon",
			"pmc" => "application/x-perfmon",
			"pml" => "application/x-perfmon",
			"pmr" => "application/x-perfmon",
			"pmw" => "application/x-perfmon",
			"pnm" => "image/x-portable-anymap",
			"pot" => "application/vnd.ms-powerpoint",
			"ppm" => "image/x-portable-pixmap",
			"pps" => "application/vnd.ms-powerpoint",
			"ppt" => "application/vnd.ms-powerpoint",
			"prf" => "application/pics-rules",
			"ps" => "application/postscript",
			"pub" => "application/x-mspublisher",
			"qt" => "video/quicktime",
			"ra" => "audio/x-pn-realaudio",
			"ram" => "audio/x-pn-realaudio",
			"ras" => "image/x-cmu-raster",
			"rgb" => "image/x-rgb",
			"rmi" => "audio/mid",
			"roff" => "application/x-troff",
			"rtf" => "application/rtf",
			"rtx" => "text/richtext",
			"scd" => "application/x-msschedule",
			"sct" => "text/scriptlet",
			"setpay" => "application/set-payment-initiation",
			"setreg" => "application/set-registration-initiation",
			"sh" => "application/x-sh",
			"shar" => "application/x-shar",
			"sit" => "application/x-stuffit",
			"snd" => "audio/basic",
			"spc" => "application/x-pkcs7-certificates",
			"spl" => "application/futuresplash",
			"src" => "application/x-wais-source",
			"sst" => "application/vnd.ms-pkicertstore",
			"stl" => "application/vnd.ms-pkistl",
			"stm" => "text/html",
			"svg" => "image/svg+xml",
			"sv4cpio" => "application/x-sv4cpio",
			"sv4crc" => "application/x-sv4crc",
			"t" => "application/x-troff",
			"tar" => "application/x-tar",
			"tcl" => "application/x-tcl",
			"tex" => "application/x-tex",
			"texi" => "application/x-texinfo",
			"texinfo" => "application/x-texinfo",
			"tgz" => "application/x-compressed",
			"tif" => "image/tiff",
			"tiff" => "image/tiff",
			"tr" => "application/x-troff",
			"trm" => "application/x-msterminal",
			"tsv" => "text/tab-separated-values",
			"txt" => "text/plain",
			"uls" => "text/iuls",
			"ustar" => "application/x-ustar",
			"vcf" => "text/x-vcard",
			"vrml" => "x-world/x-vrml",
			"wav" => "audio/x-wav",
			"wcm" => "application/vnd.ms-works",
			"wdb" => "application/vnd.ms-works",
			"wks" => "application/vnd.ms-works",
			"wmf" => "application/x-msmetafile",
			"wps" => "application/vnd.ms-works",
			"wri" => "application/x-mswrite",
			"wrl" => "x-world/x-vrml",
			"wrz" => "x-world/x-vrml",
			"xaf" => "x-world/x-vrml",
			"xbm" => "image/x-xbitmap",
			"xla" => "application/vnd.ms-excel",
			"xlc" => "application/vnd.ms-excel",
			"xlm" => "application/vnd.ms-excel",
			"xls" => "application/vnd.ms-excel",
			"xlt" => "application/vnd.ms-excel",
			"xlw" => "application/vnd.ms-excel",
			"xof" => "x-world/x-vrml",
			"xpm" => "image/x-xpixmap",
			"xwd" => "image/x-xwindowdump",
			"z" => "application/x-compress",
			"zip" => "application/zip",
			"jpg" => "",
			"jpeg" => "",
			"gif" => "",
			"png" => "",
			"bmp" => "",
			"hwp" => "",
			"txt" => "",
			"psd" => "" );
	}
}

if(!function_exists('sa_get_mimetypes_extentions')){
	function sa_get_mimetypes_extentions(){
		$result = array();
		$mimetypes = sa_get_mimetypes();
	
		foreach($mimetypes as $key=>$value){
			array_push($result, $key);
		}
	
		return $result;
	}
}

if(!function_exists('sa_str_cut')){
	function sa_str_cut($str,$cut){
		if(mb_strlen($str) > $cut){
			return mb_strcut(strip_tags($str),0,$cut) . '...';
		}else{
			return $str;
		}
	}
}
if(!function_exists('sa_str_last_name')){
	function sa_str_last_name($str,$split='/'){
		$result = explode($split, $str);
		
		return $result[count($result)-1];
	}
}

if(!function_exists('sa_get_html_in_img_src')){
	/**
	 * [0] 태그
	 * [1] 값
	 * @param string $html
	 * @return array
	 */
	function sa_get_html_in_img_src($html){
		$html = stripcslashes($html);
		
		preg_match_all("/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", $html , $m);
		
		return $m;
	}
}

if(!function_exists('sa_require_lib')){
	function sa_require_lib($name){
		require_once dirname(__FILE__).'/../libs/'.$name;
	}
}