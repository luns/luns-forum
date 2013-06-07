<?php if (!defined("APPLICATION")) exit();

$PluginInfo["Timeago"] = array(
   "Name" => "Timeago",
   "Description" => 'Timeago adds automatically updating fuzzy timestamps (e.g. "4 minutes ago") throughout Vanilla.',
   "Version" => "1.0",
   "Author" => "Kasper K. Isager",
   "AuthorEmail" => "kasperisager@gmail.com",
   "AuthorUrl" => "http://github.com/kasperisager"
);

class Timeago extends Gdn_Plugin {
	
	public function Base_Render_Before($Sender) {
	
		$Sender->AddJsFile("jquery.timeago.js", "plugins/Timeago");
		
		$Sender->Head->AddString("
			<script type='text/javascript'>
				jQuery(function() {
					$('time').timeago();
					$('time').livequery(function() {
						$(this).timeago();
					});
				});
			</script>
		");
		
	}
	
};