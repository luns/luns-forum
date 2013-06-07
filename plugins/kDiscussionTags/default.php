<?
if(!defined('APPLICATION')) { exit(); }

$PluginInfo['kDiscussionTags'] = array(
   'Description' => 'Displays Post Tags in Discussion View and First Post of Discussion',
   'Version' => '1.1',
   'Author' => "Josh Grochowski",
   'AuthorEmail' => 'josh@kastang.com',
   'AuthorUrl' => 'http://kastang.com'
);


class kDiscussionTagsPlugin implements Gdn_IPlugin {

    protected $FirstComment = true;
    protected $VanillaDir = null;
    
    public function __construct() {
        $this->VanillaDir = str_replace('index.php', '', $_SERVER["PHP_SELF"]);
    }

   /*
   * Only render when the discussion controller is active. 
   */ 
   public function Base_Render_Before($Sender) {
        $validControllers =  array("discussionscontroller");
        if (!in_array($Sender->ControllerName, $validControllers)) return;
   }

   /*
    * Displays Tags on the first post of a discussion. 
    */
    public function DiscussionController_AfterCommentMeta_Handler(&$Sender) {

       //Only display on the first post in the Discussion view. 
       if($Sender->Discussion->Tags != '' && $this->FirstComment) {
           $this->FirstComment = false;
           $Tags = explode(' ',$Sender->Discussion->Tags);
           $String = "";
           foreach($Tags as $t) {
               $String .= "<a href='";
               $String .= (strstr($Sender->Form->Action, "index.php?p=") ? "index.php?p=" : $this->VanillaDir);
               $String .= "discussions/tagged/".rawurlencode($t)." ' class='label label-info'>$t</a> ";
               
               
           }
           echo '<span class="DiscussionTags">тэги: '.$String.'</span>';
       }

      return;
   }

    /*
     * Displays Tags on every post in the Discussions View.
     */
    public function DiscussionsController_DiscussionMetaLuns_Handler(&$Sender) {

       //Pulls discussion information
       $Discussion = $Sender->EventArguments['Discussion'];

       //Only display 'Tags' if post contains them. 
       if($Discussion->Tags != '') {

            $Tags = explode(' ',$Discussion->Tags);
            $String = '<span class="TagsAdd">тэги: ';

            foreach($Tags as $t) {
                $String .= "<a class='label label-info' href='";
                $String .= (strstr($Discussion->Url, "index.php?p=") ? "index.php?p=" : "");
                $String .= "discussions/tagged/".rawurlencode($t)."'>$t</a></span> ";
            }

            echo ' '.$String.'</span>';
       }

       return;
   }

   public function Setup() {}
} 

?>
