$(function() {

	$(document).bind('attachUploadify', function(event, settings) {

		var files = [];
		var trnsfr = Object;
		var settings = settings;
		var total = 0;

		$("#CakeUploadify-"+settings.uuid).uploadify({
			'uploader': '/cake_uploadify/flash/uploadify.swf',
			'script': settings.script,
			'method': 'POST',
			'scriptData': settings,
			'cancelImg': '/cake_uploadify/img/close.png',
			'folder': settings.folder,
			'queueID': 'null',
			'auto': true,
			'width': 112,
			'multi': true,
			'onComplete': onItemComplete,
			'onProgress': updateTrnsfrProgress,
			'onSelectOnce': addFilesToTrnsfer,
			'onSelect': onSelectItems,
			'onAllComplete': cleanUp,
		});

		function addFilesToTrnsfer(event, data) {
			trnsfr = $('#'+settings.uuid+'-trnsfr').trnsfr(files);
		}

		function cleanUp() {
			//$('#'+settings.uuid+'-trnsfr, #'+settings.uuid+'-speed').delay(1000).fadeOut('slow');
		}

		function onSelectItems(event, queueId, fileObj) {
			files.push(fileObj.size);
		}

		function updateTrnsfrProgress(event, queueId, fileObj, data) {
			$('#'+settings.uuid+'-speed').html(data.speed+'KB/s');
			trnsfr.setData(data.allBytesLoaded);
			console.debug(data.allBytesLoaded );
			console.debug(files);
		}

		function onItemComplete(event, queueId, fileObj, response, data) {
//			var file = files.shift();

			var upload = $.parseJSON(response);
			if (upload.Image.length != 0) {
				var width = Number(upload.metadata.width);
				var height = Number(upload.metadata.height);

				var item = $('#'+upload.metadata.uuid+'.uploadBin').append('<div class="uploadItem" style="width: '+ (width + 10) +'px; height: '+ (height + 10) +'px;"><img src="/cake_uploadify/img/close.png" height="25" width="25" alt="Remove Item?" border="0" class="cakeUploadify-removeItem" /><img src="/generated/images/'+upload.Image.slug+'_w'+ width +'_h100.jpg" class="uploadItemImage" /><input type="hidden" name="'+upload.metadata.inputName+'[Image]['+upload.Image.id+'][image_id]" value="'+upload.Image.id+'" /><input type="hidden" name="'+upload.metadata.inputName+'[Image]['+upload.Image.id+']['+upload.metadata.position_field+']" value="" class="uploadPosition" /></div>');

				$('.uploadItem').hover(function() {
					$(this).find('.cakeUploadify-removeItem').fadeIn('fast');
				}, function() {
					$(this).find('.cakeUploadify-removeItem').fadeOut('fast');
				});

				$('.cakeUploadify-removeItem').click(function(){
					$(this).closest('.uploadItem').fadeOut('slow', function() {
						$(this).remove();
					});
				});
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