<?php
/**
 * Errors form (validation)
 * @param array $json_errors - массив полей с ошибками
 */
?>
<script>
    (function() {
        // errors lables
        var errors = <?= $errors ?>;
        // перебираем поля ошибок и расставляем соответствующие классы в родительские .form-group
        for (var field in errors) {
            $('[name="' + field + '"]').parents('.form-group').addClass('has-error');
            // message
            $('[name="' + field + '"]').before('<label class="control-label">'+ errors[field] +'</label>');
        }
    }())

</script>