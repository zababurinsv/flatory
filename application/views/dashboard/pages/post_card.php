<?php
//vdump($current_section);
?>
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
                        <?php if (isset($is_access_preview) && !!$is_access_preview): ?>
                            <a href="/<?= element('prefix', $category); ?>/preview/<?= array_get($post, 'alias') ?>" title="Предварительный просмотр" target="_blank" class="btn btn-default glyphicon glyphicon-globe"></a>
                        <?php endif ?>
                        <button type="button" name="save_page_form" data-post-action="save_page_form" class="btn btn-success">Сохранить</button>
                    </nav>
                </div>
            </div>
        </header>
    </div>
    <?= $content ?>
    <script>
        (function () {
            $('[data-post-action="save_page_form"]').on('click', function (e) {
                $('#page-form').submit();
            });
        }());
    </script>
</div>