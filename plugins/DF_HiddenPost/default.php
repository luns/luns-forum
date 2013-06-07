<?php if (!defined('APPLICATION')) exit();

$PluginInfo['DF_HiddenPost'] = array(
   'Description' => 'Возможность скрывать сообщения',
   'Version' => '1.0',
   'RequiredApplications' => array('Vanilla' => '2.0.18b'),
   'RequiredTheme' => FALSE, 
   'RequiredPlugins' => FALSE,
   'HasLocale' => FALSE,
   'SettingsUrl' => '/plugin/DF_HiddenPost',
   'SettingsPermission' => 'Garden.AdminUser.Only',
   'Author' => "luns",
   'AuthorEmail' => 'info@luns-it.ru',
   'AuthorUrl' => 'http://www.luns-it.ru'
);

class DF_HiddenPost extends Gdn_Plugin {
 
 public function DiscussionController_Render_Before($Sender) {
      $this->PrepareController($Sender);
   }

 public function PostController_Render_Before($Sender) {
      $this->PrepareController($Sender);
   }

 protected function PrepareController($Sender) {
      $Sender->AddJsFile($this->GetResource('js/hidden.js', FALSE, FALSE));
      $Sender->AddCssFile($this->GetResource('design/hidden.css', FALSE, FALSE));
   }

public function DiscussionController_CommentOptions_Handler($Sender) {
      $this->AddHideButton($Sender);
   }

   protected function AddHideButton($Sender) {
      if (!Gdn::Session()->UserID) return;
      
      $Object = !isset($Sender->EventArguments['Comment']) ? $Sender->Data['Discussion'] : $Sender->EventArguments['Comment'];
      $ObjectID = !isset($Sender->EventArguments['Comment']) ? 'Discussion_'.$Sender->Data['Discussion']->DiscussionID : 'Comment_'.$Sender->EventArguments['Comment']->CommentID;
      $HideURL = Url("post/hide/{$Object->DiscussionID}/{$ObjectID}",TRUE);
      $HideText = T('Hide');
      echo <<<QUOTE
      <span class="CommentHide"><a href="{$HideURL}">{$HideText}</a></span>
QUOTE;

   }
    public function Setup() {
   
  	 
	$Structure = GDN::Structure();
    $Structure->Table('DF_HiddenPost')
      ->Column('CommentID', 'int(11)', TRUE, 'key')
      ->Column('UserID', 'int(11)', FALSE, 'key')
      ->Column('Hidden', 'int(1)', TRUE, 'key')
      ->Column('DateUpdated', 'datetime')
      ->Set(TRUE);

   }
    
}
