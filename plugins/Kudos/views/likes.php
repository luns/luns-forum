<div class="ItemLikes">
<?php  if($this->ItemLoves) { ?>
<p class="loves">
<?php $s = null;echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"); foreach($this->ItemLoves as $ID => $Name) $s .= '<a href="'.Url('profile/'.$ID.'/'.$Name).'">'.$Name.'</a>, ';
echo trim($s, ', ');
?>
</p>
<?php } ?>
<?php  if($this->ItemHates) { ?>
<p class="hates">
<?php $s = null;echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"); foreach($this->ItemHates as $ID => $Name) $s .= '<a href="'.Url('profile/'.$ID.'/'.$Name).'">'.$Name.'</a>, ';
echo trim($s, ', ');
?>
</p>
<?php } ?>
</div>