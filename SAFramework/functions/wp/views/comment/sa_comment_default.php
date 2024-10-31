<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
	<article id="comment-<?php comment_ID(); ?>" class="comment">
		<i class="comment-icon-l"></i>
		<header class="comment-meta comment-author vcard">
		<?php
			echo get_avatar ( $comment, 60 );
			printf ( '<cite class="fn"><h3>%2$s %1$s</h3></cite>', get_comment_author_link (), 
			($comment->user_id === $post->post_author) ? '<span> ' . __ ( '작성자 : ', '' ) . '</span>' : '' );
			printf ( '<a href="%1$s"><time datetime="%2$s">%3$s</time></a>', esc_url ( get_comment_link ( $comment->comment_ID ) ), get_comment_time ( 'c' ),
			sprintf ( __ ( '%1$s at %2$s', '' ), get_comment_time (),get_comment_date () ) );
		?>
		</header>
		<?php if ( '0' == $comment->comment_approved ) : ?>
			<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', '' ); ?></p>
		<?php endif; ?>
		<section class="comment-content comment">
			<?php comment_text(); ?>
			<?php edit_comment_link( __( 'Edit', '' ), '<p class="edit-link">', '</p>' ); ?>
		</section>
		<div class="reply">
			<?php comment_reply_link( array_merge( $args, array(  'reply_text' => __( '댓글달기', '' )
																//, 'after' => ' <span>&darr;</span>'
																, 'depth' => $depth
																, 'max_depth' => $args['max_depth'] ) ) ); ?>
		</div>
	</article>

