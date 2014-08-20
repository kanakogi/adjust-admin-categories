<?php 
require_once AAC_PLUGIN_DIR . '/admin/admin-functions.php';	 
?>

<h2><?php _e( 'Adjust Admin Categories 設定', $this->textdomain ) ?></h2>
<p><?php _e( '投稿画面のカテゴリーボックスの調整ができます。', $this->textdomain ) ?></p>
<form method='post' action=''>
	<?php wp_nonce_field( 'create_post' ); ?>
	<table class='form-table'>
		<tr>
			<th style="width:300px"><?php _e( 'チェックボックスが移動するのを停止する', $this->textdomain ) ?></th>
			<td>
				<select name='checked_ontop' id='checked_ontop' class='postform'>
					<option class="level-0" value="0" <?php display_selected( 'checked_ontop', 0 ) ?>><?php _e( '停止', $this->textdomain ) ?></option>
					<option class="level-0" value="1" <?php display_selected( 'checked_ontop', 1 ) ?>><?php _e( '有効化', $this->textdomain ) ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th style="width:300px"><?php _e( 'チェックボックスをラジオボタンにする', $this->textdomain ) ?></th>
			<td>
				<select name='change_radiolist' id='change_radiolist' class='postform'>
					<option class="level-0" value="0" <?php display_selected( 'change_radiolist', 0 ) ?>><?php _e( '停止', $this->textdomain ) ?></option>
					<option class="level-0" value="1" <?php display_selected( 'change_radiolist', 1 ) ?>><?php _e( '有効化', $this->textdomain ) ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th style="width:300px"><?php _e( '親カテゴリーを選択できなくする', $this->textdomain ) ?></th>
			<td>
				<select name='checklist_no_top' id='checklist_no_top' class='postform'>
					<option class="level-0" value="0" <?php display_selected( 'checklist_no_top', 0 ) ?>><?php _e( '停止', $this->textdomain ) ?></option>
					<option class="level-0" value="1" <?php display_selected( 'checklist_no_top', 1 ) ?>><?php _e( '有効化', $this->textdomain ) ?></option>
				</select>
			</td>
		</tr>
	</table>

	<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( '変更を保存', $this->textdomain ) ?>"  /></p></form>
</form>
