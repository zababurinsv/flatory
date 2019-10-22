<!DOCTYPE html>
<html>
    <head>
        <title><?= $title ?></title>
        <meta charset="utf-8" />
        <meta name="description" content="<?= $meta_description; ?>" />
        <meta name="keyword" content="<?= $meta_keywords; ?>" />
        <meta name="copyright" content="<?= $meta_copywrite; ?>" />
        <!--<meta name="viewport" content="width=device-width,initial-scale=1.0" />-->
        <link rel="shortcut icon" href="<?= $favicon; ?>" type="image/x-icon"/>
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800&subset=latin,cyrillic-ext,latin-ext,cyrillic,greek-ext,greek,vietnamese' rel='stylesheet' type='text/css'>
        <!--CSS-->
        <?php
        foreach ($styles as $file)
            echo "\t", link_tag(((strpos($file, 'http') === FALSE) ? $styles_path : '') . $file), "\n";
        ?>
        <!--JavaScript-->
        <?php
        foreach ($scripts as $file)
            echo "\t", script_tag(((strpos($file, 'http') === FALSE) ? $scripts_path : '') . $file), "\n";
        ?>
        <?php if (ENVIRONMENT === ''): ?>
        <?php endif; ?>
    </head>
    <body>
        <div class="wrapper">
            <!-- Header -->
            <?= $header ?>
            <!-- Header #END /-->
            <!-- content -->
            <div class="container">
                <?= $content ?>
            </div>
            <div class="push"></div>
        </div>
        <!-- content #END /-->
        <!-- footer -->
        <div class="footer">
            <?= $footer ?>    
        </div>
        <!-- footer #END /-->
        <!--CSS-->
        <?php
        foreach ($styles_bottom as $file)
            echo "\t", link_tag(((strpos($file, 'http') === FALSE) ? $styles_path : '') . $file), "\n";
        ?>
        <!--JavaScript-->
        <?php
        foreach ($scripts_bottom as $file)
            echo "\t", script_tag(((strpos($file, 'http') === FALSE) ? $scripts_path : '') . $file), "\n";
        ?>
        <?= $after_body ?>
        <?php // if (ENVIRONMENT !== 'development'): ?>
        <?php if (ENVIRONMENT === 'production'): ?>
            <!--ggl adv codes-->
            <script async='async' src='https://www.googletagservices.com/tag/js/gpt.js'></script>
            <script>
                var googletag = googletag || {};
                googletag.cmd = googletag.cmd || [];
                // for top banner
                googletag.cmd.push(function() {
                    googletag.defineSlot('/135692971/flatory-topline1', [970, 90], 'div-gpt-ad-1477851160662-0').addService(googletag.pubads());
                    googletag.pubads().enableSingleRequest();
                    googletag.pubads().collapseEmptyDivs();
                    googletag.enableServices();
                });
                googletag.cmd.push(function() { googletag.display('div-gpt-ad-1477851160662-0'); });
                // \for top banner
                // for middle banner
                googletag.cmd.push(function() {
                    googletag.defineSlot('/135692971/topline_podpoiskom_250', [1000, 250], 'div-gpt-ad-1481140745463-0').addService(googletag.pubads());
                    googletag.pubads().enableSingleRequest();
                    googletag.enableServices();
                });
                googletag.cmd.push(function() { googletag.display('div-gpt-ad-1481140745463-0'); });
                // \for middle banner
                // for left banner
                googletag.cmd.push(function() {
                    googletag.defineSlot('/135692971/flatory_240x400_left', [240, 400], 'div-gpt-ad-1477944838078-0').addService(googletag.pubads());
                    googletag.pubads().enableSingleRequest();
                    googletag.pubads().collapseEmptyDivs();
                    googletag.enableServices();
                });
                googletag.cmd.push(function() { googletag.display('div-gpt-ad-1477944838078-0'); });
                // \for left banner
                // for right banner
                googletag.cmd.push(function() {
                    googletag.defineSlot('/135692971/flatory_neboskreb1_left', [160, 600], 'div-gpt-ad-1477947026674-0').addService(googletag.pubads());
                    googletag.pubads().enableSingleRequest();
                    googletag.pubads().collapseEmptyDivs();
                    googletag.enableServices();
                });
                googletag.cmd.push(function() { googletag.display('div-gpt-ad-1477947026674-0'); });
                // \for right banner
            </script>
            <!--\ggl adv codes-->
            <script type="text/javascript">

                var _gaq = _gaq || [];
                _gaq.push(['_setAccount', 'UA-49634317-1']);
                _gaq.push(['_setDomainName', 'flatory.ru']);
                _gaq.push(['_trackPageview']);

                (function () {
                    var ga = document.createElement('script');
                    ga.type = 'text/javascript';
                    ga.async = true;
                    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                    var s = document.getElementsByTagName('script')[0];
                    s.parentNode.insertBefore(ga, s);
                })();

            </script>
            <!-- Yandex.Metrika counter -->
            <script type="text/javascript">
                (function (d, w, c) {
                    (w[c] = w[c] || []).push(function () {
                        try {
                            w.yaCounter24521213 = new Ya.Metrika({id: 24521213,
                                webvisor: true,
                                clickmap: true,
                                trackLinks: true,
                                accurateTrackBounce: true});
                        } catch (e) {
                        }
                    });

                    var n = d.getElementsByTagName("script")[0],
                            s = d.createElement("script"),
                            f = function () {
                                n.parentNode.insertBefore(s, n);
                            };
                    s.type = "text/javascript";
                    s.async = true;
                    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

                    if (w.opera == "[object Opera]") {
                        d.addEventListener("DOMContentLoaded", f, false);
                    } else {
                        f();
                    }
                })(document, window, "yandex_metrika_callbacks");
            </script>
            <noscript><div><img src="//mc.yandex.ru/watch/24521213" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
            <!-- /Yandex.Metrika counter -->
        <?php endif; ?>
    </body>
</html>