<div class="content_space" style="position: relative;">
    <img src="/images/loader.gif" id="loader" style="position: absolute; margin-top: 40px; left: 225px; opacity: 0.5">
    <div id="y-map"></div>
    <script>
        (function() {
            var o = <?= isset($map) ? json_encode($map) : false; ?>

            $('#y-map').width('100%');
            $('#y-map').height($('#y-map').width());

            $.subscribe('fl_map_ready', function(e) {
                if (o)
                    FlMap.setObjects(o);
            });
        }());
    </script>
</div>

