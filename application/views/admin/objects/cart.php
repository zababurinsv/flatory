<form action="/admin/cart/save_carts/<?= $id ?>" id="save_cart" method="post">
    <div class="form-group">
        <?php foreach ($images_simple_upload as $it): ?>
            <?= $it ?>
        <?php endforeach; ?>
    </div>
    <input type="hidden" name="id" value="<?= $id ?>" />
    <button type="submit" class="btn btn-sm btn-success">Сохранить</button>
</form>
<script type="text/javascript">
(function (){
    $('#save_cart').on('submit', function (){
        $('.text_image').each(function (){
            $(this).siblings('.text_image__val').val($(this).text());
            $(this).siblings('.file_image__val').val($(this).siblings('[name="file_id"]').val());
        });
        $(this).submit();
    });
}());
</script>