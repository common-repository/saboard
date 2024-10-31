<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		<p>
			<?php _e( 'Pingback:', '' ); ?>
			<?php comment_author_link(); ?> 
			<?php edit_comment_link( __( '(Edit)', '' ), '<span class="edit-link">', '</span>' ); ?>
		</p>