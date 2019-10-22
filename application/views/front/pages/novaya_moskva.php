<div class="before-content space_bottom">
    <?php if(isset($post) && is_array($post)): ?>
    <h1 class="page-name page-name-sm space_top"><?= array_get($post, 'name'); ?></h1>
    <div class="space_bottom"><?= $post['content'] ?></div>
    <?php endif; ?>
</div>

