<?php echo $form->input('CakeUploadify-'.$settings['uuid'], array('type' => 'file', 'label' => $settings['label'], 'class' => 'uploadify '.$settings['uuid'])); ?>
<div id="<?php echo $settings['uuid']; ?>-fileQueue"></div>
<div id="<?php echo $settings['uuid']; ?>" class="uploadBin"></div>
<script type="text/javascript">
	$(document).trigger('attachUploadify', <?php echo $this->Javascript->object($settings); ?>);
	$('.uploadBin').sortable({ opacity: 0.6, tolerance: 'pointer', items: '.uploadItem' });
</script>