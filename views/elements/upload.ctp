<div id="<?php echo $settings['uuid']; ?>-fileQueue"></div>
<div class="">
</div>
<script type="text/javascript">
	$(document).trigger('attachUploadify', <?php echo $this->Javascript->object($settings); ?>);
</script>