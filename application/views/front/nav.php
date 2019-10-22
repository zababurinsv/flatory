<ul class="menu">
    <li><a href="/catalog/" title="Каталог">КАТАЛОГ</a></li>
    <li><a href="/reviews/" title="Обзоры">ОБЗОРЫ</a></li>
    <li><a href="/news/" title="Новости">НОВОСТИ</a></li>
    <li><a href="/articles/" title="Строй-блог">БЛОГ</a></li>
    <li><a href="/kartoteka/" title="Картотека">КАРТОТЕКА</a></li>
    <li><a href="/about/" title="О проекте">О ПРОЕКТЕ</a></li>
    <li><a href="/publicity/" title="Реклама">РЕКЛАМА</a></li>
</ul>
<script>
    // add active class for current link of menu
    (function() {
        var path = window.location.pathname.split('/')[1];
        // crutch for link to catalog 
        var regexp = /(catalog|objectcard|moskva|mo|po-metro)/g;

        if (regexp.exec(path) !== null || path === '')
            $('.menu a[href="/catalog/"]').addClass('active');
        else
            $('.menu a[href^="/' + path + '"]').addClass('active');
        // hide publicity in head nav
        $('.menu a[href="/publicity/"]').first().hide();
    }());
</script>

