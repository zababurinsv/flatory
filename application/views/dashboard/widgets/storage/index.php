<?php
//vdump($settings['section_default']);
?>
<div class="hpanel hblue panel-tabs js-ws" style="display: none;">
    <div class="panel-heading">
        <div class="panel-tools">
            <a class="hidebox" id="destroy_widget_storage"><i class="fa fa-times"></i></a>
        </div>
        <!--Файлы-->
        <ul class="nav nav-tabs space_none js-ws-nav" data-tab-group="storage">
            <?php foreach ($sections as $alias => $it): ?>
                <li<?php if ($settings['section_default'] === $alias) echo ' class="active"'; ?>><a href="javascript:void(0)" data-tab="<?= implode(',', $it['content']) ?>" data-section="<?= $alias ?>" title="<?= $it['title'] ?>"><?= $it['title'] ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="panel-body">
        <div class="space_bottom">
            <a href="javascript:void(0)" id="widget_storage__add" class="btn btn-info pull-right">Добавить</a>
            <ul class="nav nav-pills ws-nav-pills" data-tab-group="storage" data-tab-content="modify_nav">
                <li class="active"><a data-tab="filters">Фильтр</a></li>
                <li><a data-tab="mass_editor">Редактор</a></li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div data-tab-group="storage" data-section-content="images" data-tab-content="filters"><?= $filters ?></div>   
        <div data-tab-group="storage" data-section-content="images" data-tab-content="mass_editor"><?= $mass_editor ?></div>
        <div data-tab-group="storage" data-section-content="docs" data-tab-content="filters"><?= $filters_docs ?></div>   
        <div data-tab-group="storage" data-section-content="docs" data-tab-content="mass_editor"><?= $mass_editor_docs ?></div>
        <div data-tab-group="storage" data-tab-content="upload"><?= $upload ?></div>
        <div data-tab-group="storage" data-tab-content="images"><?= $images ?></div>
        <div data-tab-group="storage" data-tab-content="docs"><?= $docs ?></div>
        <img src="/images/loader.gif" class="center-block space_top space_bottom js-ws-loader" style="opacity: .5;">
        <div class="alert alert-warning js-ws-alert space_none" style="display: none;"></div>
    </div>
</div>
