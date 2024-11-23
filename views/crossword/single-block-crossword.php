<?php

$template_html = get_the_block_template_html();
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open();
$cwtitle = isset($_POST['cwtitle']) ? sanitize_text_field($_POST['cwtitle']) : '';
?>
<section class="content-section bg-light" id="about">
    <div class="container text-center">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <h2><?php esc_html_e('Crossword', 'ev-crosswords'); echo(" - ".wp_kses($cwtitle, array()));?></h2>
                <p class="lead mb-5"><?php esc_html_e('Your crossword is ready.', 'ev-crosswords');
                    printf(esc_html__('Use your mouse and keyboard to interact with it (Shift-click changes writing direction or %s)', 'ev-crosswords'),
                        '<button class="btn btn-primary" onclick="toggle()">Turn</button>');?>

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
                    <div class="col-12 text-left" id="messages"><?php esc_html_e('Press on a cell to view hints here. Use Shift-click to change writing direction', 'ev-crosswords');?></div>
                    <div class="col-12">
                        <button class="btn btn-info" value="solve" onclick="cw.solve();"><?php esc_html_e('View solution', 'ev-crosswords');?></button>
                        <button class="btn btn-info" value="evaluate" onclick="cw.evaluate();"><?php esc_html_e('Check for mistakes', 'ev-crosswords');?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    jQuery.noConflict();
    jQuery(document).ready(function ($) {
        var cwidth = 500;
        if (window.screen.width<500) {
            cwidth = window.screen.width-25;
        }
        var cwchosen = getChosenCw();
        var canvas = '<canvas id="cwCanvas" width="'+cwidth+'" height="'+cwidth+'" style="border:1px solid #0b0b0b"></canvas>';
        $('#myParent').html(canvas);
        crossword('cwCanvas', '<?php echo(esc_html($post->guid));?>', function(data) { //cwhtml5/cw
            $('#messages').html(data);
        });
        // workaround to make keyboards visible and get pressed keys on certain mobile browsers.
        var isMobile = window.matchMedia("only screen and (max-width: 760px)").matches;
        if (!isMobile) { // workaround because window.matchMedia fails at least on older iPads
            if ("iPad" === window.clientInformation.platform || "iPhone" === window.clientInformation.platform) {
                isMobile = true;
            }
        }
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
                var c = e.target.value, k = e.originalEvent.keyCode;
                console.log("keyup, c is " + c);
                if (c == null || c === "") {
                    c = String.fromCharCode(e.keyCode);
                }
                isChrome = (e.keyCode === 229);
                e.preventDefault();
                cwkbd(c, k);
            });
            document.getElementById("cwCanvas").addEventListener('click', function () {
                document.getElementById("kb").focus();
            });
            function cwkbd(c, k) {
                var o = {key: c, keyCode: k, mbke: true};
                console.log("char="+o.key)
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
<?php echo $template_html; // phpcs:ignore WordPress.Security.EscapeOutput ?>

<?php wp_footer(); ?>
</body>
</html>
