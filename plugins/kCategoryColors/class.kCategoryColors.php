<? 
/**
 * @author Josh Grochowski (josh at kastang dot com)
 */

if(!defined('APPLICATION')) { exit(); }

class kCategoryColors extends Gdn_Module {

    protected $SQL;

    public function __construct(&$Sender = '') { 
        $this->SQL = Gdn::SQL();
        parent::__construct($Sender); 
    }


   /**
    * Generates the content that will be displayed in the sidebar. 
    */
    public function ToString() {

        $CategoryColors = $this->SQL->Select('*')->From('kCategoryColors')->Get()->Result();

        $String = '';
        ob_start();

echo <<<EOT

<style type='text/css'>
<!--

EOT;
        foreach($CategoryColors as $cc) {
echo <<<EOT
        .Category_$cc->CategoryId {
            background-color: #$cc->CategoryColor;
        }

EOT;
        }

echo <<<EOT
-->
</style>
EOT;

        $String = ob_get_contents();
        @ob_end_clean();
        return $String;

    }
}
?>
