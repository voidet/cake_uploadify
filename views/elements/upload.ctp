<?php

	echo $form->input($settings['model'][0].'.CakeUploadify-'.$settings['uuid'], array('type' => 'file', 'label' => $settings['label'], 'class' => 'uploadify '.$settings['uuid']));
	echo '<div id="'.$settings['uuid'].'-trnsfr"></div>';
	echo '<div id="'.$settings['uuid'].'" class="uploadBin">';

	$input_name = '';
	foreach ($settings['model'] as $path) {
		$input_name .= '[\''.$path.'\']';
	}

	if (!empty($settings['items'])) {
		foreach ($settings['items'] as $item) {
			echo '<div id="Uploadify-item-'.$item['id'].'" class="uploadItem" style="width: '.($settings['width'] + 10).'px; height: '.($settings['height'] + 10).'px;">';
				echo '<img src="/cake_uploadify/img/close.png" height="25" width="25" alt="Remove Item?" border="0" class="cakeUploadify-removeItem" />';
				echo '<img src="/generated/images/'.$item['Image']['slug'].'_p.'.$settings['thumb_suffix'].'.jpg" class="uploadItemImage" />';
				echo '<input type="hidden" name="'.$input_name.'[\'+upload.Image.id+\'][uuid]" value="\'+settings.uuid+\'" />';
				echo '<input type="hidden" name="'.$input_name.'[\'+upload.Image.id+\'][image_id]" value="\'+upload.Image.id+\'" />';

			if (isset($settings['sortable']) && $settings['sortable'] === true) {
				echo '<input type="hidden" name="'.$input_name.'[\'+upload.Image.id+\'][\'+upload.metadata.position+\']" value="" class="uploadPosition" />';
			}

		if (!empty($settings['extrafields'])) {
			foreach ($settings['extrafields'] as $field) {
				echo $this->Form->input($field['name'], array($field['options']));
			}
		}
		echo '</div>';
		}
	}
	echo '</div>';
	echo '<div id="'.$settings['uuid'].'-speed" class="uploadSpeed"><span></span> KB/s</div>';
?>
<script type="text/javascript">
	$(function() {
		$(document).trigger('attachUploadify', <?php echo $this->Javascript->object($settings); ?>);
		<?php
			if (isset($settings['sortable']) && $settings['sortable'] === true) {
				echo '$(".uploadBin").sortable({ opacity: 0.6, tolerance: "pointer", items: ".uploadItem" });';
			}
		?>
	});
</script>