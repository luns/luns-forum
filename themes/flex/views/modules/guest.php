<?php if (!defined('APPLICATION')) exit(); ?>
<div class="Box Guest">
   <h4><?php echo T('Howdy, Stranger!'); ?></h4>
   <p><?php echo T($this->MessageCode, $this->MessageDefault); ?></p>
   <?php $this->FireEvent('BeforeSignInButton'); ?>
   
   <?php
   if (strcasecmp(C('Garden.Registration.Method'), 'Connect') != 0) {
      //echo '<div class="button-bar btn-block">';

      echo Anchor(T('Sign In'), SignInUrl($this->_Sender->SelfUrl), 'button medium green btn-block'.(SignInPopup() ? ' SignInPopup' : ''));
      $Url = RegisterUrl($this->_Sender->SelfUrl);
      if(!empty($Url))
         echo ' '.Anchor(T('Регистрация'), $Url, 'button medium green btn-block');

      //echo '</div>';
   }
   ?>
   <?php $this->FireEvent('AfterSignInButton'); ?>
</div>