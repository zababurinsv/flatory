<div class="dropdown">
<button type="button" class="btn btn-sm btn-default" data-toggle="dropdown"><?= $title ?>  <span class="caret"></span></button>
  <ul class="dropdown-menu">
      <?php foreach ($list as $item): ?>
      <li>
          <a href="<?= array_get($item, 'url', 'javascript:void(0)') ?>"><?= array_get($item, 'title', '') ?></a>
      </li>
      <?php endforeach; ?>
  </ul>
</div>

