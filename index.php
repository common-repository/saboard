<?php 
/*
Plugin Name: SABoard
Plugin URI: saboard.web-readymade.com
Description: SABoard is amazing community plugin writing, write comments, answers ... provide. 이 플러그인은 한국형 게시판 플러그인 입니다. 글쓰기,댓글쓰기,답변,비밀글 등의 기능을 제공합니다. 글쓰기 스킨,댓글 스킨, 글내용 seo적용 등등의 기능을 제공합니다. 
Version: 0.6
Author: Oternet
Author URI: saboard.web-readymade.com
License: GPLv2 or later
*/

require_once 'libs/fpbatis/fpbatis.php';
require_once 'libs/mobileDetect/Mobile_Detect.php';

require_once 'SAFramework/functions/loader.php';
require_once 'SAFramework/classes/SALoader.php';
require_once 'SAFramework/classes/SAManager.php';

$saManager = new SAPluginManager(__FILE__);

add_action ( 'init' , array ( $saManager,'init'));

add_action ( 'init' , array ( SAScriptLoader::getInstance()  , 'registerScripts' ));

add_action ( 'init' , array ( new SADownLoadView() , 'init' ));

add_action ( 'init' , array ( new SAUploadAjaxController()   , 'init' ) );
add_action ( 'init' , array ( new SAUploadAdminController()  , 'setUp' ) );
add_action ( 'init' , array ( new SAUploadShortCode()        , 'init' ) );

new SABoardListener();

add_action ( 'init' , array ( new SABoardRss()  			 , 'view' ) );
add_action ( 'init' , array ( new SABoardShortCode()       	 , 'init' ) );
add_action ( 'init' , array ( new SABoardAdminController()   , 'setUp' ) );
add_action ( 'init' , array ( new SABoardAjaxController()    , 'init' ) );