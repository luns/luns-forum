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

//Override vanilla's default encoding UTF-8, with UTF-8 e.g. eblah² doesnt work (the ²)
header('Content-Type: text/html; charset=ISO-8859-15');

class Van2ShoutData extends Gdn_Module {

	public function __connstruct(&$Sender = '') {
		parent::__construct($Sender);
	}

	public function ToString() {

		ob_start();
		$Session = GDN::Session();

		if(isset($_GET["postcount"]) && empty($_GET["newpost"]) && empty($_GET["del"])) {
			if(!$Session->CheckPermission('Plugins.Van2Shout.View')) {
				return;
			}

			///Display posts - format: User{,}post1[,]User{,}post2[,]... (other characters might be used while shoutboxing

			//Get data from mysql DB
			$dblink = @mysql_connect(C('Database.Host'),C('Database.User'),C('Database.Password'), FALSE, 128);
			if(!$dblink){ return; }

			@mysql_select_db(C('Database.Name'));
			$query = @mysql_query("SELECT * FROM GDN_Shoutbox WHERE PM = '' OR PM = '".mysql_real_escape_string($Session->User->Name)."' OR UserName = '".mysql_real_escape_string($Session->User->Name)."' ORDER BY ID DESC LIMIT ".intval(mysql_real_escape_string($_GET["postcount"])).";", $dblink);
			if(!$query){ echo "query failed"; return; }
			$str = array();
			//Display data
			while ($msg = mysql_fetch_assoc($query)) {
				$colourquery = @mysql_query("SELECT * FROM GDN_User WHERE Name = '".mysql_real_escape_string($msg["UserName"])."';", $dblink);
				if(!$colourquery){ echo mysql_error(); return; }
				$colour = "";
				while ($uid = mysql_fetch_assoc($colourquery)) {
					if($uid["UserID"] != "") {
						$metadata = Gdn::UserMetaModel()->GetUserMeta($uid["UserID"], "Plugin.Van2Shout.Colour", "");
						$colour = $metadata['Plugin.Van2Shout.Colour'];
						if(!$colour) { $colour = "Default"; }
						if($colour == "Default") { $colour = ""; }
					}
				}

				if($msg["PM"] == "") {
					$delimeter = "[!content!]";
					$array[] = $colour."[!colour!]".$msg["UserName"].$delimeter.$msg["Content"]."[!msgid!]".$msg["ID"]."[!msgtime!]".$msg["Timestamp"]."\n";
				} elseif($msg["PM"] != "" && $msg["UserName"] == $Session->User->Name && $msg["PM"] != $Session->User->Name) {
					$delimeter = "[!pmtocontent!]"; //User can see this PM because he sent it.
					$array[] = $colour."[!colour!]".$msg["PM"].$delimeter.$msg["Content"]."[!msgid!]".$msg["ID"]."[!msgtime!]".$msg["Timestamp"]."\n"; //Display in the following format: receiver[!pmtocontent!]content instead of sender[delimeter]content
				} else {
					$delimeter = "[!pmcontent!]";
					$array[] = $colour."[!colour!]".$msg["UserName"].$delimeter.$msg["Content"]."[!msgid!]".$msg["ID"]."[!msgtime!]".$msg["Timestamp"]."\n";
				}
			}

			$array = array_reverse($array);

			foreach($array as $line) {
				echo $line;
			}

			mysql_close($dblink);
		}

		if(!empty($_GET["newpost"]) && empty($_GET["postcount"]) && empty($_GET["del"])) {
			if(!$Session->CheckPermission('Plugins.Van2Shout.Post')) {
				return;
			}

			//On some systems, the $_GET variables are mysql_real_escaped for some reason... Let's undo this!
			$searchstring = array("\\\\", "\\n", "\\r", "\\Z", "\\'", '\\"');
  			$replacestring = array("\\", "\n", "\r", "\x1a", "'", '"');
			$string = str_replace($searchstring, $replacestring, $_GET["newpost"]);

			//Check if message is to long
			if(strlen($_GET["newpost"]) > 148) { return; }

			if(stristr($_GET["newpost"], "[!pmcontent!]") || stristr($_GET["newpost"], "[!content!]") || stristr($_GET["newpost"], "[!msgid!]")) {
				return;
			}

			//Insert data into mysql DB
			$dblink = @mysql_connect(C('Database.Host'),C('Database.User'),C('Database.Password'), FALSE, 128);
			if(!$dblink){ return; }

			@mysql_select_db(C('Database.Name'));

			//Filter XSS and MySQL injections
			$string = htmlspecialchars($string);
			//Detect links starting with http:// or ftp://
			$string = preg_replace( '/(http|ftp)+(s)?:(\/\/)((\w|\.)+)(\/)?(\S+)?/i', '<a href="\0" target="blank">\0</a>', $string);
			$string = mysql_real_escape_string($string);

			//Is the shoutbox open to everyone?
			if($Session->User->Name == "") {
				$username = "Guest";
			} else {
				$username = $Session->User->Name;
			}

			$pm = "";
			//Is it a PM?
			if(substr($string, 0, 3) == "/w "){
				$cut = explode("/w ", $string);
				$cut = explode(" ", $cut[1]);

				$pm = $cut[0];
				//Okay, we got the username, now we need to reassemble message
				$string = "";
				$i = 0;
				foreach($cut as $data){
					if($i != 0) {
						$string .= $data." ";
					}
					$i++;
				}
			}

			@mysql_query("INSERT into GDN_Shoutbox(UserName, PM, Content, Timestamp) values ('".mysql_real_escape_string($username)."', '".$pm."', '".$string."', UNIX_TIMESTAMP(NOW()));");
			@mysql_close($dblink);
		}

		if(!empty($_GET["del"]) && empty($_GET["newpost"]) && empty($_GET["postcount"])) {
			if(!$Session->CheckPermission('Plugins.Van2Shout.Delete')) {
				return;
			}
			//Delete data from mysql DB
			$dblink = @mysql_connect(C('Database.Host'),C('Database.User'),C('Database.Password'), FALSE, 128);
			if(!$dblink){ return; }
			@mysql_select_db(C('Database.Name'));

			$id = mysql_real_escape_string($_GET["del"]);
			if(!is_numeric($id)) {
				return;
			}

			@mysql_query("DELETE FROM GDN_Shoutbox WHERE ID=".$id.";");

			mysql_close($dblink);
		}

		$String = ob_get_contents();
		@ob_end_clean();

		return $String;
	}
}
