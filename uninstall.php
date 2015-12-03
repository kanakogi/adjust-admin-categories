<?php
/**
 * アンインストール時に実行
 */
// if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ){
// 	exit();
// }

function aac_delete_plugin() {
	delete_option( 'aac_options' );
}
aac_delete_plugin();
