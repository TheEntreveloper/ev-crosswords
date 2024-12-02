const evcwAjaxCall = async (endpoint, queryData) => {

    var data;

    if (queryData instanceof FormData) {
        data = queryData;
    } else {
        data = new FormData();
        for (const [key, value] of Object.entries(queryData)) {
            data.set(key, value);
        }
    }

    data.set('action', endpoint);
    // data.set('nonce', dmnobj.dmn_nonce);

    let response, error = null;

    try {
        const res = await fetch(evcw_obj.ajax_url, {
            method: "POST", // *GET, POST, PUT, DELETE, etc.
            // headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: data,
        });

        if (!res.ok) {
            error = res.status;
            throw new Error(`Response status: ${res.status}`);
        }

        response = await res.json();
        // stationsData.value = json.markers;

    } catch (e) {
        error = e;
        console.error(e.message);
    }
    return { response, error };
};



const evcwAiGenerateWidget = document.getElementById("evcwPostEditorConfig");
if (evcwAiGenerateWidget) {
    const prompt = evcwAiGenerateWidget.querySelector('input');
    const searchButton = evcwAiGenerateWidget.querySelector('a');
    const nonceField = document.getElementById('evcw_editor_nonce');
    const wordlistEl = evcwAiGenerateWidget.querySelector('textarea');
    const generateCrossword = evcwAiGenerateWidget.querySelector("#generateCrossword");
    const rows = evcwAiGenerateWidget.querySelector("#cwRows");
    const cols = evcwAiGenerateWidget.querySelector("#cwCols");

    /**
     * Call AI endpoint
     */
    searchButton.addEventListener('click', async function (e) {
        e.preventDefault();
        const { error, response } = await evcwAjaxCall('evcw_ai_generate_ajax_controller', {
            prompt: prompt.value,
            evcw_editor_nonce: nonceField.value
        });
        console.log(response);
        if (!error && response.success) {
            console.log(response);
            wordlistEl.value = response.data;
        }

    });

    /**
   * Call Crossword API 
   */
    generateCrossword.addEventListener('click', async function (e) {
        e.preventDefault();
        const { error, response } = await evcwAjaxCall('evcw_api_generate_ajax_controller', {
            wordlist: wordlistEl.value,
            evcw_editor_nonce: nonceField.value,
            rows: rows.value,
            cols: cols.value
        });
        console.log(response);
        if (!error && response.success) {
            console.log(response);

        }

    });
}


var cw = null;
var cwPath = null;

const initCrossword = () => {
    const parent = document.getElementById('myParent');

    const cwCanvas = document.getElementById('cwCanvas');

    if (!cwCanvas) return;

    const cwidth = parent.getBoundingClientRect().width;

    var cwchosen = getChosenCw();

    cwCanvas.setAttribute('width', `${cwidth}px`);
    cwCanvas.setAttribute('height', `${cwidth}px`);

    cwPath = cwCanvas.dataset.cw

    cw = new Crossword(cwCanvas, function (data) { 
        document.getElementById('messages').innetHTML = data;
    });

    // window.addEventListener('resize', function(event) {
    //     cwCanvas.setAttribute('width', `${parent.getBoundingClientRect().width}px`);
    //     cwCanvas.setAttribute('height', `${parent.getBoundingClientRect().width}px`);
    //     cwCanvas.style.width = `${parent.getBoundingClientRect().width}px`;
    //     cwCanvas.style.height = `${parent.getBoundingClientRect().width}px`;
    //     //     cwCanvas.height(parent.width());
    // }, true);

    cw.load(cwPath, cw);
}


// Tabs
function openCrosswordTab(evt, tabName) {
   
    evt.preventDefault();
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";

    if(tabName == 'Preview' && cw === null) {
        initCrossword();
    }
}

// Get the element with id="defaultOpen" and click on it
document.getElementById("defaultOpen")?.click();

// document.addEventListener("DOMContentLoaded", (event) => {
//     initCrossword();
// });


jQuery.noConflict();
jQuery(document).ready(function ($) {

    $(window).resize(function() {
        $('#cwCanvas').width($("#myParent").width());
        $('#cwCanvas').height($("#myParent").width());
        // canvas.attr('width', `${parent.width()}px`);
        // canvas.attr('height', `${parent.width()}px`);
    })

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


