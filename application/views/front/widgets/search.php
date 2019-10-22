<form class="search-panel" action="/catalog/search/">
    <div class="row">
        <div class="form-group width-65">
            <input type="text" name="name" class="form-control" placeholder="Поиск по названию">
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-success">Искать</button>
        </div>
        <div class="form-group pull-right">
            <a href="/catalog/map/" class="map-link">Поиск по карте</a>
        </div>
    </div>
    <div class="row">
        <div class="form-group width-25 js-geo-index-wrap">
            <label>Расположение</label>
            <div class="clear-input">
                <input type="text" class="form-control js-geo-index" placeholder="Москва, МО, метро...">
                <span class="clear-input--clear" style="display: none;"></span>
                <div class="js-clear-input-values"></div>
            </div>
        </div>
        <div class="form-group">
            <label>Комнат</label>
            <div>
                <div class="checkbox checkbox-btn">
                    <label><input type="checkbox" name="rooms[]" value="11"> студия</label>
                </div>
                <div class="checkbox checkbox-btn">
                    <label><input type="checkbox" name="rooms[]" value="1"> 1</label>
                </div>
                <div class="checkbox checkbox-btn">
                    <label><input type="checkbox" name="rooms[]" value="2"> 2</label>
                </div>
                <div class="checkbox checkbox-btn">
                    <label><input type="checkbox" name="rooms[]" value="3"> 3</label>
                </div>
                <div class="checkbox checkbox-btn">
                    <label><input type="checkbox" name="rooms[]" value="4"> 4+</label>
                </div>
                <div class="checkbox checkbox-btn">
                    <label><input type="checkbox" name="rooms[]" value="12"> СП</label>
                </div>
            </div>
        </div>
        <div class="form-group width-25">
            <div>
                <label>Цена, руб</label>
                <div class="radio radio-link active">
                    <label><input type="radio" name="price_type" value="0" checked="checked"> за квартиру</label>
                </div>
                <div class="radio radio-link">
                    <label><input type="radio" name="price_type" value="1"> за м²</label>
                </div>
            </div>
            <div class="form-group width-50 padding_none">
                <input type="text" name="cost_min" class="form-control clear-input" placeholder="от" maxlength="11">
            </div>
            <div class="form-group width-50 padding_none">
                <input type="text" name="cost_max" class="form-control clear-input" placeholder="до" maxlength="11">
            </div>
        </div>
        <div class="form-group pull-right">
            <label>Срок сдачи</label>
            <div class="text-right">
                <div class="checkbox checkbox-btn">
                    <label><input type="checkbox" name="complite[]" value="1"> дом сдан</label>
                </div>
                <div class="checkbox checkbox-btn">
                    <label><input type="checkbox" name="complite[]" value="<?= $_curr_year = date("Y"); ?>"> <?= $_curr_year ?></label>
                </div>
                <div class="checkbox checkbox-btn">
                    <label><input type="checkbox" name="complite[]" value="<?= $_curr_year + 1 ?>"> <?= $_curr_year + 1 ?></label>
                </div>
                <div class="checkbox checkbox-btn">
                    <label><input type="checkbox" name="complite[]" value="<?= $_curr_year + 2 ?>"> <?= $_curr_year + 2 ?>+</label>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="modal fade in" id="modal__search_panel" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <form action="" class="geo-index">
                <div class="row">
                    <div class="head-left">
                        <ul class="nav nav-pills">
                            <li class="js-tabs active"><a href="javascript:void(0)" data-tab="msk" data-tab_group="main" data-sub_tab="msk|regions" data-placeholder="Название района">Москва</a></li>
                            <li class="js-tabs"><a href="javascript:void(0)" data-tab="new_msk" data-tab_group="main" data-sub_tab="new_msk|regions" data-placeholder="Название района">Новая Москва</a></li>
                            <li class="js-tabs"><a href="javascript:void(0)" data-tab="mo" data-tab_group="main" data-sub_tab="mo|regions" data-placeholder="Название района">МО</a></li>
                        </ul>
                        <ul class="nav nav-pills">
                            <li class="js-tabs space_left"><a href="javascript:void(0)" data-tab="metro" data-tab_group="main" data-placeholder="Название метро">Метро</a></li>
                        </ul>
                        <div  data-tab_content="new_msk" data-tab_group="main" style="display: none;"></div>
                    </div>
                    <div class="head-right"></div>
                </div>
                <div class="row">
                    <div class="head-left">
                        <div class="search-container">
                            <input type="text" class="js-geo-index-autocomplete" placeholder="Название района">
                        </div>
                    </div>
                    <div class="head-right">
                        <button type="submit" class="btn btn-success pull-right">Сохранить</button>
                    </div>
                </div>
                <div class="row">
                    <div class="head-left">
                        <div data-tab_content="mo" data-tab_group="main" style="display: none;">
                            <ul class="nav nav-pills">
                                <li class="js-tabs active"><a href="#" data-tab="mo|regions" data-tab_group="mo" data-placeholder="Название района">Районы</a></li>
                                <li class="js-tabs"><a href="#" data-tab="mo|cities" data-tab_group="mo" data-placeholder="Название города">Города</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="head-right"></div>
                </div>
                <div class="row" data-tab_content="msk" data-tab_group="main">
                    <div data-tab_content="msk|regions" data-tab_group="msk" data-loaded="1">
                        <div class="row space_bottom_l js-tab-sub-nav">
                            <div class="head-left">
                                <ul class="nav-fast text-justify">
                                    <?php foreach ($sub_nav as $it): ?>
                                        <li><a href="javascript:void(0)" data-field="<?= $_field = array_get($it, 'field', '') ?>" data-value="<?= array_get($it, 'value', '') ?>"><?= array_get($it, 'label', '') ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="head-right">
                            </div>
                        </div>
                        <div class="place js-tab-data-list">
                            <?php
//                            var_dump('222222');die;
                            $col_count = 4;
                            $col_limit = ceil(count($alphabet) / $col_count);
                            for ($col = 0; $col < $col_count; $col++):
                                ?>
                                <div class="col-25" data-col="<?= $col ?>">
                                    <?php
                                    for ($l = 0; $l < $col_limit; $l++):
                                        $letter = array_shift($alphabet);
                                        ?>
                                        <ul data-letter="<?= $_l = array_get($letter, 'letter', '') ?>">
                                            <li class="letter"><?= $_l ?></li>
                                            <?php foreach (array_get($letter, 'items', []) as $it): ?>
                                                <li><span data-district_id="<?= array_get($it, 'district_id', '') ?>" data-field="<?= array_get($it, 'field', '') ?>" data-value="<?= array_get($it, 'value', '') ?>"><?= array_get($it, 'label', '') ?></span></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endfor; ?>
                                </div>
                            <?php endfor; ?>

                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <div class="row" data-tab_content="new_msk" data-tab_group="main" style="display: none;">
                    <div data-tab_content="new_msk|regions" data-tab_group="new_msk">
                        <img src="/images/loader_horizontal.gif">
                    </div>
                </div>
                <div class="row" data-tab_content="mo" data-tab_group="main" style="display: none;">
                    <div data-tab_content="mo|regions" data-tab_group="mo">
                        <img src="/images/loader_horizontal.gif">
                    </div>
                    <div data-tab_content="mo|cities" data-tab_group="mo" style="display: none;">
                        <img src="/images/loader_horizontal.gif">
                    </div>
                </div>
                <div class="row metro_search" data-tab_content="metro" data-tab_group="main" data-tpl="metro" style="display: none;">
                    <img class="metro-loader" src="/images/loader.gif">
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script type="text/javascript" src="/js/<?= js_file_name('search.js') ?>"></script>
