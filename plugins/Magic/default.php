<?php if(!defined('APPLICATION')) die();

$PluginInfo['Magic'] = array(
    'Name' => 'Magic',
    'Description' => 'Vyhled&#225;v&#225; v koment&#225;&#345;&#237;ch diskuse odkazy, smajl&#237;ky, atp. a zobraz&#237; je graficky.',
    'Version' => '1.0',
    'Author' => "Michal Toman",
    'AuthorEmail' => "toman.michal@gmail.com",
    'RequiredApplications' => array('Vanilla' => '>=2.0'),
);

class Magic implements Gdn_IPlugin {

    private $Comment = '';
    private $RegularImg = '/((http|ftp)\:\/\/)?([A-Za-z0-9\-\_\.]+)([\/\-\_A-Za-z0-9]+)?(\.(jpg|JPG|jpeg|JPEG|png|PNG|gif|GIF))/';
    private $RegularYoutube = '/((http|ftp)\:\/\/)?([w]{3}\.)?(youtube\.)([a-z]{2,4})(\/watch\?v=)([a-zA-Z0-9_-]+)(\&feature=)?([a-zA-Z0-9_-]+)?/';

    /**
     * IsValueAttribute
     * @param String $Comment
     * @param String $Url
     * @return Boolean
     */
    protected function IsValueAttribute($Comment, $Url) {
        if (ereg("=(\"|\')".$Url."(\"|\')", $Comment)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * ViewImages
     * @param Array $Matches
     * @return String
     */
    protected function ViewImages($Matches) {
        if($this->IsValueAttribute($this->Comment, $Matches[0])) {
            return $Matches[0];
        } else {
            if(strlen($Matches[1]) == 0) {
                $Matches[0] = "http://" . $Matches[0];
            }

            return "<a href=\"" . $Matches[0] . "\" target=\"_blank\">
                        <img alt=\"Image\" width=\"450\" border=\"0\" src=\"" . $Matches[0] . "\" />
                    </a>";
        }
    }

    
    /**
     * MakeView
     * @param String $Comment
     * @return String
     */
    protected function MakeView($Comment) {
        $this->Comment = $Comment;
        $Comment = preg_replace_callback($this->RegularImg, array($this, 'ViewImages'), $Comment);
        $this->Comment = $Comment;
        $Comment = preg_replace_callback($this->RegularYoutube, array($this, 'ViewYoutube'), $Comment);

        return $Comment;
    }

    /**
     * DiscussionController_BeforeCommentBody_Handler
     * @param DiscussionController $Sender
     */
    public function DiscussionController_BeforeCommentBody_Handler(&$Sender) {
        $Sender->EventArguments['Comment']->Body = $this->MakeView($Sender->EventArguments['Comment']->Body);
    }

    /**
     * PostController_BeforeCommentBody_Handler
     * @param PostController $Sender
     */
    public function PostController_BeforeCommentBody_Handler(&$Sender) {
        $Sender->EventArguments['Comment']->Body = $this->MakeView($Sender->EventArguments['Comment']->Body);
    }

    /**
     * PostController_BeforeDiscussionRender_Handler
     * @param PostController $Sender
     */
    public function PostController_BeforeDiscussionRender_Handler(&$Sender) {
        if ($Sender->View == 'preview') {
            $Sender->Comment->Body = $this->MakeView($Sender->Comment->Body);
        }
    }

    /**
     * PostController_BeforeCommentRender_Handler
     * @param PostController $Sender
     */
    public function PostController_BeforeCommentRender_Handler(&$Sender) {
        if ($Sender->View == 'preview') {
            $Sender->Comment->Body = $this->MakeView($Sender->Comment->Body);
        }
    }

    /**
     * Setup
     */
    public function Setup() {
    }
}
