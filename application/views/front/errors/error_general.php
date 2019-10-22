<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo $heading; ?></title>
    </head>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800&subset=latin,cyrillic-ext,latin-ext,cyrillic,greek-ext,greek,vietnamese' rel='stylesheet' type='text/css'>
    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: 'Open Sans';
            color: #797979;
        }
        .wrapper {
            width: 1000px;
            margin: 0 auto;
        }
        h1 {
            font-size: 2.4em;
        }
        .content {
            width: 850px;
            margin: 0 auto;
        }
        center {
            display: block;
            margin: 100px auto 40px auto;
        }
        a {
            color: #74c84a;
        }
        .message {
            margin: 0 62px;
            /*text-align: center;*/
        }
        .enter {
            position: absolute;
            bottom: 10px;
            right: 10px;
        }
    </style>
    <body>
        <div class="wrapper">
            <div>
                <a href="/"><img src="/images/new/logo.png" alt="" /></a>
            </div>
            <div class="content">
                <h1><?php echo $status_code; ?></h1>
                <center><img src="/images/sad_little_house.png" alt="Я грустный маленький домик"></center>
                <div class="message">
                    <?php echo $message; ?>
                    <a href="/">На главную</a>
                </div>
            </div>
        </div>
        <?php if(ENVIRONMENT !== 'development'): ?>
        <div class="enter">
            <!--LiveInternet counter--><script type="text/javascript"><!--
            document.write("<a href='http://www.liveinternet.ru/click' "+
            "target=_blank><img src='//counter.yadro.ru/hit?t44.1;r"+
            escape(document.referrer)+((typeof(screen)=="undefined")?"":
            ";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
            screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
            ";h"+escape(document.title.substring(0,80))+";"+Math.random()+
            "' alt='' title='LiveInternet' "+
            "border='0' width='31' height='31'><\/a>")
            //--></script><!--/LiveInternet-->

            <!-- Rating@Mail.ru counter -->
            <script type="text/javascript">
            var _tmr = _tmr || [];
            _tmr.push({id: "2505246", type: "pageView", start: (new Date()).getTime()});
            (function (d, w) {
            var ts = d.createElement("script"); ts.type = "text/javascript"; ts.async = true;
            ts.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//top-fwz1.mail.ru/js/code.js";
            var f = function () {var s = d.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ts, s);};
            if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false); } else { f(); }
            })(document, window);
            </script><noscript><div style="position:absolute;left:-10000px;">
            <img src="//top-fwz1.mail.ru/counter?id=2505246;js=na" style="border:0;" height="1" width="1" alt="Рейтинг@Mail.ru" />
            </div></noscript>
            <!-- //Rating@Mail.ru counter -->

            <!-- Rating@Mail.ru logo -->
            <a href="http://top.mail.ru/jump?from=2505246" target="_blank">
            <img src="//top-fwz1.mail.ru/counter?id=2505246;t=280;l=1"
            style="border:0; margin-left: 5px;" height="31" width="38" alt="Рейтинг@Mail.ru" /></a>
            <!-- //Rating@Mail.ru logo -->
        </div>
        <?php endif; ?>
    </body>
</html>
