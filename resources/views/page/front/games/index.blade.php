<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Unity WebGL Player | Btc Farmer</title>
    <link rel="shortcut icon" href="/games/!dependencies/TemplateData/favicon.ico">
    <link rel="stylesheet" href="/games/!dependencies/TemplateData/style.css">
</head>
<body style="background: radial-gradient(circle at right bottom, #84f0ee , #967fef) !important;">
<div id="unity-container" class="unity-desktop">
    <canvas id="unity-canvas" width=1128 height=615></canvas>
    <div id="unity-loading-bar">
        <div id="unity-logo"></div>
        <div id="unity-progress-bar-empty">
            <div id="unity-progress-bar-full"></div>
        </div>
    </div>
    <div id="unity-warning"> </div>
    <div id="unity-footer">
        <div id="unity-webgl-logo"></div>
        <div id="unity-fullscreen-button"></div>
        <div id="unity-build-title">{{$game->name}}</div>
    </div>
</div>
<script>
    var container = document.querySelector("#unity-container");
    var canvas = document.querySelector("#unity-canvas");
    var loadingBar = document.querySelector("#unity-loading-bar");
    var progressBarFull = document.querySelector("#unity-progress-bar-full");
    var fullscreenButton = document.querySelector("#unity-fullscreen-button");
    var warningBanner = document.querySelector("#unity-warning");

    // Shows a temporary message banner/ribbon for a few seconds, or
    // a permanent error message on top of the canvas if type=='error'.
    // If type=='warning', a yellow highlight color is used.
    // Modify or remove this function to customize the visually presented
    // way that non-critical warnings and error messages are presented to the
    // user.
    function unityShowBanner(msg, type) {
        function updateBannerVisibility() {
            warningBanner.style.display = warningBanner.children.length ? 'block' : 'none';
        }
        var div = document.createElement('div');
        div.innerHTML = msg;
        warningBanner.appendChild(div);
        if (type == 'error') div.style = 'background: red; padding: 10px;';
        else {
            if (type == 'warning') div.style = 'background: yellow; padding: 10px;';
            setTimeout(function() {
                warningBanner.removeChild(div);
                updateBannerVisibility();
            }, 5000);
        }
        updateBannerVisibility();
    }

    //EĞER ÇALIŞMIYORSA MESELE BURASI
    var buildUrl = "/games/{{$game->name}}"; //***
    var loaderUrl = buildUrl + "/{{$game->loader_name}}"; //***
    var config = {
        dataUrl: buildUrl + "/{{$game->data_unityweb_name}}", //****
        frameworkUrl: buildUrl + "/{{$game->js_unityweb_name}}", //****
        codeUrl: buildUrl + "/{{$game->wasm_unityweb_name}}", //****
        streamingAssetsUrl: "/games/!dependencies/StreamingAssets",
        companyName: "Zeugma Games",
        productName: "{{$game->name}}",
        productVersion: "0.1",
        showBanner: unityShowBanner,
    };

    // By default Unity keeps WebGL canvas render target size matched with
    // the DOM size of the canvas element (scaled by window.devicePixelRatio)
    // Set this to false if you want to decouple this synchronization from
    // happening inside the engine, and you would instead like to size up
    // the canvas DOM size and WebGL render target sizes yourself.
    // config.matchWebGLToCanvasSize = false;

    if (/iPhone|iPad|iPod|Android/i.test(navigator.userAgent)) {
        // Mobile device style: fill the whole browser client area with the game canvas:

        var meta = document.createElement('meta');
        meta.name = 'viewport';
        meta.content = 'width=device-width, height=device-height, initial-scale=1.0, user-scalable=no, shrink-to-fit=yes';
        document.getElementsByTagName('head')[0].appendChild(meta);
        container.className = "unity-mobile";
        canvas.className = "unity-mobile";

        // To lower canvas resolution on mobile devices to gain some
        // performance, uncomment the following line:
        // config.devicePixelRatio = 1;

    } else {
        // Desktop style: Render the game canvas in a window that can be maximized to fullscreen:

        canvas.style.width = "1128px";
        canvas.style.height = "615px";
    }

    loadingBar.style.display = "block";

    var script = document.createElement("script");
    script.src = loaderUrl;
    script.onload = () => {
        createUnityInstance(canvas, config, (progress) => {
            progressBarFull.style.width = 100 * progress + "%";
        }).then((unityInstance) => {
            loadingBar.style.display = "none";
            fullscreenButton.onclick = () => {
                unityInstance.SetFullscreen(1);
            };
        }).catch((message) => {
            alert(message);
        });
    };
    document.body.appendChild(script);
</script>
<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        // Sayfa yüklendiğinde zamanı kaydet
        let enterTime = new Date().getTime();

        // CSRF token'i meta etiketinden al
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Sayfadan ayrılmadan önce zamanı kaydet
        window.addEventListener('beforeunload', function() {
            let leaveTime = new Date().getTime();
            let deltaTime = leaveTime - enterTime; // Milisaniye cinsinden fark
            deltaTime = deltaTime / 10000;

            // deltaTime değerini sunucuya gönder
            fetch('/update-diamond/{{$game->id}}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ deltaTime: deltaTime })
            });
        });
    });
</script>
</body>
</html>
