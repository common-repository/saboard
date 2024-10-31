<?php 
header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);

$param =array();
$param['board_id'] = SARequest::getParameter('board_id');
$param['start_record'] = 0;
$param['page_per_record'] = 100;
$boardList = SABoardService::getInstance()->getAllBoardList($param);
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/">
	<channel>
		<title><?php bloginfo_rss('name'); wp_title_rss(); ?></title>
		<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
		<link><?php bloginfo_rss('url') ?></link>
		<description><?php bloginfo_rss("description") ?></description>
		<lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
		<language><?php bloginfo_rss( 'language' ); ?></language>
		<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
		<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
		<?php foreach($boardList as $board){ 
			$title = $board['board_title'];
			$url   = get_home_url().'?board_mode=board_read&board_id='.$param['board_id'].'&board_index='.$board['board_index'];
			$des   = $board['board_content'];?>
			<item>
				<title><?php echo $title ?></title>
				<link><?php echo htmlentities($url) ?></link>
				<description><![CDATA[<?php echo $des ?>]]></description>
				<pubDate><?php echo mysql2date('Y-m-d H:i:s' , $board['board_reg_date']) ?></pubDate>
				<dc:creator><?php echo $board['board_user_nm'] ?></dc:creator>
				<?php if(!empty($des)){?>
					<content:encoded><![CDATA[<?php echo $des; ?>]]></content:encoded>
				<?php }?>
			</item>
		<?php }?>
	</channel>
</rss>
<?php die(); ?>