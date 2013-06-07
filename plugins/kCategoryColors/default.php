<?
if(!defined('APPLICATION')) { exit(); }

$PluginInfo['kCategoryColors'] = array(
   'Description' => 'Will change the background of the discussion topic based on the Category',
   'Version' => '1.0',
   'Author' => "Josh Grochowski",
   'AuthorEmail' => 'josh@kastang.com',
   'AuthorUrl' => 'http://kastang.com',
);

class kCategoryColorsPluin extends Gdn_Plugin {

    public function Base_Render_Before($Sender) {

        /* The Category Colors will only render when the DiscussionController is active. */
        $validControllers =  array("discussionscontroller");
        if (!in_array($Sender->ControllerName, $validControllers)) return;

        include_once(PATH_PLUGINS.DS.'kCategoryColors'.DS.'class.kCategoryColors.php');
        $kCategoryColors = new kCategoryColors($Sender);
        $Sender->Head->AddString($kCategoryColors);
    }

    public function Base_GetAppSettingsMenuItems_Handler(&$Sender) {
        $Menu = $Sender->EventArguments['SideMenu'];
        $Menu->AddLink('Forum', 'kCategoryColors', 'plugin/kCategoryColors', 'Garden.Settings.Manage');
    }

    public function PluginController_kCategoryColors_Create(&$Sender) {

        $SQL = Gdn::SQL();
        $Sender->Form = new Gdn_Form();
        $kCategoryColors = new Gdn_Model('kCategoryColors');
        $Sender->Form->SetModel($kCategoryColors);

        if($Sender->Form->AuthenticatedPostBack() == TRUE) {

            $Data = $Sender->Form->FormValues();

            for($i=0;$i<count($Data["CategoryColor"]);$i++) {

                $CategoryColor =  $Data["CategoryColor"][$i];
                $CategoryID = $Data["CategoryID"][$i];

                $t = array (
                    "CategoryId" => $CategoryID,
                    "CategoryColor" => $CategoryColor
                );

                if($SQL->Select('*')->From('kCategoryColors')->Where('CategoryId', $CategoryID)->Get()->NumRows() == 0) {
                    $SQL->Insert('kCategoryColors', $t);
                } else {
                    $SQL->Update('kCategoryColors')->Set('CategoryColor', $CategoryColor)->Where('CategoryId', $CategoryID)->Put();
                }

            }
        }

        $Sender->CategoryInfo = $SQL->Select('Category.CategoryID, Category.Name')
                                    ->Select('kCategoryColors.CategoryColor')
                                    ->From('Category')
                                    ->Join('kCategoryColors', 'kCategoryColors.CategoryId = Category.CategoryID')
                                    ->Where('Category.CategoryId >', '-1')
                                    ->Get()
                                    ->Result();

        if($Sender->CategoryInfo == null) {
            $Sender->CategoryInfo = $SQL->Select('Category.CategoryID, Category.Name')
                                        //->Select('kCategoryColors.CategoryColor')
                                        ->From('Category')
                                        //->Join('kCategoryColors', 'kCategoryColors.CategoryId = Category.CategoryID')
                                        ->Where('Category.CategoryId >', '-1')
                                        ->Get()
                                        ->Result();

        }

        $Sender->AddSideMenu('plugins/kCategoryColors');
        $Sender->Title('kCategoryColors Management');
        $Sender->Render($this->GetView('kCategoryColors.php'));



    }

    public function Setup() {

        Gdn::Structure()
            ->Table('kCategoryColors')
            ->PrimaryKey('id')
            ->Column('CategoryId', 'int(11)')
            ->Column('CategoryColor', 'varchar(16)')
            ->Set('FALSE', 'FALSE');

    }

}
?>
