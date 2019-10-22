<div class="dropdown">
    <button type="button" class="btn btn-<?= isset($type) ? $type : 'default' ?>" data-toggle="dropdown">
        <?php if (isset($glyphicon) && $glyphicon): ?><span class="glyphicon <?= $glyphicon ?>"></span> <?php endif; ?>
        <?= $title ?>  <span class="caret"></span>
    </button>
  <ul class="dropdown-menu">
      <?php foreach ($list as $item): ?>
      <li>
          <a href="<?= array_get($item, 'url', 'javascript:void(0)') ?>"><?= array_get($item, 'title', '') ?></a>
      </li>
      <?php endforeach; ?>
  </ul>
</div>

