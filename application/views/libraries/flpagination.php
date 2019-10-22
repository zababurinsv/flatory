<div class="flatory_pagination">
    <?php if($prev !== FALSE): ?>
    <a href="<?= $base_url . $prev ?>" class="fl_arrow fl_prev"></a>
    <?php endif; ?>
    <i></i>
    <input type="text" name="page" data-max="<?= $total ?>" value="<?= $current ?>">
    <span>из</span>
    <a href="<?= $base_url . $total ?>"><span class="color_green"><?= $total ?></span></a>
    <i></i>
    <?php if($next !== FALSE): ?>
    <a href="<?= $base_url . $next ?>" class="fl_arrow fl_next"></a>
    <?php endif; ?>
    <script>
        (function (){
            /**
             * Pagination
             * @type type
             */
            var pagination = {
                point: '<?= $point ?>',
                targets: {
                  p: '.flatory_pagination',
                  pInput: '.flatory_pagination input'
                }, 
                init: function (){
                    this.setUpListeners();
                },
                setUpListeners: function (){
                    $(this.targets.pInput).off('change').on('change', pagination.goTo);
                },
                goTo: function (e){
                    var self = pagination;
                    var max = Number($(this).data('max'));
                    var go = Number($(this).val());
                    go = go <= max ? go : max;
                    var url = window.location.href.split('&' + self.point)[0];
                    // @todo define separator ? / &
                    url = url.search(/\?/) === -1 ? url + '?' : url;
                    // redirect
                    window.location.href = url + '&' + self.point + '=' + go;
                }
            };
            
            pagination.init();
        }());
    </script>
</div>