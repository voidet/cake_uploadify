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
				$('#'+upload.metadata.uuid+'.uploadBin').append('<img src="/generated/images/'+upload.Image.slug+'_w100_h100.jpg" /><input type="hidden" name="'+upload.metadata.inputName+'[Image][][image_id]" value="'+upload.Image.id+'" /><input type="hidden" name="'+upload.metadata.inputName+'[Image][][image_position]" value="" />');
			}
		}
	});

	$('#PostAdminForm').submit(function() {
		var order = 0;
		$('.uploadBin').each(function() {
			console.debug($(this));
			$(this).filter(function() {
					console.debug($(this).find('input[type=hidden]').attr('name').match('position'));
				}).each(function() {
					order++;
					$(this).attr('value', order)
			});
		});
		return false;
	});

});