<?php if (!defined('APPLICATION')) exit(); ?>
<h1><?= T('Редактировать голосование'); ?></h1>
    <div class="poll-field-input"> 
    <?= $this->Form->Open();?>
    <?= $this->Form->Label(T('Вопрос голосования'), 'Pollname');?>
    <div class="cleaner"></div>
    <?= $this->Form->TextBox('title',array('value'=>$this->Poll->title));?>
    <div class="cleaner"></div>
    <br />
    <h2>Ответы</h2>
    <?$i=0;?>
    <? foreach ($this->Answers->Result() as $answer):?>
    <?$i++?>
    <?= $this->Form->Label(T('Ответ #').$i);?>
    <div class="cleaner"></div>
    <?= $this->Form->TextBox('edit_answer_'.$answer->id,array('value'=>stripslashes($answer->title)));?> <?= Anchor(T('Удалить'),'discussion/editpoll/'.$this->Poll->id.'/deleteanswer/'.$answer->id)?>
    <div class="cleaner"></div>
    
        
    
    <?endforeach?>
    <?
    $res=$this->Answers->Result();
     if (empty($res)):?><?= T('Нет ответов')?><?endif?>
    
    <div class="cleaner"></div>
    <br />
    
    <?= $this->Form->Label(T('Добавить ответ'));?>
    <div class="cleaner"></div>
    <?= $this->Form->TextBox('new_answer');?>
    <div class="cleaner"></div>
    <br />
    <?= $this->Form->Button(T('Редактировать'));?>
    <?= $this->Form->Close();?>
    </div>
    <br />
    <b><? echo (Anchor(T("Вернуться в тему"),"discussion/".$this->Poll->discussion_id))?></b>