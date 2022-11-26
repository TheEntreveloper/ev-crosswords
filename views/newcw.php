<?php
?>
<div id="dashboard-widgets" class="metabox-holder">

<div class="postbox-container">
    <div class="postbox ">
        <?php if ( current_user_can( 'upload_files' ) ) : ?>
        <form action="<?php menu_page_url( 'evcwv-plugin-settings' ) ?>" enctype="multipart/form-data" method="post">
            <?php wp_nonce_field( 'evcw-add', '_evcw-add-cw' ); ?>
            <div class="postbox-header">
                <h2 class="hndle ui-sortable-handle"><?php esc_html_e('Upload a Crossword file in EV CW Format', 'ev-crosswords');?></h2>
            </div>
            <div class="inside">
                <div class="input-text-wrap" id="title-wrap">
                <input type="file" name="cwdata"><br>
                </div>
                <div class="input-text-wrap" id="description-wrap">
                <input type="text" id="cwname" name="cwname" placeholder="<?php esc_html_e('Crossword name', 'ev-crosswords');?>" required><br>
                </div>
                <div class="textarea-wrap" id="description-wrap">
                <textarea name="descr" id="content" rows="3" cols="40" maxlength="70" placeholder="<?php esc_html_e('Description', 'ev-crosswords');?>" required></textarea>
                </div>
                <?php submit_button(__('Upload Crossword', 'ev-crosswords')); ?>
            </div>
        </form>
        <hr>
        <div class="inside">
        <p class="submit">
            <div><?php esc_html_e("While you can understand the EV crossword format easily and create crosswords manually, it is easier with the EV Crossword making tool");?>.<br>
                <?php printf(esc_html__('You can learn more about the format and the tool %1$s here %2$s or just %3$s download the tool %4$s'),
                    '<a href="https://github.com/TheEntreveloper/crosswords" target="_blank">',
                    '</a>',
                    '<a href="https://github.com/TheEntreveloper/crosswords/blob/main/maker/maker.zip">',
                    '</a>');?>  .
            </div>
        </p>
        </div>
        <?php endif; ?>
    </div>
</div>

</div>
