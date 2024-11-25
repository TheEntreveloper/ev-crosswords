<?php
/*
 *
 *  Template file to load selected crossword
 *  License: MIT (for this module only).
 *  The MIT license is compatible with the plugin license: GPLv2 or later)
 *  Read more: https://en.wikipedia.org/wiki/MIT_License
 * -----------------------------------
 *
 * MIT License
 *
 * Copyright Entreveloper.com (https://entreveloper.com)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
*/

defined('ABSPATH') || exit;

// check if theme contains header (block themes do not)
if (!wp_is_block_theme()) {
    get_header();
} else {
    include EVCWV_PLUGIN_DIR . 'views/crossword/cw-block-header.php';
}

if (have_posts()):
    while (have_posts()): the_post();
?>

        <section class="content-section bg-light" id="about">
            <div class="container text-center">
                <div class="crossword-header">

                    <h2><?php the_title(); ?></h2>

                    <p class="lead mb-5">
                        <?php esc_html_e('Your crossword is ready.', 'ev-crosswords');
                        printf(
                            esc_html__('Use your mouse and keyboard to interact with it (Shift-click changes writing direction or %s)', 'ev-crosswords'),
                            '<button class="button button-primary" onclick="toggle()">Turn</button>'
                        ); ?>

                    </p>
                </div>
                <div class="row">
                    <div class="col-12 col-lg-8">
                        <div id="myParent">
                            <canvas></canvas>
                        </div>
                        <input id="kb" type="text" autocomplete="off" style="position:fixed;left:-1700px;top:0px" ;>
                    </div>
                    <div class="col-6 col-lg-4">
                        <div class="row">
                            <div class="col-12 text-left" id="messages"><?php esc_html_e('Press on a cell to view hints here. Use Shift-click to change writing direction', 'ev-crosswords'); ?></div>
                            <div class="col-12">
                                <button class="button button-info" value="solve" onclick="cw.solve();"><?php esc_html_e('View solution', 'ev-crosswords'); ?></button>
                                <button class="button button-info" value="evaluate" onclick="cw.evaluate();"><?php esc_html_e('Check for mistakes', 'ev-crosswords'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script type="text/javascript">
            jQuery.noConflict();
            jQuery(document).ready(function($) {

                const parent = $('#myParent');

                if (!parent) return;

                const cwidth = parent.width();
                // if (window.screen.width<500) {
                //     cwidth = window.screen.width-25;
                // }

                var cwchosen = getChosenCw();

                const canvas = $('<canvas/>', {
                    'id': 'cwCanvas'
                });

                canvas.attr('width', `${cwidth}px`)
                canvas.attr('height', `${cwidth}px`);
                canvas.css('border', '1px solid #0b0b0b');


                // var canvas = '<canvas id="cwCanvas" width="'+cwidth+'" height="'+cwidth+'" style="border:1px solid #0b0b0b"></canvas>';
                parent.html(canvas);
                crossword('cwCanvas', '<?php echo (esc_html($post->guid)); ?>', function(data) { //cwhtml5/cw
                    $('#messages').html(data);
                });
                // workaround to make keyboards visible and get pressed keys on certain mobile browsers.
                var isMobile = window.matchMedia("only screen and (max-width: 760px)").matches;
                if (!isMobile) { // workaround because window.matchMedia fails at least on older iPads
                    if ("iPad" === window.clientInformation.platform || "iPhone" === window.clientInformation.platform) {
                        isMobile = true;
                    }
                }

                $(window).resize(function() {
                    canvas.width(parent.width());
                    canvas.height(parent.width());
                    // canvas.attr('width', `${parent.width()}px`);
                    // canvas.attr('height', `${parent.width()}px`);
                })

                if (isMobile) {
                    var isChrome = false;
                    cw.setMobile(true);
                    $('#kb').keydown(function(e) {
                        e.preventDefault();
                    });
                    $('#kb').on("input", function() {
                        var c = $(this).val();
                        console.log("input, c is " + c + " isChrome? " + isChrome);
                        if (c != null && c.length > 0) {
                            if (isChrome) {
                                cwkbd(c.charAt(c.length - 1), 1);
                            }
                        }
                    });
                    $('#kb').keyup(function(e) {
                        var c = e.target.value,
                            k = e.originalEvent.keyCode;
                        console.log("keyup, c is " + c);
                        if (c == null || c === "") {
                            c = String.fromCharCode(e.keyCode);
                        }
                        isChrome = (e.keyCode === 229);
                        e.preventDefault();
                        cwkbd(c, k);
                    });
                    document.getElementById("cwCanvas").addEventListener('click', function() {
                        document.getElementById("kb").focus();
                    });

                    function cwkbd(c, k) {
                        var o = {
                            key: c,
                            keyCode: k,
                            mbke: true
                        };
                        console.log("char=" + o.key)
                        cw.keyDown(o);
                    }
                }
            });

            function getChosenCw() {
                const qsParams = new URLSearchParams(window.location.search);
                return qsParams.get('cw');
            }

            function toggle() {
                cw.toggleDir();
                document.getElementById("kb").focus();
            }
        </script>
<?php
    endwhile;
endif;
?>

<?php
if (!wp_is_block_theme()) {
    get_footer();
} else {
    include EVCWV_PLUGIN_DIR . 'views/crossword/cw-block-footer.php';
}
