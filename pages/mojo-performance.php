<?php
if ( ! defined( 'WPINC' ) ) { die; }
?>
<div id="mojo-wrapper" class="<?php echo mm_brand( 'mojo-%s-branding' );?>">
<?php
require_once( MM_BASE_DIR . 'pages/header-small.php' );
$defaults = array(
	'page'    => 'disabled',
	'browser' => 'disabled',
	'object'  => 'disabled',
	'php_edge'  => 'disabled',
);

if ( file_exists( WP_CONTENT_DIR . '/mu-plugins/endurance-page-cache.php' ) ) {
	$defaults['page'] = 'enabled';
}

if ( file_exists( WP_CONTENT_DIR . '/mu-plugins/endurance-browser-cache.php' ) ) {
	$defaults['browser'] = 'enabled';
}

if ( file_exists( WP_CONTENT_DIR . '/mu-plugins/endurance-php-edge.php' ) ) {
	$defaults['php_edge'] = 'enabled';
}

$cache_settings = get_option( 'mm_cache_settings' );
$cache_settings = wp_parse_args( $cache_settings, $defaults );
$php_edge_settings = get_option( 'mm_php_edge_settings' );
$php_edge_settings = wp_parse_args( $php_edge_settings, $defaults );
?>
	<main id="main">
		<div class="container">
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-12 col-sm-12">
							<ol class="breadcrumb">
								<li>Performance</li>
							</ol>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							Page Cache
							<p style="padding-top: 15px;">
								<img style="margin: 5px; padding-right: 10px;" class="pull-left" src="<?php echo MM_BASE_URL; ?>tmp/pagecache.svg" />
								When pages are eligible for a full page cache, a copy of the page is created and stored for easy retrieval. This means it skips most of the stuff that makes a page slow.
							</p>
							<br/>
							<?php
							if ( 'enabled' == $cache_settings['page'] ) {
								?>
								<button data-type="page" data-status="enabled" class="mojo-cache-toggle btn btn-primary btn-md">Disable</button>
								<?php
							} else {
								?>
								<button data-type="page" data-status="disabled" class="mojo-cache-toggle btn btn-success btn-md">Enable</button>
								<?php
							}
							?>
						</div>
						<div class="col-xs-12 col-sm-6">
							Browser Cache
							<p style="padding-top: 15px;">
								<img style="margin: 5px; padding-right: 10px;" class="pull-left" src="<?php echo MM_BASE_URL; ?>tmp/browsercache.svg" />
								Browser cache tells a visitors computer to keep a copy of your page assets on their computer, so it does not have to reach back out to the server for the asset.</p>
							<br/>
							<?php
							if ( 'enabled' == $cache_settings['browser'] ) {
								?>
								<button data-type="browser" data-status="enabled" class="mojo-cache-toggle btn btn-primary btn-md">Disable</button>
								<?php
							} else {
								?>
								<button data-type="browser" data-status="disabled" class="mojo-cache-toggle btn btn-success btn-md">Enable</button>
								<?php
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php if ( mm_brand() == 'bluehost' ) { ?>
		<div class="container">
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-12 col-sm-12">
							<ol class="breadcrumb">
								<li>PHP</li>
							</ol>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							PHP Edge
							<p style="padding-top: 15px;">
								<img style="margin: 5px; padding-right: 10px;" class="pull-left" src="<?php echo MM_BASE_URL; ?>tmp/php.png" />
								PHP is the programing language that WordPress is written in. Running the latest and greatest version has large performance and load time improvements. Enabling PHP Edge will allow you to always remain on the latest and greatest version.
							</p>
							<?php
							if ( 'enabled' == $php_edge_settings['page'] ) {
								?>
								<button data-type="php_edge" data-status="enabled" class="mojo-php_edge-toggle btn btn-primary btn-md">Disable</button>
								<?php
							} else {
								?>
								<button data-type="php_edge" data-status="disabled" class="mojo-php_edge-toggle btn btn-success btn-md">Enable</button>
								<?php
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
	</main>
</div>
<script type="text/javascript">
jQuery( document ).ready( function( $ ) {
	$( '.mojo-cache-toggle' ).click( function () {
		var cache_data = {
			'action'         : 'mm_cache',
			'type'           : $( this ).data( 'type' ) ,
			'current_status' : $( this ).data( 'status' )
		}
		var button = $(this);
		$.post( ajaxurl, cache_data, function( cache_response ) {
			try {
				response = JSON.parse( cache_response );
			} catch (e) {
				response = {status:"error", message:"Invalid JSON response."};
			}

			if ( typeof response.message !== 'undefined' ) {
				$( '#mojo-wrapper' ).append( '<div id="mm-message" class="mm-' + response.status + '" style="display:none;">' + response.message + '</div>' );
				$( '#mm-message' ).fadeIn( 'slow', function() {
					if ( response.status == 'success' ) {
						if ( button.data( 'status' ) == 'disabled' ) {
							button.data( 'status', 'enabled' );
							button.removeClass( 'btn-success' );
							button.addClass( 'btn-primary' );
							button.html( 'Disable' );
						} else {
							button.data( 'status', 'disabled' );
							button.removeClass( 'btn-primary' );
							button.addClass( 'btn-success' );
							button.html( 'Enable' );
						}
					}
					setTimeout( function() {
						$( '#mm-message' ).fadeOut( 'fast', function() {
							$( '#mm-message' ).remove();
						} );
					}, 8000 );
				} );
			}

		} );
	} );
} );
</script>
<script type="text/javascript">
jQuery( document ).ready( function( $ ) {
	$( '.mojo-php_edge-toggle' ).click( function () {
		var php_edge_data = {
			'action'         : 'mm_php_edge',
			'type'           : $( this ).data( 'type' ) ,
			'current_status' : $( this ).data( 'status' )
		}
		var button = $(this);
		$.post( ajaxurl, php_edge_data, function( php_edge_response ) {
			try {
				response = JSON.parse( php_edge_response );
			} catch (e) {
				response = {status:"error", message:"Invalid JSON response."};
			}

			if ( typeof response.message !== 'undefined' ) {
				$( '#mojo-wrapper' ).append( '<div id="mm-message" class="mm-' + response.status + '" style="display:none;">' + response.message + '</div>' );
				$( '#mm-message' ).fadeIn( 'slow', function() {
					if ( response.status == 'success' ) {
						if ( button.data( 'status' ) == 'disabled' ) {
							button.data( 'status', 'enabled' );
							button.removeClass( 'btn-success' );
							button.addClass( 'btn-primary' );
							button.html( 'Disable' );
						} else {
							button.data( 'status', 'disabled' );
							button.removeClass( 'btn-primary' );
							button.addClass( 'btn-success' );
							button.html( 'Enable' );
						}
					}
					setTimeout( function() {
						$( '#mm-message' ).fadeOut( 'fast', function() {
							$( '#mm-message' ).remove();
						} );
					}, 8000 );
				} );
			}

		} );
	} );
} );
</script>
