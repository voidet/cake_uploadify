<?php echo $form->input($settings['model'].'.CakeUploadify-'.$settings['uuid'], array('type' => 'file', 'label' => $settings['label'], 'class' => 'uploadify '.$settings['uuid'])); ?>
<div id="<?php echo $settings['uuid']; ?>-trnsfr"></div>
<div id="<?php echo $settings['uuid']; ?>" class="uploadBin"><?php
	if (!empty($settings['items'])) {
		foreach ($settings['items'] as $item) {
?>
<div id="Uploadify-item-<?php echo $item['id']; ?>" class="uploadItem" style="width: <?php echo $settings['width'] + 10; ?>px; height: <?php echo $settings['height'] + 10; ?>px;">
	<img src="/cake_uploadify/img/close.png" height="25" width="25" alt="Remove Item?" border="0" class="cakeUploadify-removeItem" />
	<img src="/generated/images/<?php echo $item['Image']['slug']; ?>_w<?php echo $settings['width']; ?>_h<?php echo $settings['height']; ?>.jpg" class="uploadItemImage" />
	<input type="hidden" name="'+upload.metadata.inputName+'['+upload.Image.id+'][uuid]" value="'+settings.uuid+'" />
	<input type="hidden" name="'+upload.metadata.inputName+'['+upload.Image.id+'][image_id]" value="'+upload.Image.id+'" />
	<input type="hidden" name="'+upload.metadata.inputName+'['+upload.Image.id+']['+upload.metadata.position_field+']" value="" class="uploadPosition" />
	<?php
		if (!empty($settings['extrafields'])) {
			foreach ($settings['extrafields'] as $field) {

			}
		}
	?>
</div>
<?php
		}
	}
?></div>
<div id="<?php echo $settings['uuid']; ?>-speed" class="uploadSpeed"><span></span> KB/s</div>
<script type="text/javascript">
	$(function() {
		$(document).trigger('attachUploadify', <?php echo $this->Javascript->object($settings); ?>);
		$('.uploadBin').sortable({ opacity: 0.6, tolerance: 'pointer', items: '.uploadItem' });
	});
</script>