<?php if (!defined('APPLICATION')) exit(); ?>
<div>
   <?php
   // Make sure to force this form to post to the correct place in case the view is
   // rendered within another view (ie. /dashboard/entry/index/):
   echo $this->Form->Open(array('Action' => $this->Data('FormUrl', Url('/entry/signin')), 'id' => 'Form_User_SignIn', 'class' =>'form-horizontal'));
   echo $this->Form->Errors();
   ?>
   <ul>
      <li>
          
          


         <?php
            echo $this->Form->Label('Email/Username', 'Email');
            echo ('<div class="input-prepend">');
            echo ('<span class="add-on"><i class="icon-user"></i> </span>');
            echo $this->Form->TextBox('Email',array('class'=>'span2'));
            echo ('</div>');
         ?>
      </li>
      <li>
         <?php
            echo $this->Form->Label('Password', 'Password');
            echo ('<div class="input-prepend">');
            echo ('<span class="add-on"><i class="icon-lock"></i> </span>');
            echo $this->Form->Input('Password', 'password', array('class' => 'span2'));
            echo ('</div>');
            echo Anchor(T('Forgot?'), '/entry/passwordrequest', 'ForgotPassword');
         ?>
      </li><br>
      <li>
         <?php
            echo $this->Form->Button('Sign In',array('class' => 'btn btn-primary'));
            echo $this->Form->CheckBox('RememberMe', T('Keep me signed in'), array('value' => '1', 'id' => 'SignInRememberMe'));
         ?>
      </li>
      <?php if (strcasecmp(C('Garden.Registration.Method'), 'Connect') != 0): ?>
      <br><br>
      <li>
         <?php
            $Target = $this->Target();
            if ($Target != '')
               $Target = '?Target='.urlencode($Target);

            printf(T("Don't have an account? %s"), Anchor(T('Create One.'), '/entry/register'.$Target));
         ?>
      </li>
      <?php endif; ?>
   </ul>
   <?php
   echo $this->Form->Close();
   echo $this->Form->Open(array('Action' => Url('/entry/passwordrequest'), 'id' => 'Form_User_Password', 'style' => 'display: none;'));
   ?>
   <ul>
      <li>
         <?php
            echo $this->Form->Label('Enter your Email address or username', 'Email');
            echo $this->Form->TextBox('Email');
         ?>
      </li>
      <li class="Buttons">
         <?php
            echo $this->Form->Button('Request a new password',array('class' => 'btn btn-primary btn-block'));
            echo Wrap(Anchor(T('I remember now!'), '/entry/signin', 'ForgotPassword'), 'div');
         ?>
      </li>
   </ul>
   <?php echo $this->Form->Close(); ?>
</div>