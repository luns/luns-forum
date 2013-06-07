<?php if (!defined('APPLICATION')) exit();

$PluginInfo['ColorBox'] = array(
	'Name' => 'ColorBox',
	'Description' => 'A light-weight, customizable lightbox plugin for jQuery.',
	'Version' => '1.0.2',
	'Date' => '15 Feb 2011',
	'Author' => 'Jack Moore',
	'AuthorUrl' => 'http://colorpowered.com/colorbox/'
);

class ColorBoxPlugin extends Gdn_Plugin {
	
	static $Initialized = False;
	
	static public function Initialize(&$Controller, $CssFile = 4) {
		if (!self::$Initialized) {
			self::$Initialized = True;
			if (is_numeric($CssFile)) {
				$Controller->AddCssFile('plugins/ColorBox/design/colorbox'.$CssFile.'.css');
				$Controller->AddCssFile('custom-colorbox'.$CssFile.'.css');
			} else $Controller->AddCssFile($CssFile);
			
			//$Controller->AddJsFile('plugins/ColorBox/js/jquery.colorbox.js');
			$Controller->AddJsFile('plugins/ColorBox/js/jquery.colorbox-min.js');
			
			$Controller->AddDefinition('colorbox-current', T('colorbox-current', 'image {current} of {total}'));
			$Controller->AddDefinition('colorbox-next', T('colorbox-next', 'next'));
			$Controller->AddDefinition('colorbox-previous', T('colorbox-previous', 'previous'));
			$Controller->AddDefinition('colorbox-close', T('colorbox-close', 'close'));
		}
	}
	
	public function Setup() {
	}
}