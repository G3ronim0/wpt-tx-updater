<?php
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

/**
 * Update translation in Admin Bar
 *
 * @since 1.0.1
 */
add_action( 'wp_ajax_wptxu_update_translation', 'wptxu_update_translation' );
function wptxu_update_translation() {

	$project = absint( $_POST['project'] );
	$nonce = $_POST['nonce'];

	$project = get_post( $project );

	if ( ! wp_verify_nonce( $nonce, 'wptxu-nonce' ) ) {
		wp_die( __( 'Cheatin&#8217; uh?', 'wpt-tx-updater' ) );
	}

	$lang_code = get_locale();
	if ( 'de_DE_formal' === $lang_code ) {
		$lang_code = 'de';
	}

		// Translations informations from transifex.org
	$project_tx = new WPTXU_Transifex_API( $project->post_name );
	$project_tx_infos = $project_tx->_infos_details();

	if ( is_object( $project_tx_infos ) ) {

		 foreach ( $project_tx_infos->resources as $resource ) {

			$project_tx_infos_lang = $project_tx->_infos_by_lang( $resource->slug, $lang_code );

			$translation_content = $project_tx->get_translation( $resource->slug, $lang_code );

			$translation_to_po = new WPTXU_Translation( $project->ID, $project_tx_infos_lang, $resource->slug, $lang_code, $translation_content->content );

			$translation_to_po = $translation_to_po->make_translation();

		}
	} else {

		echo wptxu_http_notices( $project_tx_infos );

	}

	die();

}
