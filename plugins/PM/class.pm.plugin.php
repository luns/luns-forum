<?php

if (!defined('APPLICATION'))
    exit();


$PluginInfo['PM'] = array(
    'Name' => 'PM',
    'Description' => "Adds a link to PM the author of each discussion",
    'Version' => '20120402',
    'RequiredApplications' => array('Vanilla' => '2.0.18b'),
    'RequiredTheme' => FALSE,
    'RequiredPlugins' => FALSE,
    'HasLocale' => FALSE,
    'SettingsUrl' => '/plugin/PM',
    'SettingsPermission' => 'Garden.AdminUser.Only',
    'Author' => "mcuhq",
    'AuthorEmail' => 'info@mcuhq.com',
    'AuthorUrl' => 'http://mcuhq.com'
);

class PMPlugin extends Gdn_Plugin {

    public function __construct() {
    }

    public function DiscussionController_CommentInfo_Handler($Sender) {
        $UserName = $Sender->EventArguments['Author']->Name;
        $Text = C('Plugin.PM.Text', 'PM');
        $Needle = '{user}';
        $Pos = strpos($Text,$Needle);
        if($Pos){
            $Text = str_replace($Needle, $UserName, $Text);
        }
        echo Wrap(Anchor(T($Text), '/messages/add/' . $UserName), 'span', array('class' => 'PM'));
    }

    public function PluginController_PM_Create($Sender) {
      $Sender->Title('Private Message Link Plugin');
      $Sender->AddSideMenu('plugin/example');

      $Sender->Form = new Gdn_Form();

      $this->Dispatch($Sender, $Sender->RequestArgs);
   }

    public function Controller_Index($Sender) {
        // Prevent non-admins from accessing this page
        $Sender->Permission('Vanilla.Settings.Manage');

        $Sender->SetData('PluginDescription', $this->GetPluginKey('Description'));

        $Validation = new Gdn_Validation();
        $ConfigurationModel = new Gdn_ConfigurationModel($Validation);
        $ConfigurationModel->SetField(array(
            'Plugin.PM.Text' => 'Send {user} a Message',
        ));

        // Set the model on the form.
        $Sender->Form->SetModel($ConfigurationModel);

        // If seeing the form for the first time...
        if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
            // Apply the config settings to the form.
            $Text = C('Plugin.PM.Text', 'PM');
            $Sender->SetData('Text',$Text);
            $Sender->Form->SetData($ConfigurationModel->Data);
        } else {

            $ConfigurationModel->Validation->ApplyRule('Plugin.PM.Text', 'Required');
            $Saved = $Sender->Form->Save();
            if ($Saved) {
                $Sender->StatusMessage = T("Your changes have been saved.");
            }
        }
        $Sender->Render($this->GetView('index.php'));
    }

    public function Setup() {

        SaveToConfig('Plugin.PM.Text', 'PM');
    }

    public function OnDisable() {
        RemoveFromConfig('Plugin.PM.Text');
    }

}