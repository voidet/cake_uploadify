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

			//Handle Image Uploads
			if (upload.Image.length != 0) {
				var width = Number(upload.metadata.width);
				var height = Number(upload.metadata.height);

				var extrafields = '';
				for (i=0; i < settings.extra_fields.length; i++) {
					extrafields = extrafields + '<input type="hidden" name="'+upload.metadata.inputName+'['+upload.Image.id+']['+settings.extra_fields[i]+']" />';
				}

				var item = $('#'+upload.metadata.uuid+'.uploadBin').append('<div id="'+queueId+'" class="uploadItem" style="width: '+ (width + 10) +'px; height: '+ (height + 10) +'px;"><img src="/cake_uploadify/img/close.png" height="25" width="25" alt="Remove Item?" border="0" class="cakeUploadify-removeItem" /><img src="/generated/images/'+upload.Image.slug+'_w'+ width +'_h100.jpg" class="uploadItemImage" /><input type="hidden" name="'+upload.metadata.inputName+'['+upload.Image.id+'][uuid]" value="'+settings.uuid+'" /><input type="hidden" name="'+upload.metadata.inputName+'['+upload.Image.id+'][image_id]" value="'+upload.Image.id+'" /><input type="hidden" name="'+upload.metadata.inputName+'['+upload.Image.id+']['+upload.metadata.position_field+']" value="" class="uploadPosition" />'+extrafields+'</div>');

				$('#'+queueId).css({top: 0}).fadeIn('slow').animate({'margin-top':'20px'},{queue:false,duration:500,easing:'easeOutBounce'});


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

			if (upload.Video.length != 0) {
				$('#'+upload.metadata.uuid+'.uploadBin').append('success');
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