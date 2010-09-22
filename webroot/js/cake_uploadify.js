$(function() {

	$(document).bind('attachUploadify', function(event, settings) {
		$(".uploadify."+settings.uuid).uploadify({
			'uploader': '/cake_uploadify/flash/uploadify.swf',
			'script': settings.script,
			'method': 'POST',
			'scriptData': settings,
			'cancelImg': '/cake_uploadify/img/cancel.png',
			'folder': settings.folder,
			'queueID': settings.uuid+'-fileQueue',
			'auto': true,
			'multi': true,
			'onComplete': updateFileInfo,
		});

		function updateFileInfo(event, queueId, fileObj, response, data) {
			var upload = $.parseJSON(response);
			if (upload.Image.length != 0) {
				$('#'+upload.metadata.uuid+'.uploadBin').append('<div class="uploadItem"><img src="/generated/images/'+upload.Image.slug+'_w100_h100.jpg" /><input type="hidden" name="'+upload.metadata.inputName+'[Image]['+upload.Image.id+'][image_id]" value="'+upload.Image.id+'" /><input type="hidden" name="'+upload.metadata.inputName+'[Image]['+upload.Image.id+']['+upload.metadata.position_field+']" value="" class="uploadPosition" /></div>');
				$('.uploadBin').sortable('refresh');
			}
		}
	});

	$('#PostAdminForm').submit(function() {
		var order = 0;
		$('.uploadBin').each(function() {
			$(this).filter(function() {
					$(this).find('.uploadItem input[type=hidden].uploadPosition');
				}).each(function() {
					order++;
					$(this).attr('value', order);
			});
		});
	});

});