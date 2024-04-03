<?php

// Enqueues the stylesheet. Do not remove this.
add_action(
	'wp_enqueue_scripts',
	function() {
		// Cache busting for development.
		$theme_last_modified = getLatestModificationTime( get_stylesheet_directory() );
		wp_enqueue_style(
			'rampreligion-theme',
			get_stylesheet_directory_uri() . '/style.css',
			[],
			$theme_last_modified
		);
	}
);

function getLatestModificationTime($dir) {
    $latestTime = 0;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $fileinfo) {
        if ($fileinfo->isFile()) {
            $fileTime = $fileinfo->getMTime();
            if ($fileTime > $latestTime) {
                $latestTime = $fileTime;
            }
        }
    }

    return $latestTime;
}


// Disable Relevanssi on Profile and Citation archives, where it interferes with our search.
/*
add_action(
	'wp',
	function() {
		if ( is_post_type_archive( 'ramp_profile' ) || is_post_type_archive( 'ramp_citation' ) ) {
			remove_filter( 'posts_request', 'relevanssi_prevent_default_request' );
			remove_filter( 'posts_pre_query', 'relevanssi_query', 99 );
		}
	}
);
*/

/*
// Replace the fallback profile icon.
add_filter(
	'ramp_default_profile_avatar',
	function() {
		return get_stylesheet_directory_uri() . '/assets/img/mw-profile-fallback-icon.png';
	}
);
*/

/**
 * Adds the ssrc_meta_nav action needed for the SSRC nav to render.
 *
 * Necessary only for SSRC sites.
 */
add_action(
	'wp_body_open',
	function() {
		do_action( 'ssrc_meta_nav' );
	}
);

/**
 * Adds Focus Tags taxonomy support for ramp_topic post type.
 */
add_action(
	'init',
	function() {
		register_taxonomy_for_object_type( 'ramp_focus_tag', 'ramp_topic' );
	},
	100
);

/**
 * Filters the archive title for our custom taxonomies.
 */
add_filter(
	'get_the_archive_title_prefix',
	function( $prefix ) {
		if ( is_tax( 'ramp_article_type' ) ) {
			return 'Project Type: ';
		}

		if ( is_tax( 'ramp_focus_tag' ) ) {
			return 'Tag: ';
		}

		return $prefix;
	}
);

/**
 * More current-menu-item filtering.
 *
 * This does not work properly in the theme because the original items were deleted
 * and recreated without being made 'post-type-archive'.
 */
function ramp_religion_add_current_classes_to_nav_links( $block_content, $block ) {
	if ( false !== strpos( $block_content, 'current-menu-item' ) ) {
		return $block_content;
	}

	$is_current = false;

	$archives = [
		'ramp_topic'     => get_post_type_archive_link( 'ramp_topic' ),
		'ramp_review'    => get_post_type_archive_link( 'ramp_review' ),
		'ramp_article'   => get_post_type_archive_link( 'ramp_article' ),
		'ramp_profile'   => get_post_type_archive_link( 'ramp_profile' ),
		'ramp_citation'  => get_post_type_archive_link( 'ramp_citation' ),
		'ramp_news_item' => get_post_type_archive_link( 'ramp_news_item' ),
	];

	switch ( $block['attrs']['kind'] ) {
		case 'custom' :
		case 'post-type-archive' :
			$post_type = null;
			foreach ( $archives as $type => $url ) {
				if ( $url === $block['attrs']['url'] ) {
					$post_type = $type;
					break;
				}

				// Also check for relative URLs.
				if ( str_replace( home_url(), '', $url ) === $block['attrs']['url'] ) {
					$post_type = $type;
					break;
				}
			}

			if ( $post_type ) {
				$is_current = is_post_type_archive( $post_type ) || is_singular( $post_type );
			}

			break;
	}

	if ( $is_current ) {
		$block_content = preg_replace( '/^<li class="/', '<li class="current-menu-item ', $block_content );
	}

	return $block_content;
}
add_filter( 'render_block_core/navigation-link', 'ramp_religion_add_current_classes_to_nav_links', 20, 2 );
add_filter( 'render_block_core/navigation-submenu', 'ramp_religion_add_current_classes_to_nav_links', 20, 2 );

/**
 * Change the rewrite settings for the ramp_review post type.
 */
add_action(
	'init',
	function() {
		// 'research-reviews' => 'field-reviews'
		$review_post_type = get_post_type_object( 'ramp_review' );
		$review_post_type->rewrite['slug'] = 'field-reviews';
		register_post_type( 'ramp_review', $review_post_type );

		// 'articles' => 'projects'
		$article_post_type = get_post_type_object( 'ramp_article' );
		$article_post_type->rewrite['slug'] = 'projects';
		register_post_type( 'ramp_article', $article_post_type );
	},
	100
);
