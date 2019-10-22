<div class="row">
    <div class="col-md-4">
        <div class="list-group">
            <!--<a href="#" class="list-group-item active"></a>-->
            <?php foreach ($nav as $path => $title): ?>
            <a href="<?= $uri . $path ?>" class="list-group-item"><?= $title ?></a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="col-md-8"></div>
</div>

