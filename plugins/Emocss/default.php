<?php if (!defined('APPLICATION')) exit();

// Define the plugin:
$PluginInfo['Emocss'] = array(
	'Name' => 'Emocss',
	'Description' => 'Replaces emoticons in forum comments with css images.',
	'Version' 	=>	 '1.0',
	'RequiredTheme' => FALSE, 
	'RequiredPlugins' => FALSE,
	'HasLocale' => FALSE,
	'Author' 	=>	 "422",
	'AuthorEmail' => 'steve@422.com.au',
	'AuthorUrl' =>	 'http://30.com.au',
	'License' => 'GPL v2',
	'RequiredApplications' => array('Vanilla' => '2.0.17')
);
/**
    * EMOcss
    *
    * This is a very simple plugin, which replaces image smileys with pure css simleys.
	* Thus it saves on server requests, each time an image is requested thats one trip back and 
	* forth to your server. Got a thread with 50 posts, and 3 smileys in each post ? then pure css
	* can make sense
**/
class EmocssPlugin implements Gdn_IPlugin {
	
	public function PostController_Render_Before($Sender) {
		$this->_Emocss($Sender);
	}
	
	public function DiscussionController_Render_Before($Sender) {
		$this->_Emocss($Sender);
	}
	
	private function _Emocss($Sender) {
		$Sender->AddJsFile('plugins/Emocss/emocss.js');
		$Sender->AddCssFile('plugins/Emocss/emocss.css');
	}
	
	public function Setup() { }
	
}