<div class="before-content space_bottom">
    <?php if(isset($post) && is_array($post)): ?>
    <h1 class="page-name page-name-sm space_bottom"><?= array_get($post, 'name'); ?></h1>
    <?php endif; ?>
    <?php if(isset($geo_index_list) && $geo_index_list): ?>
    <!--<h2><?= $geo_index_title ?></h2>-->
    <div class="place space_bottom">
        <?php $col_count = 3; 
        $col_limit = ceil(count($geo_index_list) / $col_count); 
        for($col = 0; $col < $col_count; $col++): ?>
        <div class="col-33" data-col="<?= $col ?>">
            <ul class="list-unstyled geo-index-short">
            <?php for($l = 0; $l < $col_limit; $l++):  if(!($it = array_shift($geo_index_list))) continue; ?>            
                <li><a href="<?= $path_url . array_get($it, 'alias', '') ?>"><?= array_get($it, 'name', '') ?> (<?= array_get($it, 'count_objects', '') ?>)</a></li>
            <?php endfor; ?>
            </ul>
        </div>
        <?php endfor; ?>
        <div class="clearfix"></div>
    </div>
    <?php endif; ?>
    <?php if(isset($post) && is_array($post)): ?>
    <div class="space_bottom"><?= $post['content'] ?></div>
    <?php endif; ?>
</div>