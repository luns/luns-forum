<?php if(!defined('APPLICATION')) exit();

class Van2ShoutDiscussionsModule extends Gdn_Module {
	public function __construct(&$Sender = '') {
		parent::__construct($Sender);
	}

	public function AssetTarget() {
		return VAN2SHOUT_ASSETTARGET;
	}

	public function ToString() {
		$Session = Gdn::Session();
		if(!$Session->CheckPermission('Plugins.Van2Shout.View')) {
			return "";
		}

		$String = '';

		ob_start();
		require(PATH_PLUGINS.DS.'Van2Shout'.DS.'views'.DS.'discussionscontroller.php');
		$String = ob_get_contents();
		@ob_end_clean();

		return $String;
	}
}
