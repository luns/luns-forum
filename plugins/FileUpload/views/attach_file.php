<div class="AttachFileWrapper AttachmentWindow">
   <div class="AttachFileLink">
      <a href="javascript:void(0);"><?php echo T('Прикрепить файл'); ?></a>
      <div class="CurrentUploader"></div>
   </div>
   <div class="AttachFileContainer">
      <div class="PrototypicalAttachment" style="display:none;">
         <div class="Attachment">
            <div class="FilePreview"></div>
            <div class="FileHover">
               <div class="FileMeta">
                  <div>
                     <span class="FileName"><?php echo T('Имя файла'); ?></span>
                     <span class="FileSize"><?php echo T('Размер файла'); ?></span>
                  </div>
                  <span class="FileOptions"></span>
                  <a class="InsertImage Hidden"><?php echo T('Вставить картинку'); ?></a>
               </div>
            </div>
         </div>
         <div class="UploadProgress">
            <div class="Foreground"><strong><?php echo T('Загрузка...'); ?></strong></div>
            <div class="Background">&nbsp;</div>
            
         </div>
      </div>
   </div>
</div>
<script type="text/javascript">
   if (GdnUploaders)
      GdnUploaders.Prepare();
</script>