<?php
/**
 * Popup delet
 */
?>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="form-horizontal" role="form" method="post">
                <div class="modal-header alert-danger">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Удаление</h4>
                </div>
                <div class="modal-body">
                    <span class="label label-danger">Будет удалено:</span>
                    <div class="form-group">
                        <div class="col-sm-10">
                            <p id="del_current__title" class="form-control-static"></p>
                        </div>
                    </div>
                    <input type="hidden" id="del_current__id" name="id" value="">
                    <div class="alert alert-danger" style="margin-bottom: 0;"><strong>Внимание!</strong> Данные будут удалены безвозвратно!</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-danger">Удалить</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    // удаляем елемент
    $('.del_current').on('click', function() {
        var title = $(this).parent('td').parent('tr').find('.title_item').html();
        var id = $(this).parent('td').parent('tr').find('.ID').html();
        $('#del_current__title').html(title)
        $('#del_current__id').val(id)
        $('#myModal').modal();
    });
</script>