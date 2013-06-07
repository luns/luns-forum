<?php if (!defined('APPLICATION')) exit();
if(empty($this->AboutMe)) { // if the sender's row in the 'about me' table is empty, tell the user ?>
	<div class="Empty">
		<?php echo "This profile hasn't been set up yet."; ?>
	</div>
<?php
} else {	 // else display the page
	// Define variables for data
	$RealName = $this->AboutMe->RealName;
	/*
	$NickName = $this->AboutMe->OtName;
	$Quote = $this->AboutMe->Quote;
	$BD = $this->AboutMe->BD;
	$HideBD = $this->AboutMe->HideBD;
	$HideBY = $this->AboutMe->HideBY;
	$RelationshipStatus = $this->AboutMe->RelStat;
	$Quote = $this->AboutMe->Quote;
	$Location = $this->AboutMe->Loc;
	$Employer = $this->AboutMe->Emp;
	$JobTitle = $this->AboutMe->JobTit;
	$HighSchool = $this->AboutMe->HS;
	$College = $this->AboutMe->Col;
	$Interests = $this->AboutMe->Inter;
	$Music =$this->AboutMe->Mus;
	$Games = $this->AboutMe->Gam;
	$Movies = $this->AboutMe->Mov;
	$TV = $this->AboutMe->TV;
	$Books = $this->AboutMe->Bks;
	$Biography = $this->AboutMe->Bio;
	$UserName = $this->User->Name;
	$Photo = $this->User->Photo;
*/


?>
<div class="aboutme">
<table id="nameinfo">
	<tr>
		<td class="name"><?php echo $this->AboutMe->RealName ?></td>
		<?php if(!empty($this->AboutMe->OtName )){ // if the column OtName isn't empty, display: ?>
		<td class="nick name">A.K.A. <?php echo $this->AboutMe->OtName; ?></td>
		<?php } ?>
	</tr>
</table>

<?php
$this->FireEvent('AboutPageBoxAfter');
?>
</div>
<?php }

