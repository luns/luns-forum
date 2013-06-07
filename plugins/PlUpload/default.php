<?php if (!defined('APPLICATION')) exit();
// Define the plugin:
$PluginInfo['PlUpload'] = array(
   'Name' => 'PlUpload',
   'Description' => 'This plugin enables for multiple file upload and some file uploads, such as images, to be resized before upload to avoid errors, and disappointment.',
   'Version' => '0.1b',
   'RequiredApplications' => array('Vanilla' => '2.0.18b'),
   'RequiredTheme' => FALSE, 
   'RequiredPlugins' => FALSE,
   'MobileFriendy' => TRUE,
   'HasLocale' => FALSE,
   'SettingsUrl' => '/settings/plupload',
   'SettingsPermission' => 'Garden.Settings.Manage',
   'Author' => "Paul Thomas",
   'AuthorEmail' => 'dt01pqt_pt@yahoo.com'
);

class PlUpload extends Gdn_Plugin {
	
	public function Base_GetAppSettingsMenuItems_Handler($Sender) {
		$Menu = $Sender->EventArguments['SideMenu'];
		$Menu->AddLink('Forum', T('PlUpload'), 'settings/plupload', 'Garden.Settings.Manage');
	}
	
   public function SettingsController_PlUpload_Create($Sender){
		$Sender->Permission('Garden.Settings.Manage');
	
		$Validation = new Gdn_Validation();
		if ($Sender->Form->AuthenticatedPostBack()) {
			$Validation->ApplyRule('Plugins.PlUpload.PreMaxUploadSize', 'Integer');
			$Validation->ApplyRule('Plugins.PlUpload.PreMaxUploadQuality', 'Interger');
			$Validation->ApplyRule('Plugins.PlUpload.PreMaxUploadWidth', 'Integer');
			$Validation->ApplyRule('Plugins.PlUpload.PreMaxUploadHeight', 'Integer');
			if($Sender->Form->GetValue('Plugins.PlUpload.PreMaxUploadSize') < 1){
				$Validation->AddValidationResult('Plugins.PlUpload.PreMaxUploadSize', 'PreMaxUploadSize not greater than 1');
			}
			if($Sender->Form->GetValue('Plugins.PlUpload.PreMaxUploadQuality') < 1 || $Sender->Form->GetValue('Plugins.PlUpload.PreMaxUploadQuality') > 100){
				$Validation->AddValidationResult('Plugins.PlUpload.PreMaxUploadQuality', 'PreMaxUploadQuality is not a valid percentage');
			}
			if($Sender->Form->GetValue('Plugins.PlUpload.PreMaxUploadWidth') < 1){
				$Validation->AddValidationResult('Plugins.PlUpload.PreMaxUploadWidth', 'PreMaxUploadWidth not greater than 1');
			}
			if($Sender->Form->GetValue('Plugins.PlUpload.PreMaxUploadHeight') < 1){
				$Validation->AddValidationResult('Plugins.PlUpload.PreMaxUploadHeight', 'PreMaxUploadHeight not greater than 1');
			}
			$Validation->Validate();
			if(count($Validation->Results())>0){
				$_POST = null;
			}
			$Sender->Form->SetValidationResults($Validation->Results());
		}
		$Config = new ConfigurationModule($Sender);
		$Config->Initialize(array(
			'Plugins.PlUpload.PreMaxUploadSize' => array(
				'Type' => 'int',
				'Control' => 'TextBox',
				'Default' => 10, 
				'Description' => 'The maximum upload size in MB for post attachments to be reduced to'
				),
			'Plugins.PlUpload.PreMaxUploadQuality' => array(
				'Type' => 'int',
				'Control' => 'TextBox',
				'Default' => 100, 
				'Description' => 'The percentage figure of image quality'
				),
			'Plugins.PlUpload.PreMaxUploadWidth' => array(
				'Type' => 'int',
				'Control' => 'TextBox',
				'Default' => 650, 
				'Description' => 'The maximum image width, to reduce to'
				),
			'Plugins.PlUpload.PreMaxUploadHeight' => array(
				'Type' => 'int',
				'Control' => 'TextBox',
				'Default' => 650, 
				'Description' => 'The maximum image height, to reduce to'
				)
		));//saved after postback
		$Sender->AddSideMenu('settings/plupload');
		$Sender->SetData('Title', T('PlUpload Settings'));
		$Sender->ConfigurationModule = $Config;
		$Config->RenderAll();
   }
   	
   public function DiscussionController_Render_Before($Sender) {
      $this->AddUploadScripts($Sender);
   }

   public function PostController_Render_Before($Sender) {
      $this->AddUploadScripts($Sender);
   }
   
   public function AddUploadScripts($Sender){
	   $Sender->Head->AddScript('http://bp.yahooapis.com/2.4.21/browserplus-min.js');
	   $Sender->AddJsFile('plupload.full.js','plugins/PlUpload/js/plupload/');
	   $Sender->AddJsFile('plupload.js','plugins/PlUpload');
	   $PlDefs = array();
	   $PlDefs['AllowedFileExtensions'] = C('Garden.Upload.AllowedFileExtensions',array());
	   $PlDefs['PreMaxUploadSize'] = C('Plugins.PlUpload.PreMaxUploadSize',10);
	   $PlDefs['PreMaxUploadQuality'] = C('Plugins.PlUpload.PreMaxUploadQuality',100);
	   $PlDefs['PreMaxUploadWidth'] = C('Plugins.PlUpload.PreMaxUploadWidth',650);
	   $PlDefs['PreMaxUploadHeight'] = C('Plugins.PlUpload.PreMaxUploadHeight',650);
	   $PlDefs['InsertImageTxt'] = T('Вставить изображение');
	   $PlDefs['SelectFilesTxt'] = T('Выбрать файлы ');
	   $PlDefs['UploadFilesTxt'] = T('Загрузить файлы');
	   $PlDefs['ClearFileTxt'] = T('Очистить');
	   $PlDefs['AttachFilesTxt'] = T('Добавить файлы: ');
	   $PlDefs['BasicUploadTxt'] = T('Перейти к <a href="#">простой загрузке<a>.');
	   $PlDefs['AdvancedUploadTxt'] = T('Перейти к <a href="#">расширенной загрузке<a>.');
	   $Sender->AddDefinition('PlDefs',json_encode($PlDefs));
   }
   
   public function PostController_MultiUpload_Create($Sender){
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		$Sender->DeliveryMethod(DELIVERY_METHOD_JSON);
		$Sender->DeliveryType(DELIVERY_TYPE_VIEW);
		$UploadDir = MediaModel::PathUploads().DS.'FileUpload';
		$CleanTargetDir = true; 
		$MaxFileAge = 5 * 3600; 
		@set_time_limit(5 * 60);

		$Chunk = GetIncomingValue('chunk',0);
		$Chunks = GetIncomingValue('chunks',0);
		$FileName = preg_replace('/[^\w\._]+/', '_', GetIncomingValue('name',''));
		$ExtensionPos = strrpos($FileName, '.');
		$Name = substr($FileName, 0, $ExtensionPos);
		$Extension = strtolower(substr($FileName, $ExtensionPos));
		$FileName = 'chunk_'.md5($Name).'_'.Gdn::Session()->UserID.'_'.$Count.$Extension;
		try {
			  $AllowedExtensions = C('Garden.Upload.AllowedFileExtensions',array());
			 if (!in_array(substr($Extension,1), $AllowedExtensions) && !in_array('*',$AllowedExtensions))
				throw new PlUploadErrorException("Uploaded file type is not allowed.", 11, '???');
				
			if ($Chunks < 2 && file_exists($UploadDir . DS . $FileName)) {
				$Count = 1;
				while (file_exists($UploadDir.DS.'chunk_'.md5($Name).'_'.Gdn::Session()->UserID.'_'.$Count . $Extension))
					$Count++;

				$FileName = 'chunk_'.md5($Name).'_'.Gdn::Session()->UserID.'_'.$Count.$Extension;
			}
			
			$FilePath = $UploadDir.DS.$FileName;
			$FilePathOut = $UploadDir.DS.md5($Name).$Extension;
			
			if ($CleanTargetDir && is_dir($UploadDir) && ($Dir = opendir($UploadDir))) {
				while (($File = readdir($Dir)) !== false) {
					$TmpFile = $UploadDir . DS . $File;
					if (preg_match('/\.part$/', $File) && (filemtime($TmpFile) < time() - $MaxFileAge) && ($TmpFile != "{$FilePath}.part")) {
						@unlink($TmpFile);
					}
				}

			closedir($dir);
			} else {
				throw new PlUploadErrorException("Failed to open upload directory",100,'???');
			}
			$MaxUploadSize = Gdn_Upload::UnformatFileSize(C('Garden.Upload.MaxFileSize', '1G'));
			$ContentType = GetValue('HTTP_CONTENT_TYPE',$_SERVER,GetValue('HTTP_CONTENT_TYPE',$_SERVER));
			if(strpos($ContentType, "multipart") !== false) {
				if (GetValueR('file.tmp_name',$_FILES) && is_uploaded_file($_FILES['file']['tmp_name'])) {
					$FileSize = $_FILES['file']['size'];
					$FileType = $_FILES['file']['type'];
					 
					 if ($FileSize > $MaxUploadSize) {
						$Message = sprintf(T('The uploaded file was too big (max %s).'), Gdn_Upload::FormatFileSize($MaxUploadSize));
						throw new PlUploadErrorException($Message, 11, '???');
					 }
					$Out = fopen("{$FilePath}.part", $Chunk == 0 ? "wb" : "ab");
					if ($Out) {
						$In = fopen($_FILES['file']['tmp_name'], "rb");
						
						if ($In) {
							while ($Buffer = fread($In, 4096))
								fwrite($Out, $Buffer);
						} else {
							throw new PlUploadErrorException("Failed to open input stream.",101,'???');
						}
						fclose($In);
						fclose($Out);
						@unlink($_FILES['file']['tmp_name']);
					} else {
						throw new PlUploadErrorException("Failed to open output stream.",102,'???');
					}
				}
			}else{
				$Out = fopen("{$FilePath}.part", $Chunk == 0 ? "wb" : "ab");
				if ($Out) {
					$In = fopen("php://input", "rb");

					if ($In) {
						$Size = 0;
						while ($Buffer = fread($In, 4096)){
							fwrite($Out, $Buffer);
							$Size += 4096;
							if(Gdn_Upload::UnformatFileSize($Size.'B')>Gdn_Upload::FormatFileSize($MaxUploadSize)){
								fclose($In);
								fclose($Out);
								@unlink("{$FilePath}.part");
								$Message = sprintf(T('The uploaded file was too big (max %s).'), Gdn_Upload::FormatFileSize($MaxUploadSize));
								throw new PlUploadErrorException($Message, 11, '???');
								break;
							}
						}
							
					} else {
						throw new PlUploadErrorException("Failed to open input stream.",101,'???');
					}
					fclose($In);
					fclose($Out);
				}
			
			}
			
			 $SaveFilename = md5(microtime()).$Extension;
			 $SaveFilename = '/FileUpload/'.substr($SaveFilename, 0, 2).'/'.substr($SaveFilename, 2);
			 
			 $this->EventArguments['Path'] = $FilePath;
			 $Parsed = Gdn_Upload::Parse($SaveFilename);
			 $this->EventArguments['Parsed'] =& $Parsed;
			 $Handled = FALSE;
			 $this->EventArguments['Handled'] =& $Handled;
			 $this->FireEvent('UploadSaveAs');
			 $SavePath = $Parsed['Name'];
					  
			 if (!$Handled) {
				// Build save location
				$SavePath = MediaModel::PathUploads().$SaveFilename;
				if (!is_dir(dirname($SavePath)))
				   @mkdir(dirname($SavePath), 0777, TRUE);
				if (!is_dir(dirname($SavePath)))
				   throw new PlUploadErrorException("Internal error, could not save the file.", 9, $FileName);
				
				// Move to permanent location
				$MoveSuccess = @rename("{$FilePath}.part", $SavePath);
				
				if (!$MoveSuccess)
				   throw new PlUploadErrorException("Internal error, could not move the file.", 9, $FileName);            
			 }
			 
			 // Get the image dimensions (if this is an image).
			 list($ImageWidth, $ImageHeight) = MediaModel::GetImageSize($SavePath);

					  
			 // Save Media data
			 $Media = array(
				'Name'            => $Name,
				'Type'            => $FileType,
				'Size'            => $FileSize,
				'ImageWidth'      => $ImageWidth,
				'ImageHeight'     => $ImageHeight,
				'InsertUserID'    => Gdn::Session()->UserID,
				'DateInserted'    => date('Y-m-d H:i:s'),
				'StorageMethod'   => 'local',
				'Path'            => $SaveFilename
			 );
			 $MediaModel = new MediaModel();
			 $MediaID = $MediaModel->Save($Media);
					  
			 $FinalImageLocation = '';
			 $PreviewImageLocation = MediaModel::ThumbnailUrl($Media);

			 $MediaResponse = array(
				'Status'             => 'success',
				'MediaID'            => $MediaID,
				'Filename'           => $Name,
				'Filesize'           => $FileSize,
				'FormatFilesize'     => Gdn_Format::Bytes($FileSize,1),
				'PreviewImageLocation' => Url($PreviewImageLocation),
				'FinalImageLocation' => Url(MediaModel::Url($Parsed['Name']))
			 );
		} catch (PlUploadErrorException $e) {
      
			 $MediaResponse = array(
				'Status'          => 'failed',
				'ErrorCode'       => $e->getCode(),
				'Filename'        => $e->getFilename(),
				'StrError'        => $e->getMessage()
			 );
			 if (!is_null($e->getApcKey()))
				$MediaResponse['ProgressKey'] = $e->getApcKey();
			 
			 if ($e->getFilename() != '???')
				$MediaResponse['StrError'] = '('.$e->getFilename().') '.$MediaResponse['StrError'];
		}
		  

		
		die(json_encode(array('MediaResponse'=>$MediaResponse)));
	}
	
	function Setup(){
		if(!(C('EnabledPlugins.FileUploadDetect') || C('EnabledPlugins.FileUpload') || class_exists('MediaModel')))
			throw new Exception(T('The FileUpload/FileUploadDetect plugin is required'));
	}
}

class PlUploadErrorException extends Exception {
   protected $Filename;
   protected $ApcKey;
   
   public function __construct($Message, $Code, $Filename, $ApcKey = NULL) {
      parent::__construct($Message, $Code);
      $this->Filename = $Filename;
      $this->ApcKey = $ApcKey;
   }
   
   public function getFilename() {
      return $this->Filename;
   }

   public function getApcKey() {
      return $this->ApcKey;
   }
}
