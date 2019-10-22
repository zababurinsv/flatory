<nav class="nav-pagination" aria-label="Page navigation">
    <?php if ($is_show_limit_select && $pagination_limits): ?>  
        <select name="pagination_limit" class="pagination-limit">
            <?php foreach ($pagination_limits as $it): ?>
            <option value="<?= $it ?>"<?php if($pagination_limit === $it) echo ' selected="selected"'; ?>><?= $it ?></option>
            <?php endforeach; ?>
        </select>
    <script>
        (function(){
            $('[name="pagination_limit"]').off('change').on('change', function (e){
                var url = '<?= $base_url ?>';
                url = url.split('amp;').join('');
                url += url.indexOf('?') !== -1 ? '&' : '?';
                url += 'pagination_limit=' + $(this).val();
                location.href = location.protocol + '//' + location.host + url;
            });
        }());
    </script>
    <?php endif; ?>  
    <ul class="pagination">
        <?php foreach ($list as $it): ?>
        <li <?php if(array_get($it, 'current')) echo 'class="active"'; ?>>
            <a href="<?= array_get($it, 'url', '') ?>"><?= array_get($it, 'title', '') ?></a>
        </li>
        <?php endforeach; ?>
    </ul>
</nav>