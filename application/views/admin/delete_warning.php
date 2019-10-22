<?php
/**
 * Popup delet
 */
?>
<!-- Modal -->
<div class="modal fade" id="modal__delete_warning" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
                <div class="modal-header alert-danger">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Удаление</h4>
                </div>
                <div class="modal-body">
                    <span class="label label-danger">Будет удалено:</span>
                    <div class="form-group">
                        <div class="col-sm-10">
                            <p id="modal__delete_warning__title" class="form-control-static"></p>
                        </div>
                    </div>
                    <div class="alert alert-danger" style="margin-bottom: 0;"><strong>Внимание!</strong> Вы уверены, что хотите этого?</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                    <a href="javascript:void(0)" class="btn btn-danger modal__approve">Удалить</a>
                </div>
        </div>
    </div>
</div>
