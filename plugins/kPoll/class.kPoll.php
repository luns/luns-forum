<? 
/**
 * @author Josh Grochowski (josh at kastang dot com)
 */

if(!defined('APPLICATION')) { exit(); }

class kPoll extends Gdn_Module {

    protected $SessionInfo;
    protected $userID;
    protected $SQL;
    protected $pollId;

    public function __construct(&$Sender = '') { 

        $this->SessionInfo = Gdn::Session();
        $this->userID = $this->SessionInfo->UserID;
        $this->SQL = Gdn::SQL();
        $this->pollId = $this->getPollId();

        /*
         * Check to see if a vote has just been cast. If it has, submit the vote.
         */
        if(isset($_POST["pollContentId"])) {
            $this->submitVote();
        }

        parent::__construct($Sender); 
    }

    /**
     * This function is called if a vote was cast. This function will insert an 
     * entry in the kPollVotes database and update the total number of votes cast 
     * for the given poll.
     *
     * @return nothing. 
     */
    private function submitVote() {

        $pollContentId = $_POST["pollContentId"];

        /* Check again to verify the user hasn't refreshed the page to recast a vote */
        if(!$this->hasVoted()) {

            /* Record the Vote If the User hasn't voted yet... */
            $kPollVotes = array(
                        "id" => '',
                        "userId" => $this->userID,
                        "pollId" => $this->pollId,
                        "pollContentId" => $pollContentId,
                        "dateVoted" => date('Y-m-d H:i:s'));
            $this->SQL->Insert('kPollVotes', $kPollVotes);

            /* Update the Total Votes count everytime a vote is submitted */
            $votes = $this->SQL->Select('totalVotes')->From('kPollInfo')->Where('pollId', $this->pollId)->Get()->FirstRow('', DATASET_TYPE_ARRAY)->totalVotes;
            $votes += 1;
            $this->SQL->Update('kPollInfo')->Set('totalVotes', $votes)->Where('pollId', $this->pollId)->Put();

        }
    }

    /**
     * This function will check to see if the logged in user has voted in the current active poll. 
     *
     * @Return True if the user has already participated in this poll, false otherwise. 
     */
    private function hasVoted() {

        $query = $this->SQL->Select('*')->From('kPollVotes')->Where('userId', $this->userID)->Where('pollId', $this->pollId)->Get();

        /** If no rows are returned, then the user hasn't voted yet. **/
        if($query->NumRows() == 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Returns the Active Poll Id. If no poll is active, return null.
     */
    private function getPollId() {

        $query = $this->SQL->Select('pollId')->From('kPollInfo')->Where('isActive', 1)->Get()->FirstRow('', DATASET_TYPE_ARRAY);

        if(!empty($query)) {
            return $query->pollId;
        } else {
            return null;
        }
    }

    /*
     * This function will return the total number of users who chose the given answer as their answer. 
     */
    private function getTotalVotes($pollContentId) {
        $query = $this->SQL->Select('*')->From('kPollVotes')->Where('pollId', $this->pollId)->Where('pollContentId', $pollContentId)->Get();
        return $query->NumRows();
    }

    /**
     * Tells Vanilla to display the poll on the Sidebar. 
     */
    public function AssetTarget() { 
        return 'Panel'; 
    }


    /**
     * Generates the content that will be displayed in the sidebar. 
     */
    public function ToString() {

        $pollContent = $this->SQL->Select('*')->From('kPollContent')->Where('pollId', $this->pollId)->Get();
        $pollInfo = $this->SQL->Select('pollTitle, totalVotes')->From('kPollInfo')->Where('pollId', $this->pollId)->Get()->FirstRow();

        $String = '';
        ob_start();

        if($this->pollId != null) {         //if there is an active poll...

            echo <<<EOT
            <div id="kPoll" class="Box">
            <h4>Голосования</h4>
EOT;

            if($this->userID > 0) {         //if there is a user logged in...

                echo <<<EOT
                <ul class="PanelActivity">
                <strong><li>$pollInfo->pollTitle</li></strong>
                <form id="poll" action="./" method="POST">
EOT;

                if(!$this->hasVoted()) {    //if the user has NOT voted yet...


                    foreach($pollContent as $c) {
                        echo  "<li><input type='radio' name='pollContentId' value='$c->pollContentId' />$c->pollContent</li> ";
                    }

                    echo <<<EOT
                    <br />
                    <li>Всего голосов: $pollInfo->totalVotes</li >
                    <input type="submit" value="Оставить голос" />
EOT;


                } else {                    //if the user has already voted...

                    //Displays only the total number of votes for each answer.
                    foreach($pollContent as $c) {
                        echo "<li>$c->pollContent 
                                <span style='float: right;'>(".round((($this->getTotalVotes($c->pollContentId))/$pollInfo->totalVotes) * 100, 1)."%)</span>
                              </li>";
                    }

                    echo <<<EOT
                    <br />
                    <li>Всего голосов: $pollInfo->totalVotes</li >
EOT;
                }
                echo "</ul>";
            } else {                        //if the user is not logged in...
                echo "Вы должны войти для голосования";
            }
        }

echo <<<EOT
            </p>
            </form>
        </div>
EOT;

        $String = ob_get_contents();
        @ob_end_clean();
        return $String;

    }
}
?>
