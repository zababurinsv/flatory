<?php
// prepare metro station
if (isset($metro_station) && $metro_station) {
    if (array_get($metro_station, 'params'))
        $metro_station['params'] = json_decode($metro_station['params']);
} else {
    $metro_station = [];
}
?>
<div class="alert alert-info" id="metro_place_alert" style="display: none;">
    Необходимо разместить маркер <span style="display: inline-block;padding: 2px;background: rgba(255,235,61,0.3); border: 1px dashed #444;width: 20px; height: 12px;"></span> над названием станции и расставить точки.
</div>
<div class="space_bottom"></div>
<div style="width: 950px; margin: 0 auto;" id="metro_place"></div>
<script>
    var _FL_m;
    $(document).on('ready', function() {

        if (typeof FlMetro !== 'function')
            throw new Error('Can\'t find FlMetro module!');

        var metro = _FL_m = new FlMetro($('#metro_place')), it = <?= json_encode($metro_station) ?>

        $('#metro_place_alert').show(100);
        // set nav
        metro.renderNav({position: 'relative'}, {addMarker: {}});
        metro.nav.append($('<button>', {type: 'button', class: 'btn btn-xs btn-success', id: 'js-save-metro', text: 'Сохранить'}));
        metro.nav.append($('<button>', {type: 'button', class: 'btn btn-xs btn-danger pull-right', id: 'js-drop-points', text: 'Сбросить все точки'}));
        // add save event
        $('#js-save-metro').on('click', function(e) {

            $.post(location.href, {marker: metro.getStateMarker(), points: metro.getStatePoints()}, function(response) {

                if (response.success) {
                    alert('Успешно сохранено!');
                } else {
                    var err = 'Ошибка!\n';
                    for (var k in response.errors) {
                        err += response.errors[k] + '\n';
                    }
                    alert(err);
                }

            }, 'json').error(function(er) {
                console.log(er);
                alert('Извините, что-то пошло не так.');
            });
        });

        $('#js-drop-points').on('click', function(e) {
            if (confirm('Вы уверены что хотите сбросить все точки?'))
                metro.dropPoints();
        });

        // set marker
        metro.addMarker(typeof it.params === 'object' && it.params ? it.params.marker : false);
        // set points
        if (typeof it.params === 'object' && it.params && $.isArray(it.params.points)) {
            for (var k in it.params.points)
                metro.addPoint(it.params.points[k]);
        }

    });
</script>