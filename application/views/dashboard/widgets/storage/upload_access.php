<script>
    $(document).on('ready', function () {
        FlUpload.setAccessByType(<?= json_encode($upload_access) ?>);
    });
</script>