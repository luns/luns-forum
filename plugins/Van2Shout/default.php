<?php if(!defined('APPLICATION')) exit();
//Copyright (c) 2010-2011 by Caerostris <caerostris@gmail.com>
//	 This file is part of Van2Shout.
//
//	 Van2Shout is free software: you can redistribute it and/or modify
//	 it under the terms of the GNU General Public License as published by
//	 the Free Software Foundation, either version 3 of the License, or
//	 (at your option) any later version.
//
//	 Van2Shout is distributed in the hope that it will be useful,
//	 but WITHOUT ANY WARRANTY; without even the implied warranty of
//	 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//	 GNU General Public License for more details.
//
//	 You should have received a copy of the GNU General Public License
//	 along with Van2Shout.  If not, see <http://www.gnu.org/licenses/>.

define('VAN2SHOUT_VERSION', '1.0');
define('VAN2SHOUT_ASSETTARGET', 'Panel');

// Define the plugin:
$PluginInfo['Van2Shout'] = array(
	'Name' => 'Van2Shout',
	'Description' => 'A simple shoutbox for vanilla2 with support for different groups and private messages',
	'Version' => '1.0',
	'Author' => "Caerostris",
	'AuthorEmail' => 'caerostris@gmail.com',
	'AuthorUrl' => 'http://caerostris.com',
		'SettingsPermission' => array('Plugins.Van2Shout.View', 'Plugins.Van2Shout.Post', 'Plugins.Van2Shout.Delete', 'Plugins.Van2Shout.Colour'),
		'RegisterPermissions' => array('Plugins.Van2Shout.View', 'Plugins.Van2Shout.Post', 'Plugins.Van2Shout.Delete', 'Plugins.Van2Shout.Colour'),
);

class Van2ShoutPlugin extends Gdn_Plugin {
	public function PluginController_Van2ShoutData_Create(&$Sender) {
		//Check if user is allowed to view
		$Session = GDN::Session();
			if(!$Session->CheckPermission('Plugins.Van2Shout.View')) {
			return;
		}


		//Displays the posts of the shoutbox

		include_once(dirname(__FILE__).DS.'controllers'.DS.'class.van2shoutdata.php');
		$Van2ShoutData = new Van2ShoutData($Sender);
		echo $Van2ShoutData->ToString();

	}

//	public function DiscussionsController_Render_Before(&$Sender) {
	public function Base_Render_Before(&$Sender) {
		$Session = GDN::Session();
		if($Session->CheckPermission('Plugins.Van2Shout.View')) {
			//Display the delete icon?
			if($Session->CheckPermission('Plugins.Van2Shout.Delete')) {
				$Sender->AddDefinition('Van2ShoutDelete', 'true');
			}

			include_once(PATH_PLUGINS.DS.'Van2Shout'.DS.'modules'.DS.'class.van2shoutdiscussionsmodule.php');
			$Van2ShoutDiscussionsModule = new Van2ShoutDiscussionsModule($Sender);
			$Sender->AddModule($Van2ShoutDiscussionsModule);
		}
	}

	public function ProfileController_AfterAddSideMenu_Handler(&$Sender) {
		$Session = GDN::Session();
		$SideMenu = $Sender->EventArguments['SideMenu'];
		$ViewingUserID = $Session->UserID;

		if($Sender->User->UserID == $ViewingUserID && $Session->CheckPermission('Plugins.Van2Shout.Colour')) {
			$SideMenu->AddLink('Options', T('Van2Shout Settings'), '/profile/van2shout', FALSE, array('class' => 'Popup'));
		}
	}

	public function ProfileController_Van2Shout_Create(&$Sender) {

		$Session = GDN::Session();

		if(!$Session->CheckPermission('Plugins.Van2Shout.Colour')) { return; }

		$UserID = $Session->IsValid() ? $Session->UserID: 0;

		//Get the data
		$UserMetaData = $this->GetUserMeta($UserID, '%');
		$ConfigArray = array('Plugin.Van2Shout.UserColour' => NULL);

		if($Sender->Form->AuthenticatedPostBack() === FALSE) {
			$ConfigArray = array_merge($ConfigArray, $UserMetaData);
			$Sender->Form->SetData($ConfigArray);
		} else {
			$Values = $Sender->Form->FormValues();
			$FrmValues = array_intersect_key($Values, $ConfigArray);

			require(dirname(__FILE__)."/colours.txt");

			foreach($FrmValues as $MetaKey) {
				$this->SetUserMeta($UserID, "Colour", $MetaKey);
				$Sender->StatusMessage = T("Your changes have been saved.");
			}
		}

		$Sender->Render($this->GetView('settings.php'));
	}

	public function Setup() {
		//I'd love to use GDN::Structure but this class does not support Auto_Increment
		$dblink = @mysql_connect(C('Database.Host'),C('Database.User'),C('Database.Password'), FALSE, 128);
		if(!$dblink)
			return false;

		@mysql_select_db(C('Database.Name'));
		$query = @mysql_query("CREATE TABLE IF NOT EXISTS `GDN_Shoutbox` ( `ID` int(30) NOT NULL AUTO_INCREMENT COMMENT 'No questions about this? :P', `UserName` varchar(50) NOT NULL COMMENT 'VarChar(50) is vanillas max usernamelength', `PM` varchar(50) NOT NULL COMMENT 'If the message is a PM, the username it is sent to will go here, so also VarChar(50)', `Content` varchar(149) NOT NULL COMMENT 'Maxlength should be 148; 149 just to be sure...', `Timestamp` int(11) NOT NULL, PRIMARY KEY (`ID`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Stores the shoutbox posts from Van2Shout' AUTO_INCREMENT=109;"); 
		if(!$query){ return false; }
		mysql_close($query);
	}
}
