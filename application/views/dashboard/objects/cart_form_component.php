<div contenteditable="true" class="text_image"><?= element ('alt', $data, '') ?></div>
<input type="hidden" class="text_image__val" name="text_<?= $index ?>" value="<?= element ('alt', $data, '') ?>"/>
<input type="hidden" class="file_image__val" name="file_<?= $index ?>" value="<?= element ('file_id', $data, '') ?>"/>
