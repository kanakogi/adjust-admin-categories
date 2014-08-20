<?php 
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

    extract( $_POST );
    $aac_options = array(
        'checked_ontop' => esc_html( $checked_ontop ),
        'change_radiolist' => esc_html( $change_radiolist ),
        'checklist_no_top' => esc_html( $checklist_no_top ),
    );
    update_option( 'aac_options', $aac_options );
    $this->aac_options = get_option( 'aac_options' );

    //成功時
    $e->add( 'error', __( '保存されました', $this->textdomain ) );
    set_transient( 'post-updated', $e->get_error_messages(), 3 );
}


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

//エラー表示
function display_messages( $_messages, $_state ) {
?>
    <div class="<?php echo $_state; ?>">
        <ul>
            <?php foreach ( $_messages as $message ): ?>
                <li><?php echo esc_html( $message ); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php
}


//selectedを表示
function display_selected( $_name, $_value ) {
    $options = get_option( 'aac_options' );
    if ( $options[$_name] == $_value ) {
        echo 'selected="selected"';
    }
}

