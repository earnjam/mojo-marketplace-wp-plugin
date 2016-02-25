<?php

function mm_hg_jetpack_onboarding_test() {
	$file = MM_BASE_DIR . 'tests/jetpack-onboarding/jetpack-onboarding.php';
	if ( file_exists( $file ) && 'quickinstall' == get_option( 'mm_brand' ) ) {
		if ( ! mm_ab_test_inclusion( 'jetpack-onboarding-hg-v1', md5( 'jetpack-onboarding-hg-v1' ), 20, WEEK_IN_SECONDS * 4 ) ) {
			mm_ab_test_inclusion( 'jetpack-onboarding-hg-exempt-v1', md5( 'jetpack-onboarding-exempt-hg-v1' ), 25, WEEK_IN_SECONDS * 4 );
			add_option( 'jpstart_wizard_has_run', true );
		} else {
			/*
			This is to avoid the issue with WC dismissing the welcome screen
			*/
			if ( false == get_option( 'mm_wc_screen_hack' ) ) {
				update_user_meta( get_current_user_id(), 'show_welcome_panel', 1 );
			}
			mm_require( $file );
		}
	}
}
add_action( 'init', 'mm_hg_jetpack_onboarding_test', 9 );