<?php if (!defined('APPLICATION')) exit();

 
$PluginInfo['StatisticsFooter'] = array(
   'Name' => 'Statistics Footer',
   'Description' => 'Adds Total View, Total User, and Total Discussion Topic Counts to Footer on Discussion Page',
   'Version' => '1.1',
   'Author' => "Peregrine",
);


class StatisticsFooterPlugin extends Gdn_Plugin {
 
 
  public function DiscussionsController_Render_Before($Sender) {
        $Sender->AddCssFile('/plugins/StatisticsFooter/design/sfooter.css');
    }
 
  
  public function DiscussionsController_AfterRenderAsset_Handler($Sender) {
 
   $AssetName = GetValueR('EventArguments.AssetName', $Sender);
    
    if ($AssetName != "Content") return;
   
    $SFVcount = $this->GetViewCount();
    $SFDcount = $this->GetDiscussionCount();
    $SFUcount =  $this->GetUserCount();
    $SFCcount = $this->GetCommentCount();
   
    $SFPcount = $SFDcount + $SFCcount;

  
   echo Wrap(Wrap(T('Просмотров')) . Gdn_Format::BigNumber($SFVcount), 'div', array('class' => 'SFBox SFVCBox'));
    
   echo Wrap(Wrap(T('Пользователей')) . Gdn_Format::BigNumber($SFUcount), 'div', array('class' => 'SFBox SFUBox'));
  
   echo Wrap(Wrap(T('Тем')) . Gdn_Format::BigNumber($SFDcount), 'div', array('class' => 'SFBox SFTBox'));
  
   echo Wrap(Wrap(T('Сообщений')) . Gdn_Format::BigNumber($SFPcount), 'div', array('class' => 'SFBox SFPBox'));
    }  

 public function GetUserCount(){
     $UModel = new Gdn_Model('User');
    return $UModel->SQL
        ->GetCount('User');
 }

 public function GetViewCount(){
      $DModel = new Gdn_Model('Discussion');
        $Sender->UserData = $DModel->SQL
        ->Select('Sum(CountViews) AS SumViewCount')
        ->From('Discussion')
        ->Get();
       $Result =  $Sender->UserData->Result();
        return current((array)$Result[0]);
       
 }
    
 public function GetDiscussionCount(){
     $VModel = new Gdn_Model('Discussion');
    return $VModel->SQL
        ->GetCount('Discussion');
 }
   
public function GetCommentCount(){
   $CModel = new Gdn_Model('Comment');
    return $CModel->SQL
        ->GetCount('Comment');
        }
  
  
   public function CategoriesController_Render_Before($Sender) {
   
        $this->DiscussionsController_Render_Before($Sender);
    }
   
   
    public function CategoriesController_AfterRenderAsset_Handler($Sender) {
       $this->DiscussionsController_AfterRenderAsset_Handler($Sender);
    }
 
 
  public function Setup() {
        
    }
    
    }
