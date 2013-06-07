<?php if (!defined('APPLICATION')) exit();

$PluginInfo['FaviconLinks'] = array(
	'Name' => 'FaviconLinks',
	'Description' => 'Adds a favicon to urls in comments.',
	'Version' => '1.0.1',
	'Date' => '12 Apr 2011',
	'Author' => 'Frostbite',
	'AuthorUrl' => 'http://www.malevolence2007.com',
	'RequiredApplications' => False,
	'RequiredTheme' => False, 
	'RequiredPlugins' => False,
	'RegisterPermissions' => False,
	'SettingsPermission' => False,
	'License' => 'X.Net License'
);

class FaviconLinksPlugin implements Gdn_IPlugin {
	
	public function DiscussionController_Render_Before($Sender) {
		if ($Sender->DeliveryType() == DELIVERY_TYPE_ALL && $Sender->SyndicationMethod == SYNDICATION_NONE) {
			$Sender->AddJsFile('plugins/FaviconLinks/faviconlinks.functions.js');
		}
	}
	
	public function Setup() {
	}
}