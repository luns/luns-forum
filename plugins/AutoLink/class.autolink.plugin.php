<?php if (!defined('APPLICATION'))  exit();

// Define the plugin:
$PluginInfo['AutoLink'] = array(
    'Name' => 'Auto Link',
    'Description' => 'Highlights and Link certain keywords in comments (specified by you in the plugin). Modify your own links and keywords and the css to display highlight characteristics. Also, this plugin can be used in conjunction with the tagging plugin to Highlight words that match words in Tag database.',
    'Version' => '1.1',
    'RequiredApplications' => array('Vanilla' => '2.0'),
    'RequiredTheme' => FALSE, 
    'RequiredPlugins' => FALSE,
    'HasLocale' => FALSE,
    'SettingsUrl' => '/dashboard/plugin/autolink',
    'Author' => "Peregrine" 
);

class AutoLinkPlugin extends Gdn_Plugin {

    public function PluginController_AutoLink_Create($Sender) {
        $Sender->Title('Auto Link');
        $Sender->AddSideMenu('plugin/autolink');
        $Sender->Permission('Garden.Settings.Manage');
        $Sender->Form = new Gdn_Form();
        $Validation = new Gdn_Validation();
        $ConfigurationModel = new Gdn_ConfigurationModel($Validation);
        $ConfigurationModel->SetField(array(
            'Plugins.AutoLink.Tags',
            'Plugins.AutoLink.Links',
            'Plugins.AutoLink.Precedence',
        ));
        $Sender->Form->SetModel($ConfigurationModel);


        if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
            $Sender->Form->SetData($ConfigurationModel->Data);
        } else {
            $Data = $Sender->Form->FormValues();

            if ($Sender->Form->Save() !== FALSE)
                $Sender->StatusMessage = T("Your settings have been saved.");
        }

        $Sender->Render($this->GetView('al-settings.php'));
    }

    protected function WordFind(&$text) {
        $search = '/(\b\p{L}{4,15}\b)/u';
        $text = preg_replace_callback($search, array($this, 'LinkWord'), $text);
        return $text;
    }

    protected $TagArray = Array();
    public $linksOn, $tagsOn;

    public function DiscussionController_Render_Before($Sender) {
        $this->AttachAutolinkResources($Sender);

        if (C('Plugins.AutoLink.Tags')) {
            $TagArray = $this->GetAutolinkTags();
        }
    }

    public function CategoriesController_Render_Before($Sender) {
        $this->DiscussionController_Render_Before($Sender);
    }

    public function DiscussionController_AfterCommentFormat_Handler($Sender) {
        $Object = $Sender->EventArguments['Object'];
        if ((C('Plugins.AutoLink.Links')) || (C('Plugins.AutoLink.Tags')))
            $this->WordFind($Object->FormatBody);
    }

    public function PostController_AfterCommentFormat_Handler($Sender) {
        $this->DiscussionController_AfterCommentFormat_Handler($Sender);
    }

    public function CategoryController_AfterCommentFormat_Handler($Sender) {
        $this->DiscussionController_AfterCommentFormat_Handler($Sender);
    }

    protected function AttachAutolinkResources($Sender) {
        $Sender->AddCssFile('al.css', 'plugins/AutoLink');
    }

    static $autosetlinks = FALSE;

    public function LinkWord($match) {
        $item = $match[0];


        $wordlinkArray = array("documentation" => "http://vanillaforums.org/docs",
            "wiki" => "http://vanillawiki.homebrewforums.net/index.php/Main_Page",
            "localization" => "http://vanillaforums.org/docs/localization",
            "plugin" => "http://vanillaforums.org/docs/pluginquickstart",
            "bonk" => "http://vanillaforums.org/docs/errors",
            "config" => "http://vanillaforums.org/docs");

        $words = array_keys($wordlinkArray);
        //  $links =  array_values($wordlinkArray);




        if (!$autosetlinks) {
            if (C('Plugins.AutoLink.Links'))
                $linksOn = TRUE;
            if (C('Plugins.AutoLink.Tags'))
                $tagsOn = TRUE;
            if (C('Plugins.AutoLink.Precedence'))
                $autolinkorder = TRUE;

            $autosetlinks = TRUE;
        }


        if (!$autolinkorder) {
            if ($tagsOn) {
                $this->AutoLinkTheTags($item);
            }
            if ($linksOn) {
                $this->AutoLinktheLink($item, $words, $wordlinkArray);
            }
        } else {
            if ($linksOn) {
                $this->AutoLinktheLink($item, $words, $wordlinkArray);
            }
            if ($tagsOn) {
                $this->AutoLinkTheTags($item);
            }
        }

        return $item;
    }

    public function AutoLinktheLink($item, $words, $wordlinkArray) {
        if (in_array($item, $words)) {
            $link = $wordlinkArray[$item];
            $item = '<a class="autolinker" title="click me to for more information" href="' . $link . '">' . $item . '</a>';
        }
    }

    public function AutoLinkTheTags($item) {
        if (in_array($item, $this->TagArray)) {
            $item = '<a class="autotagger" title="click me to see discussions tagged with this word" href="' . "discussions/tagged/$item" . '">' . $item . '</a>';
        }
    }

    public function GetAutoLinkTags() {

        $MyTagsModel = new Gdn_Model('Tags');
        $MyTags = $MyTagsModel->SQL
                ->Select('Name')
                ->From('Tag')
                ->Get()
                ->ResultArray();
        $TagArray = array();
        foreach ($MyTags as $VTag) {
            array_push($TagArray, $VTag['Name']);
        }

        $gettags++;
        $this->TagArray = $TagArray;
        return $TagArray;
    }

    public function Setup() {
        //no setup needed
    }

}


