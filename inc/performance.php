<?php

function mm_cache_toggle() {
	if ( isset( $_POST['type'] ) && isset( $_POST['current_status'] ) ) {
		$defaults = array(
			'page'    => 'disabled',
			'browser' => 'disabled',
			'object'  => 'disabled',
		);
		$cache_settings = get_option( 'mm_cache_settings' );
		$cache_settings = wp_parse_args( $cache_settings, $defaults );
		$valid_cache_names = array(
			'browser',
			'page',
			'object',
		);
		$valid_status = array(
			'enabled',
			'disabled',
		);

		if ( in_array( $_POST['current_status'], $valid_status ) ) {
			$new_status = ( 'disabled' == $_POST['current_status'] ) ? 'enabled': 'disabled';
		} else {
			$response = array( 'status' => 'error', 'message' => 'Invalid status.' );
		}

		if ( in_array( $_POST['type'], $valid_cache_names ) && ! isset( $response ) ) {
			if ( 'enabled' == $new_status ) {
				$response = mm_cache_add( $_POST['type'] );
			} else {
				$response = mm_cache_remove( $_POST['type'] );
			}
			if ( 'object' != $_POST['type'] && 'success' == $response['status'] ) {
				save_mod_rewrite_rules();
			}
		} else {
			$response = array( 'status' => 'error', 'message' => 'Invalid cache type.' );
		}

		if ( 'success' == $response['status'] ) {
			$cache_settings[ $_POST['type'] ] = $new_status;
			update_option( 'mm_cache_settings', $cache_settings );
		}
		echo json_encode( $response );
	}
	die;
}
add_action( 'wp_ajax_mm_cache', 'mm_cache_toggle' );

function mm_php_edge_toggle() {
	if ( isset( $_POST['current_status'] ) ) {

		$php_edge_settings = get_option( 'mm_php_edge_settings', 'disabled' );

		$valid_status = array(
			'enabled',
			'disabled',
		);

		if ( in_array( $_POST['current_status'], $valid_status ) ) {
			$new_status = ( 'disabled' == $_POST['current_status'] ) ? 'enabled': 'disabled';
		} else {
			$response = array( 'status' => 'error', 'message' => 'Invalid status.' );
		}

		if ( ! isset( $response ) ) {
			if ( 'enabled' == $new_status ) {
				$response = mm_php_edge_add();
			} else {
				$response = mm_php_edge_remove( );
			}
		}

		if ( 'success' == $response['status'] ) {
			update_option( 'mm_php_edge_settings', $new_status );
		}
		echo json_encode( $response );
	}
	die;
}
add_action( 'wp_ajax_mm_php_edge', 'mm_php_edge_toggle' );

function mm_cache_add( $type = null ) {
	$cache = array();
	if ( ! is_dir( WP_CONTENT_DIR . '/mu-plugins' ) ) {
		mkdir( WP_CONTENT_DIR . '/mu-plugins' );
	}
	switch ( $type ) {
		case 'browser':
			$cache['code'] = 'https://raw.githubusercontent.com/bluehost/endurance-browser-cache/production/endurance-browser-cache.php';
			$cache['location'] = WP_CONTENT_DIR . '/mu-plugins/endurance-browser-cache.php';
			break;

		case 'page':
			$cache['code'] = 'https://raw.githubusercontent.com/bluehost/endurance-page-cache/production/endurance-page-cache.php';
			$cache['location'] = WP_CONTENT_DIR . '/mu-plugins/endurance-page-cache.php';
			break;

		case 'object':
			if ( class_exists( 'memcached' ) || class_exists( 'memcache' ) ) {
				$response = array( 'status' => 'error', 'message' => 'Object cache coming soon.' );
			} else {
				$response = array( 'status' => 'error', 'message' => 'Object cache not available on your hosting plan.' );
			}
			break;
	}
	if ( isset( $cache['code'] ) && isset( $cache['location'] ) ) {
		$request = wp_remote_get( $cache['code'] );
		if ( ! is_wp_error( $request ) ) {
			file_put_contents( $cache['location'], $request['body'] );
			if ( file_exists( $cache['location'] ) ) {
				$response = array( 'status' => 'success', 'message' => ucfirst( $type ) . ' cache added successfully.' );
			}
		}
	}

	if ( ! isset( $response ) ) {
		$response = array( 'status' => 'error', 'message' => 'Unable to add ' . ucfirst( $type ) . ' cache.' );
	}
	return $response;

}

function mm_cache_remove( $type = null ) {
	switch ( $type ) {
		case 'browser':
			$file = WP_CONTENT_DIR . '/mu-plugins/endurance-browser-cache.php';
			break;
		case 'page':
			$file = WP_CONTENT_DIR . '/mu-plugins/endurance-page-cache.php';
			break;
		case 'object':
			$file = WP_CONTENT_DIR . '/object-cache.php';
			break;
	}
	if ( file_exists( $file ) ) {
		if ( unlink( $file ) ) {
			$response = array( 'status' => 'success', 'message' => ucfirst( $type ) . ' cache removed successfully.' );
		} else {
			$response = array( 'status' => 'error', 'message' => 'Could not remove cache file.' );
		}
	} else {
		$response = array( 'status' => 'error', 'message' => 'Cache file does not exist.' );
	}
	return $response;
}

function mm_php_edge_add( $type = null ) {
	$php_edge = array();
	if ( ! is_dir( WP_CONTENT_DIR . '/mu-plugins' ) ) {
		mkdir( WP_CONTENT_DIR . '/mu-plugins' );
	}
	$php_edge['code'] = 'https://raw.githubusercontent.com/bluehost/endurance-php-edge/master/endurance-php-edge.php';
	$php_edge['location'] = WP_CONTENT_DIR . '/mu-plugins/endurance-php-edge.php';

	$request = wp_remote_get( $php_edge['code'] );
	if ( ! is_wp_error( $request ) ) {
		file_put_contents( $php_edge['location'], $request['body'] );
		save_mod_rewrite_rules();
		if ( file_exists( $php_edge['location'] ) ) {
			$php_check = wp_remote_get( site_url() );
			if ( ! is_wp_error( $php_check ) && wp_remote_retrieve_response_code( $php_check ) == '200' ) {
				$response = array( 'status' => 'success', 'message' => 'PHP Edge enabled successfully.' );
			} else {
				$response = array( 'status' => 'error', 'message' => 'Site is incompatible with PHP Edge. Edge removed.' );
				mm_php_edge_remove();
				save_mod_rewrite_rules();
			}
		}
	} else {
		$response = array( 'status' => 'error', 'message' => 'Unable to enable PHP Edge.' );
	}
	return $response;
}
function mm_php_edge_remove( $type = null ) {
	$file = WP_CONTENT_DIR . '/mu-plugins/endurance-php-edge.php';

	if ( file_exists( $file ) ) {
		if ( unlink( $file ) ) {
			$response = array( 'status' => 'success', 'message' => 'PHP Edge successfully disabled.' );
		} else {
			$response = array( 'status' => 'error', 'message' => 'Unable to remove PHP Edge.' );
		}
	} else {
		$response = array( 'status' => 'error', 'message' => 'PHP Edge file does not exist.' );
	}
	return $response;
}
