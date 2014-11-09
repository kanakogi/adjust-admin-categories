<?php 
require_once AAC_PLUGIN_DIR . '/admin/admin-functions.php';	 

/**
 * データを保存
 */
if (
    !empty( $_POST ) &&
    is_user_logged_in() &&
    isset( $_POST['_wpnonce'] ) &&
    wp_verify_nonce( $_POST['_wpnonce'], 'create_post' )
) {
    //エラー
    $e = new WP_Error();

    //データをチェック
    extract( $_POST );
    $aac_options = get_option( 'aac_options' );
    $aac_options[$posttype][$posttaxonomy] = array(
        'checked_ontop' => esc_html( $checked_ontop ),
        'change_radiolist' => esc_html( $change_radiolist ),
        'checklist_no_top' => esc_html( $checklist_no_top ),
        'requires' => esc_html( $requires ),
    );
    update_option( 'aac_options', $aac_options );
    $this->aac_options = get_option( 'aac_options' );

    //成功時
    $e->add( 'error', $this->_( "Settings saved", '保存されました' ) );
    set_transient( 'post-updated', $e->get_error_messages(), 3 );
}


//投稿タイプを取得
$post_types = get_post_types(array(), "objects");
//特定の投稿タイプを削除
unset(
      $post_types['attachment'],
      $post_types['revision'],
      $post_types['nav_menu_item'],
      $post_types['acf'], //ACF
      $post_types['wpcf7_contact_form'] //ContactForm7
);

//タクソノミーを取得
$post_taxonomies = get_taxonomies(array(), "objects");
//特定のタクソノミー削除
unset(
      $post_taxonomies['page'],
      $post_taxonomies['post_tag'],
      $post_taxonomies['nav_menu'],
      $post_taxonomies['link_category'],
      $post_taxonomies['post_format']
);

?>

<h2><?php $this->e( 'Adjust Admin Categories Settings', 'Adjust Admin Categories 設定' ) ?></h2>
<?php 
/**
 * エラーメッセージ
 */
//保存成功
if ( $messages = get_transient( 'post-updated' ) ) {
    display_messages( $messages, 'updated' );

//保存失敗
}elseif ( $messages = get_transient( 'post-error' ) ) {
    display_messages( $messages, 'error' );
}
?>
<p><?php $this->e( "The posts screen category box can be adjusted.", '投稿画面のカテゴリーボックスの調整ができます。' ) ?></p>

<ul class="aac_tab">
<?php foreach($post_types as $post_type) : ?>	
    <li class="aac_tab-<?php echo $post_type->name;?>"><?php echo $post_type->labels->name;?></li>
<?php endforeach; ?>
</ul>
<div class="aac_contents">
<?php foreach($post_types as $post_type) : ?>	
<div class="aac_content">
	<h3><?php echo $post_type->labels->name;?></h3>

<?php 
$flg_taxonomy = 0;
foreach($post_taxonomies as $post_taxonomy) : 
	//オブジェクトタイプがタクソノミーを使用できるか調べる
	if( !is_object_in_taxonomy( $post_type->name, $post_taxonomy->name ) ){
		continue;
	}
?>		
<form method='post' action='' class="aac_form">
	<h4 class="taxonomy_title"><?php echo $post_taxonomy->labels->name;?></h4>
	<?php wp_nonce_field( 'create_post' ); ?>
	<table class='form-table'>
		<tr>
			<th style="width:300px"><?php $this->e( "Stop the checkbox from moving", 'チェックボックスが移動するのを停止する' ) ?></th>
			<td>
				<select name='checked_ontop' id='checked_ontop' class='postform'>
					<option class="level-0" value="0" <?php display_selected( $post_type->name, $post_taxonomy->name, 'checked_ontop', 0 ) ?>><?php $this->e( "Deactivate", '停止' ) ?></option>
					<option class="level-0" value="1" <?php display_selected( $post_type->name, $post_taxonomy->name, 'checked_ontop', 1 ) ?>><?php $this->e( "Activate", '有効化' ) ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th style="width:300px"><?php $this->e( "Change checkbox to radio button", 'チェックボックスをラジオボタンにする' ) ?></th>
			<td>
				<select name='change_radiolist' id='change_radiolist' class='postform'>
					<option class="level-0" value="0" <?php display_selected( $post_type->name, $post_taxonomy->name, 'change_radiolist', 0 ) ?>><?php $this->e( "Deactivate", '停止' ) ?></option>
					<option class="level-0" value="1" <?php display_selected( $post_type->name, $post_taxonomy->name, 'change_radiolist', 1 ) ?>><?php $this->e( "Activate", '有効化' ) ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th style="width:300px"><?php $this->e( "Make parent taxonomies unselectable", '親の選択が出来ないようにする' ) ?></th>
			<td>
				<select name='checklist_no_top' id='checklist_no_top' class='postform'>
					<option class="level-0" value="0" <?php display_selected( $post_type->name, $post_taxonomy->name, 'checklist_no_top', 0 ) ?>><?php $this->e( "Deactivate", '停止' ) ?></option>
					<option class="level-0" value="1" <?php display_selected( $post_type->name, $post_taxonomy->name, 'checklist_no_top', 1 ) ?>><?php $this->e( "Activate", '有効化' ) ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th style="width:300px"><?php $this->e( "Required", '必須項目にする' ) ?></th>
			<td>
				<select name='requires' id='requires' class='postform'>
					<option class="level-0" value="0" <?php display_selected( $post_type->name, $post_taxonomy->name, 'requires', 0 ) ?>><?php $this->e( "Deactivate", '停止' ) ?></option>
					<option class="level-0" value="1" <?php display_selected( $post_type->name, $post_taxonomy->name, 'requires', 1 ) ?>><?php $this->e( "Activate", '有効化' ) ?></option>
				</select>
			</td>
		</tr>
	</table>
	
	<input type="hidden" name="posttype" value="<?php echo $post_type->name;?>">
	<input type="hidden" name="posttaxonomy" value="<?php echo $post_taxonomy->name;?>">
	<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php $this->e( 'Save Changes', '変更を保存' ) ?>"  /></p></form>
</form>
<?php 
$flg_taxonomy ++;
endforeach; 

//タクソノミーがなければメニューを削除
if($flg_taxonomy == 0){
?>
<script>
jQuery('.aac_tab-<?php echo $post_type->name;?>').hide();
</script>
<?php 
}
?>

</div>
<?php endforeach; ?>
</div>