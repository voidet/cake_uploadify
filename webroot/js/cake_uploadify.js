$(function() {

	$(document).bind('attachUploadify', function(event, settings) {

		var files = [];
		var trnsfr = Object;
		var settings = settings;
		var total = 0;

		$("#"+settings.model[0]+"CakeUploadify-"+settings.uuid).uploadify({
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
			'onAllComplete': cleanUp
		});

		function addFilesToTrnsfer(event, data) {
			trnsfr = $('#'+settings.uuid+'-trnsfr').trnsfr(files);
			$('#'+settings.uuid+'-trnsfr, #'+settings.uuid+'-speed').fadeIn('fast');
		}

		function cleanUp() {
			$('#'+settings.uuid+'-trnsfr, #'+settings.uuid+'-speed').delay(4000).fadeOut('slow', function() {
				$('#'+settings.uuid+'-trnsfr').empty();
				files = [];
			});
		}

		function onSelectItems(event, queueId, fileObj) {
			files.push(fileObj.size);

			$(document).trigger('attachVideoOptions');
			fields = $('.optionalSettings-'+settings.uuid);
			var hash = new Object();
			fields.each(function() {
				name = $(this).attr('name');
				textValue = escape($(this).attr('value'));
				hash[name] = textValue;
			});

			$("#CakeUploadify-"+settings.uuid).uploadifySettings('scriptData', hash);
		}

		function updateTrnsfrProgress(event, queueId, fileObj, data) {
			$('#'+settings.uuid+'-speed span').html(Math.round(data.speed));
			trnsfr.setData(data.allBytesLoaded);
		}

		function onItemComplete(event, queueId, fileObj, response, data) {
			var upload = $.parseJSON(response);

			if (typeof(upload) == 'object' && settings.upload_type == 'image') {

				single_item = '<div id="'+queueId+'" class="uploadItem"><img src="/cake_uploadify/img/close.png" height="25" width="25" alt="Remove Item?" border="0" class="cakeUploadify-removeItem" /><img src="'+settings.thumb_dir + '/' + upload.slug + settings.thumb_suffix +'.jpg" class="uploadItemImage" /><input type="hidden" name="data'+upload.input_name+'[%upload_id%][uuid]" value="'+settings.uuid+'" /><input type="hidden" name="data'+upload.input_name+'[%upload_id%][image_id]" value="'+upload.id+'" /><input type="hidden" name="data'+upload.input_name+'[%upload_id%][image_position]" value="" class="uploadPosition" />';

				extraitems = '';
				if (settings.extrafields.length > 0) {
					for (i=0; i < settings.extrafields.length; i++) {
						extraitems = extraitems + '<input type="hidden" name="data'+upload.input_name + '[%upload_id%]' + settings.extrafields[i].name+'" value="'+settings.extrafields[i].value+'" />';
					}
				}

				var item = $('#'+settings.uuid+'.uploadBin').append(single_item+extraitems+'</div>');
				$('#'+queueId).attachHover();
				$('#'+queueId).css({top: 0}).fadeIn('slow').animate({'margin-top':'20px'},{queue:false,duration:500,easing:'easeOutBounce'});

				$('.cakeUploadify-removeItem').click(function(){
					$(this).closest('.uploadItem').fadeOut('slow', function() {
						$(this).remove();
					});
				});
				$('.uploadBin').sortable('refresh');
			}

			if (upload.Video.length != 0) {
				$('#'+upload.metadata.uuid+'.uploadBin').append('success');
			}

		}
	});

	$('input[type=submit]').click(function() {
		var order = 0;

		$("#"+settings.id).remove();
		$('.uploadBin').each(function() {

			var upload_id = 0;
			$(this).find('.uploadItem').each(function() {
				$(this).find('input[type=hidden]').each(function() {
					$(this).attr('name', $(this).attr('name').replace(/%upload_id%/, upload_id));
				});
				upload_id++;
			});

			return false;

			$(this).filter(function() {
					$(this).find('.uploadItem input[type=hidden].uploadPosition');
				}).each(function() {
					order++;
					$(this).attr('value', order);
			});

		});
	});

	$(function() {
		$.fn.attachHover = function() {
			$(this).hover(function() {
				$(this).find('.cakeUploadify-removeItem').fadeIn('fast');
			}, function() {
				$(this).find('.cakeUploadify-removeItem').fadeOut('fast');
			});
		}

		$('.uploadItem').attachHover();

	});

});