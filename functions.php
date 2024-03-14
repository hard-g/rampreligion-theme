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

		// Enqueue the theme's JavaScript.
		wp_enqueue_script(
			'rampreligion-theme',
			get_stylesheet_directory_uri() . '/assets/js/frontend.js',
			[],
			$theme_last_modified,
			true
		);

		// Determine current post type, which we'll use to set the current nav menu in JS.
		$current_post_type         = get_post_type();
		$pt_directory_uri          = get_post_type_archive_link( $current_post_type );
		$pt_directory_uri_relative = str_replace( home_url(), '', $pt_directory_uri );

		wp_add_inline_script(
			'rampreligion-theme',
			'window.rampreligion = ' . wp_json_encode(
				[
					'ptDirectoryUri'         => $pt_directory_uri,
					'ptDirectoryUriRelative' => $pt_directory_uri_relative,
				]
			),
			'before'
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
			return 'Article Type: ';
		}

		if ( is_tax( 'ramp_focus_tag' ) ) {
			return 'Tag: ';
		}

		return $prefix;
	}
);
