jQuery(document).ready(function($){
	$('.AttachFileWrapper:not(.PlUpload)').livequery(function(){
		
		var id = new Date().getTime();
		
		var plDefs = $.parseJSON(gdn.definition('PlDefs'));
		
		
		
		$(this).before(
			$('<div class="AttachFileWrapper PlUpload" id="pl_container_'+id+'">' +
				'<div id="pl_filelist_'+id+'"></div>' +
				'<br />' +
				plDefs['AttachFilesTxt'] +
				'<a id="pl_pickfiles_'+id+'" href="#">'+plDefs['SelectFilesTxt']+'</a>' +
				'<a id="pl_uploadfiles_'+id+'" href="#">'+plDefs['UploadFilesTxt']+'</a>' +
				'<div class="basic_upload">'+plDefs['BasicUploadTxt']+'</div>' +
				'<br>'+
			'</div>')
		);
		var that = this;
		$(this).append('<div class="advanced_upload">'+plDefs['AdvancedUploadTxt']+'</div>');
		$(this).find('.advanced_upload').click(function(){
			$(that).hide();
			$('#pl_container_'+id).show();
			return false;
		});
		$(this).hide();
		
		$('#pl_container_'+id+' .basic_upload a').click(function(){
			$(that).show(); 
			$('#pl_container_'+id).hide();
			return false;
		});
		var uploader = new plupload.Uploader({
			runtimes : 'gears,html5,flash,silverlight,browserplus',
			max_file_count: 20,
            unique_names: false,
            multiple_queues: true,
			browse_button : 'pl_pickfiles_'+id,
			pl_container : 'pl_container_'+id,
			max_file_size : plDefs['PreMaxUploadSize']+'mb',
			url : gdn.url('/post/multiupload'),
			flash_swf_url : gdn.url('plugins/PlUpload/js/plupload/plupload.flash.swf'),
			silverlight_xap_url : gdn.url('plugins/PlUpload/js/plupload/plupload.silverlight.xap'),
			filters : [
				{title : "Allowed files", extensions : plDefs['AllowedFileExtensions'].join(',')},
			],
			resize : {width : plDefs['PreMaxUploadWidth'], height : plDefs['PreMaxUploadHeight'], quality : plDefs['PreMaxUploadQuality']}
		});

		$('#pl_uploadfiles_'+id).click(function(e) {
			uploader.start();
			e.preventDefault();
		});

		uploader.init();

		uploader.bind('FilesAdded', function(up, files) {
			$.each(files, function(i, file) {
				if($.inArray(file.name.substring(file.name.indexOf('.')+1),plDefs['AllowedFileExtensions'])!=-1){
					$('#pl_filelist_'+id).append(
						'<div id="' + file.id + '">' +
						'<div class="pl_insert"></div>' +
						file.name + ' (<span class="pl_filesize">' + plupload.formatSize(file.size) + '</span>)'+
						' <b class="pl_progress"></b> '+
					'</div>');
				}
			});

			up.refresh();
		});

		uploader.bind('UploadProgress', function(up, file) {
			$('#' + file.id + " b").html(file.percent + "%");
		});

		uploader.bind('Error', function(up, err) {
			$('#pl_filelist_'+id).append("<div id="+err.file.id+">Error: " + err.code +
				", Message: " + err.message +
				(err.file ? ", File: " + err.file.name : "") +
				" </div>"
			);
			
			var a = $('<a href="#"/>');
				a.text(plDefs['ClearFileTxt']);
				a.bind('click', function() {
					$('#'+err.file.id).remove();
					return false;
			}); 
			$('#'+err.file.id).append(a);

			up.refresh();
		});

		uploader.bind('FileUploaded', function(up, file, data) {
			$('#' + file.id + " b").html("100%");
			response = $.parseJSON(data.response);
			switch(response.MediaResponse.Status){
				case 'success':
					if (response.MediaResponse.Filesize != null) {
						$('#' + file.id + ' .pl_filesize').html(response.MediaResponse.FormatFilesize);
					}
					var img = $('<img />');
					img.attr('src',response.MediaResponse.PreviewImageLocation);

					
					if (response.MediaResponse.FinalImageLocation != '' && response.MediaResponse.FinalImageLocation.match(/\.(gif|jpg|png)$/)) {
						var a = $('<a />');
						a.attr('href', response.MediaResponse.FinalImageLocation)
						a.append(img);
						a.append('<div>'+plDefs['InsertImageTxt'],'</div>');
						a.bind('click', function() {
							var frame = $('#' + file.id + " .pl_insert").parents('form').find('iframe');
							var img = '<img src="'+$(this).attr('href')+'" />';
							if (frame.length && frame.is(':visible')) {
								var editor = frame.contents().find('body');
								editor.html(function(index, content) { return content+img });
								frame.focus();
								editor.focus();
							}else {
								var txtbox = $('#' + file.id + " .pl_insert").parents('form').find('textarea');
								if(txtbox.length)
									txtbox.val(txtbox.val()+img);
							}
							return false;
						}); 
						$('#' + file.id + " .pl_insert").append(a);
					}else{
						$('#' + file.id + " .pl_insert").append(img);
					}
					$('#' + file.id + " .pl_insert").append($('<input />').attr({
						'type': 'hidden',
						'name': 'AttachedUploads[]',
						'value': response.MediaResponse.MediaID
					}));
					$('#' + file.id + " .pl_insert").append($('<input />').attr({
						'type': 'hidden',
						'name': 'AllUploads[]',
						'value': response.MediaResponse.MediaID
					}));
					var a = $('<a href="#"/>');
						a.text(plDefs['ClearFileTxt']);
						a.bind('click', function() {
							$('#' + file.id).remove();
							return false;
					}); 
					$('#' + file.id).append(a);
					break;
				case 'failed':
					$('#' + file.id + " .pl_insert").append(
						response.MediaResponse.StrError+
						' ['+response.MediaResponse.ErrorCode+']'
					);

			}
			
		});
	});
});
