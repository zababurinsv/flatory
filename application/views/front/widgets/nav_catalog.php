<nav class="nav-catalog">
    <ul class="list-inline">
        <li><a href="/moskva/" data-url="moskva">Новостройки Москвы</a></li>
        <li><a href="/novaya-moskva/" data-url="novaya-moskva">Новостройки Новой Москвы</a></li>
        <li><a href="/moskovskaya-oblast/" data-url="moskovskaya-oblast">Новостройки Московской области</a></li>
    </ul>
    <?php if(isset($segment) && $segment): ?>
    <script>
        (function (){
            $('.nav-catalog [data-url="'+ '<?= $segment ?>' +'"]').parent('li').addClass('active');
        }());
    </script>
    <?php endif ?>
</nav>
