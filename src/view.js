jQuery.noConflict();

jQuery(document).ready(function ($) {

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
    crossword('cwCanvas', '<?php echo (esc_html($post->guid)); ?>', function (data) { //cwhtml5/cw
        $('#messages').html(data);
    });
    // workaround to make keyboards visible and get pressed keys on certain mobile browsers.
    var isMobile = window.matchMedia("only screen and (max-width: 760px)").matches;
    if (!isMobile) { // workaround because window.matchMedia fails at least on older iPads
        if ("iPad" === window.clientInformation.platform || "iPhone" === window.clientInformation.platform) {
            isMobile = true;
        }
    }

    $(window).resize(function () {
        canvas.width(parent.width());
        canvas.height(parent.width());
        // canvas.attr('width', `${parent.width()}px`);
        // canvas.attr('height', `${parent.width()}px`);
    })

    if (isMobile) {
        var isChrome = false;
        cw.setMobile(true);
        $('#kb').keydown(function (e) {
            e.preventDefault();
        });
        $('#kb').on("input", function () {
            var c = $(this).val();
            console.log("input, c is " + c + " isChrome? " + isChrome);
            if (c != null && c.length > 0) {
                if (isChrome) {
                    cwkbd(c.charAt(c.length - 1), 1);
                }
            }
        });
        $('#kb').keyup(function (e) {
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
        document.getElementById("cwCanvas").addEventListener('click', function () {
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
