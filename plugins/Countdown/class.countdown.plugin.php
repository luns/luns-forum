﻿<?php if (!defined('APPLICATION')) exit();

// Define the plugin:
$PluginInfo['Countdown'] = array(
	'Name' => 'Countdown',
	'Description' => 'Add a countdown to a specific time and date to a comment. Pick from different display types.',
	'Version' 	=>	 '1.1.0',
	'Author' 	=>	 "Matt Sephton",
	'AuthorEmail' => 'matt@gingerbeardman.com',
	'AuthorUrl' =>	 'http://www.vanillaforums.org/profile/matt',
	'License' => 'GPL v2',
	'SettingsUrl' => '/settings/countdown',
	'SettingsPermission' => 'Garden.Settings.Manage',
	'RequiredApplications' => array('Vanilla' => '>=2'),
);

class Countdown extends Gdn_Plugin {

	// settings
	public function SettingsController_Countdown_Create($Sender, $Args = array()) {
		$Sender->Permission('Garden.Settings.Manage');
		$Sender->SetData('Title', T('Countdown'));
		
		$tzlist[] = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
		$timezones = array_combine($tzlist[0], $tzlist[0]);

		$Cf = new ConfigurationModule($Sender);
		$Cf->Initialize(array(
			'Plugins.Countdown.Tag' => array('Description' => 'The following text will be replaced with the countdown widget', 'Control' => 'TextBox', 'Default' => '[COUNTDOWN]'),
			'Plugins.Countdown.Time' => array('Description' => 'Accepts most English textual date and time descriptions, see <a href="http://php.net/manual/en/function.strtotime.php">strtotime</a>', 'Control' => 'TextBox', 'Default' => '00:00:00 19 August 2012'),
			'Plugins.Countdown.Timezone' => array('Control' => 'DropDown', 'Items' => $timezones, 'Default' => 'UTC'),
			'Plugins.Countdown.Digits' => array('Control' => 'DropDown', 'Items' => array('digits' => 'Original', 'digits_transparent' => 'Original Transparent', 'digits_inverted' => 'Original Transparent Inverted', 'digits' => 'Original', 'digits2' => 'LED', 'digits2_blue' => 'LED Blue', 'digits2_green' => 'LED Green', 'digits2_orange' => 'LED Orange', 'digits2_purple' => 'LED Purple', 'digits2_red' => 'LED Red', 'digits2_yellow' => 'LED Yellow'))
		));

		$Sender->AddSideMenu('dashboard/settings/plugins');
		$Cf->RenderAll();
	}
	
	// replace in comment
	public function Base_AfterCommentFormat_Handler($Sender) {
		$Object = $Sender->EventArguments['Object'];
		$Object->FormatBody = $this->DoReplacement($Object->FormatBody);
		$Sender->EventArguments['Object'] = $Object;
	}

	// replacement logic
	public static function DoReplacement($Text) {
		// digits
		$CountdownDigits = (C('Plugins.Countdown.Digits')) ? C('Plugins.Countdown.Digits') : 'digits';

		// timezone
		$CountdownTimezone = (C('Plugins.Countdown.Timezone')) ? C('Plugins.Countdown.Timezone') : 'UTC';
		date_default_timezone_set($CountdownTimezone);

		// time
		$CountdownTime = (C('Plugins.Countdown.Time')) ? C('Plugins.Countdown.Time') : '00:00:00 19 August 2012';

		// get seconds
		$CountdownTime = strtotime($CountdownTime);
		$Now = time();

		// calc diff or set to zero if in the past
		if ($CountdownTime < $Now) {
			$Diff = 0;
		} else {
			$Diff = $CountdownTime-$Now;
		}

		// get components
		$elements = formatSeconds($Diff);
		$days = $elements['d'];
		$hours = $elements['h'];
		$minutes = $elements['m'];
		$seconds = $elements['s'];

		// tag
		if (!C('Plugins.Countdown.Tag')) {
			$CountdownTag = '[COUNTDOWN]';
		} else {
			$CountdownTag = C('Plugins.Countdown.Tag');
		}

		// get img
		$ImgSrc = Asset('/plugins/Countdown/design/'.$CountdownDigits.'.png');
		
		$CountdownJS = <<<JS
<div id="countdown" class="$CountdownDigits"></div>
<div class="countdown-desc">
	<div>Дней</div>
	<div>Часов</div>
	<div>Минут</div>
	<div>Секунд</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	$('#countdown').countdown({
		image: '$ImgSrc',
		startTime: "$days:$hours:$minutes:$seconds"
	});
});
</script>
JS;
		
		return str_replace($CountdownTag, $CountdownJS, $Text);
	}

	// hook
	public function Base_Render_Before($Sender) {
		$this->_CountdownSetup($Sender);
	}
	
	// setup
	private function _CountdownSetup($Sender) {
		$Sender->AddJsFile('jquery.countdown.min.js', 'plugins/Countdown');
		$Sender->AddCssFile('countdown.css', 'plugins/Countdown');
	}
	
	public function Setup() {
		return TRUE;
	}
	
}

function formatSeconds($secs) {
	$result['s'] = numberPad($secs%60);
	$result['m'] = numberPad(floor($secs/60)%60);
	$result['h'] = numberPad(floor($secs/60/60)%24);
	$result['d'] = numberPad(floor($secs/60/60/24));

	return $result;
}

function numberPad($number) {
	return sprintf("%02d", $number);
}

?>