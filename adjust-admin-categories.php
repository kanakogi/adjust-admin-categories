<?php
/*
Plugin Name: Adjust Admin Categories
Plugin URI: http://www.kigurumi.asia/imake/3603/
Description: Installing this plugin allows you to adjust the behavior of the area below the posts screen categoryand custom taxonomy box.
Author: Nakashima Masahiro
Version: 2.2.0
Author URI: https://www.kigurumi.asia
Text Domain: aac
Domain Path: /languages/
*/
define( 'AAC_VERSION', '2.2.0' );
define( 'AAC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'AAC_PLUGIN_NAME', trim( dirname( AAC_PLUGIN_BASENAME ), '/' ) );
define( 'AAC_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );
define( 'AAC_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );

require_once AAC_PLUGIN_DIR . '/includes/class-category-checklist.php';


class adjust_admin_categories {

    private $textdomain = 'aac';
    private $aac_options;
    private $aac_defalt_options =  array(
            'post' => array(
                'category' => array(
                    'checked_ontop' => 0,
                    'change_radiolist' => 0,
                    'checklist_no_top' => 0,
                    'requires' => 0,
                )
            )
        );

    public function __construct() {
        $this->init();

        //カテゴリーのチェックボックスにフック
        add_action( 'wp_terms_checklist_args', array( $this, 'wp_terms_checklist_args' ) );

        //カテゴリーを必須項目用にフック
        add_action( 'admin_print_footer_scripts', array( $this, 'category_to_require' ) );

        //管理画面追加
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );

        // css, js
        add_action('admin_print_styles', array( $this, 'head_css'));
        add_action('admin_print_scripts', array( $this, "head_js"));
        add_action('admin_enqueue_scripts', array( $this, "enqueue_js"));

        // プラグインが有効・無効化されたとき
        register_activation_hook( __FILE__, array( $this, 'activationHook' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivationHook' ) );
    }


    function init() {
        //他言語化
        load_plugin_textdomain( $this->textdomain, false, basename( dirname( __FILE__ ) ) . '/languages/' );

        //データを取得
        $this->aac_options = get_option( 'aac_options' );
    }


    /**
     * カテゴリーのチェックボックス
     */
    function wp_terms_checklist_args( $args, $post_id = null ) {

        global $post_type;
        //投稿タイプに合わせる
        foreach ($this->aac_options as $key => $value) {
            if ($key == $post_type ){
                foreach ( $value as $key2 => $value2) {
                    if( $key2  == $args['taxonomy'] ){

                        //カテゴリーのチェックボックスが移動するのを停止
                        if ( $this->aac_options[$key][$key2]['checked_ontop'] == true ) {
                            $args['checked_ontop'] = false;
                        }

                        //カテゴリーをラジオボタンにする
                        $change_radiolist = false;
                        if ( $this->aac_options[$key][$key2]['change_radiolist'] == true ) {
                            $change_radiolist = true;
                        }

                        //親カテゴリーを選択できなくする
                        $checklist_no_top = false;
                        if ( $this->aac_options[$key][$key2]['checklist_no_top'] == true ) {
                            $args['checked_ontop'] = false;
                            $checklist_no_top = true;
                        }
                        $Checklist = new AAC_Category_Checklist($change_radiolist, $checklist_no_top);
                        $args['walker'] = $Checklist;

                        return $args;
                     }
                }
            }
        }
        return $args;
    }


    /**
     * カテゴリーを必須項目にする
     */
    function category_to_require() {
        global $post_type;
        //投稿の時のみ
        foreach ($this->aac_options as $key => $value) {
            if( $key == $post_type ){
                foreach ( $value as $key2 => $value2) {
                    if( $this->aac_options[$key][$key2]['requires'] ){
                        $term = get_taxonomy( $key2 );
                        $func_key2 = str_replace('-', '_', $key2);
?>
    <script type="text/javascript">
    (function($){
    $("#post").on("submit", function(event){
        if (!check_<?php echo $func_key2; ?>()) {
            alert("<?php echo $term->labels->name; ?><?php $this->e( ' is required', 'は必須項目です' ) ?>");
            event.preventDefault();
            event.stopPropagation();
        }
    });
    function check_<?php echo $func_key2; ?>(){
        var total_check_num = $("#<?php echo $key2; ?>checklist input:checked").length;
        return total_check_num == 0 ? false : true;
    }
    })(jQuery);
    </script>
<?php
                    }
                }
            }
        }
    }


    /**
     * 管理画面追加
     */
    function admin_menu() {
        // print_r($this->aac_options);
        add_options_page(
            'Adjust Admin Categories', //ページのタイトル
            'Adjust Categories', //管理画面のメニュー
            'manage_options', //ユーザーレベル
            'adjust_admin_categories', //URLに入る名前
            array( $this, 'aac_admin_menu' ) //機能を提供する関数
        );
    }

    function aac_admin_menu() {
        add_action('wp_head', 'include_files');
        require_once AAC_PLUGIN_DIR . '/admin/admin.php';
    }


    /**
     * 管理画面CSS追加
     */
    function head_css () {
        if( isset($_REQUEST['page']) && $_REQUEST['page'] == "adjust_admin_categories") {
            wp_enqueue_style( "aac_css", AAC_PLUGIN_URL . '/css/style.css');
        }
    }

    /*
     * 管理画面JS追加
     */
    function head_js () {
        if( isset($_REQUEST['page']) && $_REQUEST['page'] == "adjust_admin_categories") {
            wp_enqueue_script( "aac_js", AAC_PLUGIN_URL . '/js/scripts.js', array("jquery"));
        }
    }

    /*
     * 投稿画面JS追加
     */
    function enqueue_js( $hook_suffix ) {
        if( $hook_suffix === 'edit.php' ) {
            wp_enqueue_script( 'aac_inline_edit', AAC_PLUGIN_URL . '/js/aac-inline-edit.js', array( 'jquery' ), null, true );
        }
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

    /**
     * 翻訳用
     */
    public function e( $text, $ja = null ){
        _e( $text, $this->textdomain );
    }
    public function _( $text, $ja = null ){
        return __( $text, $this->textdomain );
    }
}
$adjust_admin_categories = new adjust_admin_categories();
