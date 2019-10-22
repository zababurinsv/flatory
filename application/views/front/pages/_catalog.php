<!--list controls-->
<div class="content_space">
    <?php if ($is_show_controls): ?>
        <!--result panel-->
        <div class="result_panel">
            <a href="<?= $base_url ?>/?<?= http_build_query(array_except(xss_clean($_GET), array('sd'))) ?>&sd=asc" data-direction="asc" title="По возрастанию" class="control_btn_xs pull-left"><span class="icon_desc"></span></a>
            <a href="<?= $base_url ?>/?<?= http_build_query(array_except(xss_clean($_GET), array('sd'))) ?>&sd=desc" data-direction="desc" title="По убыванию" class="control_btn_xs pull-left"><span class="icon_asc"></span></a>
            <div class="filter_group">
                <label for="">Сортировать по</label>
            </div>
            <div class="filter_group">
                <select name="sf" class="select-styling-simple">
                    <option value="cost">Цена за квартиру</option>
                    <option value="cost_m">Цена за метр</option>
                    <option value="space">Площадь</option>
                    <option value="delivery">Срок ввода</option>
                    <!--<option value="date">По дате</option>-->
                </select>
            </div>
            <a href="<?= $base_url ?>/?<?= http_build_query(array_except(xss_clean($_GET), array('vt'))) ?>&vt=list" title="Список" class="control_btn pull-right catalog_list"><span class="icon_list"></span></a>
            <a href="<?= $base_url ?>/?<?= http_build_query(array_except(xss_clean($_GET), array('vt'))) ?>&vt=tiles" title="Плитка" class="control_btn pull-right catalog_tiles"><span class="icon_tile"></span></a>
            <script>
                // set current view type
                (function() {
                    var app = {
                        // type of view (list or tiles)
                        viewType: '<?= $view_type ?>',
                        // field of sort
                        sortField: 'cost',
                        // direction of sort
                        sortDirection: 'asc',
                        // targets
                        targets: {
                            sortSelect: '.select-styling-simple'
                        },
                        // constructor
                        init: function() {
                            // define sort direction
                            this._sortParams();
                            // active view type
                            $('.' + this.viewType).addClass('active');
                            // active sort direction
                            $('a[data-direction="' + this.sortDirection + '"]').addClass('active');
                            // ui select Sort by
                            
                            $(this.targets.sortSelect).selectmenu({
                                change: function(event, ui) {
                                    var el = ui.item;
                                    app.sortField = $(el)[0]['value'];
                                    var url = app._prepareUrl();

                                    location.href = url + '&sf=' + app.sortField + '&sd=' + app.sortDirection;
                                }
                            });
                            // ui select current Sort by
                            $(this.targets.sortSelect).find('[value="' + this.sortField + '"]').attr('selected', 'selected');
                            $(this.targets.sortSelect).selectmenu( "refresh" );
                        },
                        /**
                         * Prepare url for redirect
                         * @returns {String}
                         */
                        _prepareUrl: function() {
                            var url = document.URL.replace(/&sf=([a-z]+)/i, '');
                            url = url.replace(/&sd=([a-z]+)/i, '');
                            if (url.search('\\?') === -1)
                                url += '?';
                            return url;
                        },
                        /**
                         * Set current sort direction & sort field
                         * @returns {undefined} 
                         */
                        _sortParams: function() {
                            // sort direction
                            var sd = FlHelper.arr.get(FlHelper.Get(), 'sd', this.sortDirection);
                            if (sd !== 'asc' && sd !== 'desc')
                                sd = this.sortDirection;
                            this.sortDirection = sd;
                            
                            // sort field
                            var sf = FlHelper.arr.get(FlHelper.Get(), 'sf', this.sortDirection);
                            var sortField = [];
                            $(this.targets.sortSelect).find('option').each(function(a, b) {
                                var f = $(b).attr('value');
                                sortField.push(f);
                            })
                            if($.inArray(sf, sortField) === -1)
                                sf = this.sortField;
                            this.sortField = sf;
                        }
                    }
                    app.init();
                }());
            </script>
        </div>
        <!--/result panel-->
    <?php endif; ?>
    <?php if (isset($message)): ?>
        <?= $message; ?>
    <?php endif; ?>
</div>
<!--/list controls-->
<?php if($view_type === 'catalog_list'): ?>
<div class="content_space">
<?= $view_objects ?>
</div>
<?php else: ?>
<?= $view_objects ?>
<?php endif; ?>
