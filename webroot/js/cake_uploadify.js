$(function() {
	$(document).bind('attachUploadify', function(event, settings) {
		$(".uploadify."+settings.uuid).uploadify({
			'uploader': '/cake_uploadify/flash/uploadify.swf',
			'script': settings.script,
			'method': 'POST',
			'scriptData': {'uuid': settings.uuid},
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
				console.debug($('#'+upload.uuid+'-uploadBin'));
				$('#'+upload.uuid+'-uploadBin').append('<img src="/generated/images/'+upload.Image.slug+'_w100_h100.jpg" />');
			}

		}

	});


});