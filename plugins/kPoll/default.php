<?
if(!defined('APPLICATION')) { exit(); }

$PluginInfo['kPoll'] = array(
   'Description' => 'Sidebar Poll Plugin.',
   'Version' => '1.2',
   'Author' => "Josh Grochowski",
   'AuthorEmail' => 'josh@kastang.com',
   'AuthorUrl' => 'http://kastang.com',
   'RegisterPermissions' => array('Plugins.kPoll.Manage'),
   'SettingsPermission' => array('Plugins.kPoll.Manage')
);

class kPollPlugin extends Gdn_Plugin {

    public function Base_Render_Before($Sender) {

        /* The Poll will only render when the DiscussionController is active. */
        $validControllers =  array("discussionscontroller");
        if (!in_array($Sender->ControllerName, $validControllers)) return;


        /* Create the Poll Module */
        include_once(PATH_PLUGINS.DS.'kPoll'.DS.'class.kPoll.php');
        $kPoll = new kPoll($Sender);
        $Sender->AddModule($kPoll);

    }

    public function Base_GetAppSettingsMenuItems_Handler(&$Sender) {

        /* Administrate Poll View in Dashboard */
        $Menu = $Sender->EventArguments['SideMenu'];
        $Menu->AddLink('Forum', 'kPoll', 'plugin/kPoll', 'Garden.Settings.Manage');
    }   

    public function PluginController_kPoll_Create(&$Sender) {

        $Sender->AddCssFile('plugins/kPoll/design/kPoll.css');
        $Sender->Permission('Plugins.kPoll.Manage');

        $Session = Gdn::Session();
        $SQL = Gdn::SQL();

        /*
        if($SQL->Select('isActive')->From('kPollInfo')->Where('isActive', 1)->Get()->NumRows() > 0) {
            $pollId = $SQL->Select('pollId')->From('kPollInfo')->Where('isActive', 1)->Get()->FirstRow()->pollId;

            $Sender->kCurrentPoll = $SQL->Select('votes.userId', '', 'UserID')
                                        ->Select('kPollContent.pollContent', '', 'Answer')
                                        ->Select('kPollInfo.pollTitle', '', 'Title')
                                        ->Select('User.Name')
                                        ->From('kPollVotes votes')
                                        ->Join('kPollContent', 'kPollContent.pollContentId = votes.pollContentId', 'LEFT')
                                        ->Join('kPollInfo', 'kPollContent.pollId = votes.pollId', 'RIGHT')
                                        ->Join('User', 'User.UserID = votes.userId')
                                        ->Where('kPollInfo.isActive', 1)
                                        ->Where('votes.pollId', $pollId)
                                        ->OrderBy('kPollContent.pollContent', 'desc')
                                        ->Get()
                                        ->Result();
        }
         */

        $Sender->kArchivePoll = $SQL->Select('kPollInfo.pollTitle','','Title')
                                    ->Select('kPollVotes.userId', '', 'UserID')
                                    ->Select('kPollContent.pollContent', '', 'Answer')
                                    ->Select('User.Name')
                                    ->From('kPollVotes')
                                    ->Join('kPollInfo', 'kPollVotes.pollId = kPollInfo.pollId', 'LEFT')
                                    ->Join('kPollContent', 'kPollVotes.pollId = kPollContent.pollId AND kPollVotes.pollContentId = kPollContent.pollContentId', 'LEFT')
                                    ->Join('User', 'User.UserID = kPollVotes.userId')
                                    ->OrderBy('kPollInfo.pollId', 'desc')
                                    ->OrderBy('kPollContent.pollContent', 'asc')
                                    ->Get()
                                    ->Result();

        $Sender->Form = new Gdn_Form();
        $kPollInfo = new Gdn_Model('kPollInfo');
        $Sender->Form->SetModel($kPollInfo);

        if($Sender->Form->AuthenticatedPostBack() == TRUE) {

            /* Define the undefined (backend only) model fields */
            $Data = $Sender->Form->FormValues();

            if(isset($Data["Save"])) {

                /* Deactivates an active poll if one exists */
                $SQL->Update('kPollInfo')->Set('isActive', 0)->Put();

                $Sender->Form->SetFormValue('pollTitle', $Data["PollTitle"]);
                $Sender->Form->SetFormValue('createdById', $Session->UserID);
                $Sender->Form->SetFormValue('isActive', 1);
                $Sender->Form->SetFormValue('totalVotes', 0);
                $Sender->Form->SetFormValue('dateCreated', date('Y-m-d H:i:s'));

                /* Gets the latest PollId number and increases the value by 1. */
                $pollId = $SQL->Select('pollId')->From('kPollInfo')->OrderBy('pollId', 'desc')->Get();
                $pollId =($pollId->NumRows() == 0 ? 1 : $pollId->FirstRow()->pollId+=1);
                $Sender->Form->SetFormValue('pollId', $pollId);

                $counter = 1;
                $pollContent = $Data["PollAnswer"];
                foreach ($pollContent as $p) {
                    if($p != null) {
                        $kPollContent = array(
                            "id" => "",
                            "pollId" => $pollId,
                            "pollContent" => $p,
                            "pollContentId" => $counter);
                        $SQL->Insert('kPollContent', $kPollContent);
                    }
                    $counter++;
                }  

                if ($Sender->Form->Save() !== FALSE) {
                    $Sender->StatusMessage = T("New Poll Created");
                } 

            } else if(isset($Data["Clear_Active_Poll"])) {

                if($SQL->Select('*')->From('kPollInfo')->Where('isActive', 1)->Get()->NumRows() > 0) {
                    $SQL->Update('kPollInfo')->Set('isActive', 0)->Put();
                    $Sender->StatusMessage = T("Active Poll Cleared.");
                } else {
                    $Sender->StatusMessage = T("No Active Polls Exist.");
                }

            }
        }

        $Sender->AddSideMenu('plugins/kPoll');
        if(isset($Data["Create_New_Poll"])) {   //create poll view
            $Sender->Title('Create New Poll');
            $Sender->Render($this->GetView('kPollCreate.php'));
        } else {    //main screen

           
            $Sender->Title('Manage kPoll');
            $Sender->Render($this->GetView('kPoll.php'));
        }
    }

    public function Setup() {

        /* Creates the table the keeps track of the votes */
        Gdn::Structure()
            ->Table('kPollVotes')
            ->PrimaryKey('id')
            ->Column('userId', 'int(11)')
            ->Column('pollId', 'int(11)')
            ->Column('pollContentId', 'int(11)')
            ->Column('dateVoted', 'datetime')
            ->Set(FALSE, FALSE);

        /* Creates the table that contains the poll content */
        Gdn::Structure()
            ->Table('kPollContent')
            ->PrimaryKey('id')
            ->Column('pollId', 'int(11)')
            ->Column('pollContent', 'varchar(255)')
            ->Column('pollContentId', 'int(11)')
            ->Set(FALSE, FALSE);

        /* Creates the table the contains the name (Title) of the poll */
        Gdn::Structure()
            ->Table('kPollInfo')
            ->PrimaryKey('id')
            ->Column('pollId', 'int(11)')
            ->Column('pollTitle', 'varchar(255)')
            ->Column('totalVotes', 'int(11)')
            ->Column('createdById', 'int(11)')
            ->Column('isActive', 'int(11)')
            ->Column('dateCreated', 'datetime')
            ->Set(FALSE, FALSE);
    }
}

?>
