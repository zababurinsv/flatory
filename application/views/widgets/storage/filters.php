<div class="panel panel-default space_top_xs panel-body-cover">
    <div class="panel-heading">Фильтр</div>
    <div class="panel-body" style="padding: 0;">
        <form action="/admin/storage/" class="form-horizontal fl-filter">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name" class="col-sm-3 control-label">Поиск</label>
                        <div class="col-sm-9">
                            <input type="text" name="name" class="form-control" placeholder="Поиск"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="data_range" class="col-sm-3 control-label">Диапазон дат</label>
                        <div class="col-sm-9">
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
                    <div class="form-group">
                        <div class="col-sm-3">
                            <div class="text-right">
                                <label for="tag" class="control-label">Теги</label>
                            </div>
                            <div class="pull-right">
                                <label class="radio-inline"> <input type="radio" name="search_type[]" id="optionsRadios1" value="and" checked> И</label>
                                <label class="radio-inline"> <input type="radio" name="search_type[]" id="optionsRadios1" value="or"> ИЛИ</label>
                            </div>
                        </div>
                        <div class="col-sm-9">
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
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="category" class="col-sm-3 control-label">Раздел</label>
                        <div class="col-sm-9">
                            <select name="file_category_id" class="form-control">
                                <option value="">Все</option>
                                <?php foreach ($categories as $it): ?>
                                    <option value="<?= $it['file_category_id'] ?>"><?= $it['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="parent_id" class="col-sm-3 control-label">ИД в разделе</label>
                        <div class="col-sm-9">
                            <input type="text" name="parent_id" class="form-control" placeholder="ИД в разделе"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="proportion_id" class="col-sm-3 control-label">Ресайзы</label>
                        <div class="col-sm-9">
                            <select name="proportion_id" class="form-control">
                                <option value="">Все</option>
                                <option value="-1">Отсутсвует</option>
                                <?php foreach ($proportions as $it): ?>
                                    <option value="<?= $it['proportion_id'] ?>"><?= $it['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row more_filters" style="display: none;">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="is_description" class="col-sm-3 control-label">Источник</label>
                        <div class="col-sm-9">
                            <select name="is_description"  class="form-control">
                                <option value="">Все</option>
                                <option value="1">Да</option>
                                <option value="0">Нет</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="is_watermark" class="col-sm-3 control-label">Водяной знак</label>
                        <div class="col-sm-9">
                            <select name="is_watermark"  class="form-control">
                                <option value="">Все</option>
                                <option value="1">Да</option>
                                <option value="0">Нет</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="is_tags" class="col-sm-3 control-label">Наличие тегов</label>
                        <div class="col-sm-9">
                            <select name="is_tags"  class="form-control">
                                <option value="">Все</option>
                                <option value="1">Да</option>
                                <option value="0">Нет</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="is_alt" class="col-sm-3 control-label">Наличие описания</label>
                        <div class="col-sm-9">
                            <select name="is_alt"  class="form-control">
                                <option value="">Все</option>
                                <option value="1">Да</option>
                                <option value="0">Нет</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="is_category" class="col-sm-3 control-label">Наличие раздела</label>
                        <div class="col-sm-9">
                            <select name="is_category"  class="form-control">
                                <option value="">Все</option>
                                <option value="1">Да</option>
                                <option value="0">Нет</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="is_square" class="col-sm-3 control-label">Квадратные</label>
                        <div class="col-sm-9">
                            <select name="is_square"  class="form-control">
                                <option value="">Все</option>
                                <option value="1">Да</option>
                                <option value="0">Нет</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="image_width" class="col-md-3 control-label">Ширина</label>
                        <div class="col-md-9">
                            <select name="image_width_eq"  class="form-control pull-left" style="width: 35%">
                                <option value="">Все</option>
                                <option value=">">&gt;</option>
                                <option value=">=">&gt;&#61;</option>
                                <option value="=">&#61;</option>
                                <option value="<">&lt;</option>
                                <option value="<=">&lt;&#61;</option>
                            </select>
                            <div class="input-group pull-right" style="width: 55%">
                                <input type="text" name="image_width" class="form-control">
                                <div class="input-group-addon">px</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <a href="javascript:void(0)" class="btn btn-default toggle_more_filters">Дополнительные фильтры</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-primary pull-right submit-form"><span class="glyphicon glyphicon-search"></span> Фильтр</button>
                            <a href="javascript:void(0)" class="btn btn-default pull-right space_right drop-form">Сброс</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

