<?php echo $form->input('CakeUploadify-'.$settings['uuid'], array('type' => 'file', 'label' => $settings['label'], 'class' => 'uploadify '.$settings['uuid'])); ?>
<div id="<?php echo $settings['uuid']; ?>-trnsfr"></div>
<div id="<?php echo $settings['uuid']; ?>" class="uploadBin"></div>
<div id="<?php echo $settings['uuid']; ?>-speed" class="uploadSpeed"><span></span> KB/s</div>
<script type="text/javascript">
	$(function() {
		$(document).trigger('attachUploadify', <?php echo $this->Javascript->object($settings); ?>);
		$('.uploadBin').sortable({ opacity: 0.6, tolerance: 'pointer', items: '.uploadItem' });
	});
</script>