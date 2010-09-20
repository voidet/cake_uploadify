$(function() {
	$(document).bind('attachUploadify', function(event, settings) {
		$(".uploadify."+settings.uuid).uploadify({
			'uploader': '/cake_uploadify/flash/uploadify.swf',
			'script': settings.script,
			'method': 'POST',
			'cancelImg': '/cake_uploadify/img/cancel.png',
			'folder': settings.folder,
			'queueID': settings.uuid+'-fileQueue',
			'auto': true,
			'multi': true,
			'onComplete': updateFileInfo,
		});

		function updateFileInfo(name, filePath, size, creationDate, modificationDate, type, response, data) {

		}

	});


});