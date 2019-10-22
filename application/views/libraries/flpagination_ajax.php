<nav class="pagination-ajax">
    <ul class="pagination">
        <li <?php if ($prev === FALSE): ?>style="display: none;"<?php endif; ?>>
            <a href="#"><span>&laquo;</span></a>
        </li>
        <li class="active"><a href="#"><?= $current ?></a></li>
        <li><a href="#"><?= $current + 1 ?></a></li>
        <li><a href="#"><?= $current + 2 ?></a></li>
        <li <?php if ($next === FALSE): ?>style="display: none;"<?php endif; ?>>
            <a href="#"><span>&raquo;</span></a>
        </li>
        <li><a href="#">Все <span><?= $total ?></span></a></li>
    </ul>
</nav>