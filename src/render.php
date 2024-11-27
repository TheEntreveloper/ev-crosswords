<?php
/**
 * PHP file to use when rendering the block type on the server to show on the front end.
 *
 * The following variables are exposed to the file:
 *     $attributes (array): The block attributes.
 *     $content (string): The block default content.
 *     $block (WP_Block): The block instance.
 *
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

$cwtitle = isset($_POST['cwtitle']) ? sanitize_text_field($_POST['cwtitle']) : '';
$content = $cwtitle.'
<section class="content-section bg-light" id="about">
    <div class="container text-center">
        <div class="row">
            <div class="col-lg-10 mx-auto">';
$content .= '<h2>'.esc_html( translate('Crossword', 'ev-crosswords'))." - ".wp_kses($cwtitle, array()).'</h2>'.
'<p class="lead mb-5">'.esc_html(translate('Your crossword is ready.', 'ev-crosswords'));
$content .=
    esc_html(translate('Use your mouse and keyboard to interact with it (Shift-click changes writing direction or %s)', 'ev-crosswords')).
        '<button class="btn btn-primary" onclick="toggle()">Turn</button>

</p>
</div>
</div>
<div class="row">
    <div class="col-12 col-lg-8">
        <div id="myParent"></div>
        <input id="kb" type="text" autocomplete="off" style="position:fixed;left:-1700px;top:0px";>
    </div>
    <div class="col-6 col-lg-4">
        <div class="row">
            <div class="col-12 text-left" id="messages">'.esc_html(translate('Press on a cell to view hints here. Use Shift-click to change writing direction',
        'ev-crosswords')).'</div>
            <div class="col-12">
                <button class="btn btn-info" value="solve" onclick="cw.solve();">'.esc_html(translate('View solution', 'ev-crosswords')).'</button>
                <button class="btn btn-info" value="evaluate" onclick="cw.evaluate();">'.esc_html(translate('Check for mistakes', 'ev-crosswords')).'</button>
            </div>
        </div>
    </div>
</div>
</div>
</section>';

echo wp_kses_post( $content );
