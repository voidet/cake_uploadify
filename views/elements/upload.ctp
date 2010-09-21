<div id="<?php echo $settings['uuid']; ?>-fileQueue"></div>
<div id="<?php echo $settings['uuid']; ?>-uploadBin" class="uploadBin"></div>
<script type="text/javascript">
	$(document).trigger('attachUploadify', <?php echo $this->Javascript->object($settings); ?>);
</script>