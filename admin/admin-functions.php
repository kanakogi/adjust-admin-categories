<?php 
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
function display_selected( $_posttype, $_taxonomy, $_name, $_value ) {
    if($options = get_option( 'aac_options' )){
        if ( !empty($options[$_posttype][$_taxonomy][$_name]) && $options[$_posttype][$_taxonomy][$_name] == $_value ) {
            echo 'selected="selected"';
        }
    }
}

