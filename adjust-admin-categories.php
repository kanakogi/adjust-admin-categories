<?php
/*
Plugin Name: Adjust Admin Categories
Plugin URI: http://www.kigurumi.asia/imake/3603/
Description: Installing this plugin allows you to adjust the behavior of the area below the posts screen category box.
Author: Nakashima Masahiro
Version: 1.0.3
Author URI: http://www.kigurumi.asia
Text Domain: aac
Domain Path: /languages/
*/
define( 'AAC_VERSION', '1.0.2' );
define( 'AAC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'AAC_PLUGIN_NAME', trim( dirname( AAC_PLUGIN_BASENAME ), '/' ) );
define( 'AAC_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );
define( 'AAC_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );

require_once AAC_PLUGIN_DIR . '/includes/class-category-checklist.php';


class adjust_admin_categories {

	private $textdomain = 'aac';
	private $aac_options;
	private $aac_defalt_options = array(
		'checked_ontop' => false, //チェックボックスが移動するのを停止
		'change_radiolist' => false, //カテゴリーをラジオボタンにする
		'checklist_no_top' => false, //親カテゴリーを選択できなくする
		'requires' => false, //カテゴリーを必須項目にする
	);

	public function __construct() {
		$this->init();

		//カテゴリーのチェックボックスにフック
		add_action( 'wp_terms_checklist_args', array( $this, 'wp_terms_checklist_args' ) );

		//カテゴリーを必須項目用にフック
		add_action( 'admin_print_footer_scripts', array( $this, 'category_to_require' ) );

		//管理画面追加
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		// プラグインが有効・無効化されたとき
		register_activation_hook( __FILE__, array( $this, 'activationHook' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivationHook' ) );
	}


	function init() {
		//他言語化
		load_plugin_textdomain( $this->textdomain, false, dirname( AAC_PLUGIN_BASENAME ) . '/languages/' );

		//データを取得
		$this->aac_options = get_option( 'aac_options' );
	}


	/**
	 * カテゴリーのチェックボックス
	 */
	function wp_terms_checklist_args( $args, $post_id = null ) {

		global $post_type;
		//投稿の時のみ
		if ( $post_type === 'post' ) {
			//カテゴリーのチェックボックスが移動するのを停止
			if ( $this->aac_options['checked_ontop'] == true ) {
				$args['checked_ontop'] = false;
			}

			//カテゴリーをラジオボタンにする
			if ( $this->aac_options['change_radiolist'] == true ) {
				$args['walker'] = new Radio_Category_Checklist();
			}

			//親カテゴリーを選択できなくする
			if ( $this->aac_options['checklist_no_top'] == true ) {
				$args['checked_ontop'] = false;
				$args['walker'] = new Notop_Category_Checklist();
			}

			return $args;
		}
	}


	/**
	 * カテゴリーを必須項目にする
	 */
	function category_to_require() {
		global $post_type;
		//投稿の時のみ
		if ( $this->aac_options['requires'] == true && $post_type === 'post' ) {
?>
    <script type="text/javascript">
    jQuery("#post").attr("onsubmit", "return check_category();");
    function check_category(){
        var total_check_num = jQuery("#categorychecklist input:checked").length;
        if(total_check_num == 0){
            alert("<?php _e( 'カテゴリーは必須項目です', $this->textdomain ) ?>");
            return false;
        }
    }
    </script>
    <?php
		}
	}

	/**
	 * 管理画面追加
	 */
	function admin_menu() {
		add_options_page(
			'Adjust Admin Categories', //ページのタイトル
			'Adjust Categories', //管理画面のメニュー
			'manage_options', //ユーザーレベル
			'adjust_admin_categories', //URLに入る名前
			array( $this, 'aac_admin_menu' ) //機能を提供する関数
		);
	}

	function aac_admin_menu() {
		require_once AAC_PLUGIN_DIR . '/admin/admin.php';
	}


	/**
	 * プラグインが有効化されたときに実行
	 */
	function activationHook() {
		if ( !get_option( 'aac_options' ) ) {
			update_option( 'aac_options', $this->aac_defalt_options );
		}
	}

	/**
	 * 無効化ときに実行
	 */
	function deactivationHook() {
		delete_option( 'aac_options' );
	}

}
$adjust_admin_categories = new adjust_admin_categories();
