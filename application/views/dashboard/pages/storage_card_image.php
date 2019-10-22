<div class="container-fluid">
    <div class="objectcard-header-wrapp">
        <header id="objectcard-header">
            <div class="row">
                <?php
                if (isset($breadcrumbs) && is_array($breadcrumbs) && $breadcrumbs): $_b_i = 0;
                    $_b_count = count($breadcrumbs);
                    ?>
                    <ol class="breadcrumb">
                        <?php foreach ($breadcrumbs as $it): $_b_i++; ?>
                            <?php if ($_b_i !== $_b_count): ?>
                                <li><a href="<?= array_get($it, 'url') ?>"><?= array_get($it, 'title') ?></a></li>
                            <?php else: ?>
                                <li class="active"><?= array_get($it, 'title') ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ol>
                <?php endif; ?>
                <div class="col-md-6">
                    <h1><?= $title ?></h1>
                </div>
                <div class="col-md-6">
                    <nav>  
                        <button type="button" name="save_page_form" data-object-action="save_page_form" class="btn btn-success">Сохранить</button>
                    </nav>
                </div>
            </div>
        </header>
    </div>
        <div class="row">
            <div class="col-md-9">
                <div class="hpanel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3">
                                <img src="/images/original/<?= $file['file_name'] ?>" alt="" class="img-responsive thumbnail space_bottom">

                            </div>
                            <div class="col-md-9">
                                <b class="space_top space_left_s">Размер</b>

                                <?php if ($proportions): ?>
                                    <b><a href="javascript:void(0)" id="toggle__image_add_size" class="text-info space_left_xs">добавить</a></b>
                                    <!--add size-->
                                    <form action="" id="image_add_size" method="post" style="display: none;">
                                        <table class="table table-no-border space_none">
                                            <tbody>
                                                <tr>
                                                    <td style="width: 50%;">
                                                        <select name="proportion_id" class="form-control">
                                                            <?php foreach ($proportions as $item): ?>
                                                                <option value="<?= $item['proportion_id'] ?>"><?= $item['name'] ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </td>
                                                    <td style="width: 50px;"><input type="checkbox" name="is_watermark" value="1"> ВЗ</td>
                                                    <td>
                                                        <input type="hidden" name="file_id" value="<?= $file['file_id'] ?>">
                                                        <a href="javascript:void(0)" class="btn btn-info" style="min-width: 105px;">Добавить</a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </form>
                                    <!--/ add size-->
                                <?php endif; ?>
                                <div class="space_bottom_xs"></div>
                                <!--edit size-->
                                <table class="table table-no-border space_none">
                                    <tbody>
                                        <?php foreach ($file_proportions as $item): ?>
                                            <tr data-proportion="<?= $item['proportion_id'] ?>">
                                                <td style="width: 25%;"><a href="/images/<?= $item['x'] . 'x' . $item['y'] . '/' . $file['file_name'] ?>" target="_blank"><?= $item['x'] . 'x' . $item['y'] ?></a></td>
                                                <td style="width: 25%;"><?= byte_format($item['size']) ?></th>
                                                <td style="width: 50px;"><input type="checkbox" name="is_watermark" data-watermark="<?= (int) $item['is_watermark'] ?>" <?php if ($item['is_watermark']): ?>checked="checked"<?php endif; ?>> ВЗ</td>
                                                <td>
                                                    <a href="javascript:void(0)" class="btn btn-xs btn-warning save_proportion disabled">Сохранить</a>
                                                    <a href="javascript:void(0)" class="btn btn-xs btn-default delete_proportion"><span class="glyphicon glyphicon-trash"></span></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <div class="space_bottom_xs"></div>
                                <?php if (empty($file_proportions)): ?>
                                    <div class="alert alert-info" id="image_no_size">Изображение доступно только в оригинале. Добавьте необходимый размер.</div>
                                <?php endif; ?>
                                <!--/edit size-->
                            </div>
                        </div>


                        <form action="post" id="storage_card">
                            <div class="form-group">
                                <label>Название</label>
                                <input type="text" name="original_name" class="form-control" value="<?= $file['original_name'] ?>">
                            </div>
                            <div class="form-group">
                                <label>Описание</label>
                                <textarea name="alt" class="form-control" ><?= $file['alt'] ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Источник</label>
                                <textarea name="description" class="form-control ckeditor" ><?= $file['description'] ?></textarea>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="file_id"  value="<?= $file['file_id'] ?>">
                            </div>

                            <div class="form-group">
                                <label>Теги</label>
                                <ul id="methodTags"></ul>
                                <input type="hidden" name="tags" id="mySingleFieldNode" value="<?= $image_tags ?>">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="hpanel">
                    <div class="panel-heading hbuilt">Информация о файле</div>
                    <div class="panel-body">
                        <h5>Используется в:</h5>
                        <?php if (!empty($file_involves)): ?>
                            <ul class="list-unstyled">
                                <?php foreach ($file_involves as $involves): ?>
                                    <li>
                                        <a href="<?= $involves['uri'] ?>" target="_blank" class="space_right_xs" title="<?= $involves['name'] ?>"><span class="glyphicon glyphicon-globe"></span></a>
                                        <a href="<?= $involves['uri_adm'] ?>" target="_blank"><?= $involves['name'] ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="alert alert-info">Файл нигде не используется.</div>
                            <a href="javascript:void(0)" class="btn btn-danger center-block delete_item space_top_xs" data-file_id="<?= $file['file_id'] ?>">Удалить безвозвратно</a>
                        <?php endif; ?>
                        <h5 class="space_top">Параметры:</h5>
                        <ul class="list-unstyled">
                            <li>Размеры: <?= $file['x'] . 'x' . $file['y'] ?></li>
                            <li>Вес: <?= byte_format($file['size']) ?></li>
                            <li>Вес всех файлов: <?= byte_format($total_size) ?></li>
                            <li><hr class="space_top_xl"></li>
                            <li>Дата создания: <?= date("d.m.Y H:i", strtotime($file['created'])) ?></li>
                            <li>Дата обновления: <?= date("d.m.Y H:i", strtotime($file['updated'])) ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
</div>


<!--set tags-->
<script>
<?php if ($tags): ?>
        FlRegister.set('tags', <?= $tags ?>);
<?php endif; ?>
</script>
<!--/set tags-->