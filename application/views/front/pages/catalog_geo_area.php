<div class="before-content space_bottom">
    <?php if($alphabet): ?>
    <span class="page-nav-title">Быстрый поиск новостроек</span>
    <ul class="nav nav-pills nav-tabs" role="tablist">
        <li><a href="#po-gorodam" aria-controls="po-gorodam" role="tab" data-toggle="tab">по городам</a></li>
    </ul>
    <div class="clearfix"></div>
    <!-- Tab panes -->
    <div class="tab-content geo-index">
        <div role="tabpanel" class="tab-pane" id="po-gorodam">
            <div class="place space_top">
                <?php $col_count = 3; 
                $col_limit = ceil(count($alphabet) / $col_count); 
                for($col = 0; $col < $col_count; $col++): ?>
                <div class="col-33" data-col="<?= $col ?>">
                    <?php for($l = 0; $l < $col_limit; $l++): 
                        $letter = array_shift($alphabet);
                        ?>
                    <ul data-letter="<?= $_l = array_get($letter, 'letter', '') ?>">
                        <li class="letter"><?= $_l ?></li>
                        <?php foreach (array_get($letter, 'items', []) as $it): ?>
                        <li><a href="<?= $path_url . array_get($it, 'alias', '') ?>"><?= array_get($it, 'label', '') ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endfor; ?>
                </div>
                <?php endfor; ?>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php if(isset($post) && is_array($post)): ?>
    <h1 class="page-name page-name-sm space_top"><?= array_get($post, 'name'); ?></h1>
    <div class="space_bottom"><?= $post['content'] ?></div>
    <?php endif; ?>
</div>