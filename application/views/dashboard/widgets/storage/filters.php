<form action="/admin/storage/" class="fl-filter space_bottom">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="name" class="control-label">Поиск по названию</label>
                        <input type="text" name="name" class="form-control" placeholder="Поиск по названию"/>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="data_range" class="control-label">Диапазон дат</label>
                        <div>
                            <div class="input-group" style="width: 48%;float: left;margin-right: 2%;">
                                <div class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></div>
                                <input type="text" name="date_begin" class="form-control" placeholder="от">
                            </div>
                            <div class="input-group" style="width: 50%;">
                                <div class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></div>
                                <input type="text" name="date_end" class="form-control" placeholder="до">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <div>
                            <label for="tag" class="control-label">Теги</label>
                            <label class="radio-inline"> <input type="radio" name="search_type[]" id="optionsRadios1" value="and" checked> И</label>
                            <label class="radio-inline"> <input type="radio" name="search_type[]" id="optionsRadios1" value="or"> ИЛИ</label>
                        </div>
                        <div>
                            <ul class="methodTags"></ul>
                            <input type="hidden" name="tags" class="mySingleFieldNode" value="">
                            <?php if ($tags): ?>
                                <!--set tags-->
                                <script>FlRegister.set('tags', <?= $tags ?>);</script>
                                <!--/set tags-->
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
            <div class="more_filters" style="display: none;">

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group" style="width: 48%; float: left;">
                            <label for="category" class="control-label">Раздел</label>
                            <select name="file_category_id" class="form-control">
                                <option value="">Все</option>
                                <?php foreach ($categories as $it): ?>
                                    <option value="<?= $it['file_category_id'] ?>"><?= $it['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group" style="width: 48%; float: right;">
                            <label for="parent_id" class="control-label">ИД в разделе</label>
                            <input type="text" name="parent_id" class="form-control" placeholder="ИД в разделе"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" style="width: 48%; float: left;">
                            <label for="proportion_id" class="control-label">Ресайзы</label>
                            <select name="proportion_id" class="form-control">
                                <option value="">Все</option>
                                <option value="-1">Отсутсвует</option>
                                <?php foreach ($proportions as $it): ?>
                                    <option value="<?= $it['proportion_id'] ?>"><?= $it['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group" style="width: 48%; float: right;">
                            <label for="image_width" class="control-label">Ширина</label>
                            <div>
                                <select name="image_width_eq"  class="form-control" style="width: 35%; float: left; padding: 3px;">
                                    <option value="">Все</option>
                                    <option value=">">&gt;</option>
                                    <option value=">=">&gt;&#61;</option>
                                    <option value="=">&#61;</option>
                                    <option value="<">&lt;</option>
                                    <option value="<=">&lt;&#61;</option>
                                </select>
                                <div class="input-group" style="width: 55%; float: right;">
                                    <input type="text" name="image_width" class="form-control">
                                    <div class="input-group-addon">px</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                    </div>
                </div>
                <?php
                $checkbox_list = [
                    ['name' => 'is_description', 'title' => 'Источник'],
                    ['name' => 'is_watermark', 'title' => 'Водяной знак'],
                    ['name' => 'is_tags', 'title' => 'Теги'],
                    ['name' => 'is_alt', 'title' => 'Описание'],
                    ['name' => 'is_category', 'title' => 'Раздел'],
                    ['name' => 'is_square', 'title' => 'Квадратные'],
                ];
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <ul class="fl-filter-checkbox-list">
                                <?php foreach ($checkbox_list as $it): ?>
                                    <li>
                                        <label class="control-label"><?= $it['title'] ?></label>
                                        <label class="radio-inline">
                                            <input type="radio" name="<?= $it['name'] ?>" value="1"> Есть
                                        </label>
                                        <label class="checkbox-inline">
                                            <input type="radio" name="<?= $it['name'] ?>" value="0"> Нет
                                        </label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <ul class="fl-filter-controls">
                        <li><a href="javascript:void(0)" class="toggle_more_filters">Расширенный</a></li>
                        <li><button type="button" class="btn btn-default drop-form">Сброс</button></li>
                        <li><button type="submit" class="btn btn-primary submit-form"><span class="glyphicon glyphicon-search"></span> Фильтр</button></li>
                    </ul>
                </div>
            </div>
        </form>
