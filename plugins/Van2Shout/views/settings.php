<?php if (!defined('APPLICATION')) exit(); ?>
<h2><?php echo T('Van2Shout'); ?></h2>
<?php
echo $this->Form->Open();
echo $this->Form->Errors();
?>
<ul>
	<li>
		<?php
			$Session = GDN::Session();

			echo $this->Form->Label('Settings');
			require(dirname(__FILE__)."/../colours.txt");

			//Get data from mysql DB
			$dblink = @mysql_connect(C('Database.Host'),C('Database.User'),C('Database.Password'), FALSE, 128);
			if(!$dblink){ return; }

			@mysql_select_db(C('Database.Name'));

			//Get the groups
			$query = @mysql_query("SELECT * FROM GDN_Role");
			if(!$query) { echo "query failed"; return; }

			$mysqlgroupnames = array();
			$mysqlgroupids = array();

			while($grp = mysql_fetch_assoc($query)) {
				array_push($mysqlgroupnames, $grp["Name"]);
				array_push($mysqlgroupids, $grp["RoleID"]);
			}

			$query = @mysql_query("SELECT * FROM GDN_UserRole Where UserID = '" . $Session->UserID . "';");
			if(!$query){ echo "query failed"; return; }

			$groupNamesArray = array("Default");

			while ($userentry = mysql_fetch_assoc($query)) {
				$searchgrpname = $mysqlgroupnames[array_search($userentry["RoleID"], $mysqlgroupids)];
				if(array_search($searchgrpname, $groupNames) !== FALSE) {
					array_push($groupNamesArray, $searchgrpname);
				}
			}

			$data = array("Default" => "Default");
			foreach($groupNamesArray as $grp) {
				$find = array_search($grp, $groupNames);
				if($find !== FALSE) {
					$data[$colourCodes[$find]] = $grp;
				}
			}

			echo "Select the group that should be used in the Shoutbox:<br />";
			echo $this->Form->DropDown('Plugin.Van2Shout.UserColour', $data);
		?>
	</li>
</ul>

<?php echo $this->Form->Close('Save');
