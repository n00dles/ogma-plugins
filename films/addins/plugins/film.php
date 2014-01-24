<?php 

// default Hello World plugin for OGMA CMS

Plugins::registerPlugin( 
				'film',
        'Film',
        'Sample Film Database for OGMA CMS',
        '0.0.1',
        'Mike Swan',
        'http://www.digimute.com/'
        );

class Film{
	
	public function __construct() {
 
    }

    public static function init(){
        // check if the table exists 
        if (Query::tableExists('taglines')){
            Actions::addAction('admin-add-sidebar','Menu::addSidebarMenu',1,array("Films",'','film','fa fa-fw fa-film'));
            Actions::addAction('admin-add-to-dashboard','Menu::addDashboardItem',1,array("Films",'','film','fa fa-fw fa-film'));
        }
        $language = Core::$site['language'];
        //Lang::mergeLanguage(Core::$settings['pluginpath'].'testimonials'.DS.'lang'.DS.$language.'.lang.php');
    }

    public static function admin(){
        $action = Core::getAction();            // get URI action
        $id = Core::getID();                    // get page ID

        $table = new Query('film');



        $table->getCache();

        extract(Query::getSortOptions());
         if ($action=="deleterecord" ){
          $nonce = $_POST['security-nonce'];
          $record = $_POST['security-record'];
          $tableid = $_POST['security-table'];
          if (Security::checkNonce($nonce,'deleterecord', 'film')){
            $ret=$table->deleteRecord($record);
            $action='view';
          } else {
            echo "something wrong...";
          }

        }

        if ($action=="createnew"){
          Debug::pa($_REQUEST);

              $ret = $table->addRecordForm();
              $action='view';
              $_GET['action']='view';
            }


        if ($action=="update"){
          $record = $table->getFullRecord($id);
          $fieldTypes= $table->tableFields;
          foreach($fieldTypes as  $name => $val){
            if (!isset($_POST['post-'.$name]) && $val=="checkbox") $_POST['post-'.$name]='false';
            if(isset($_POST['post-'.$name])){
              $record[$name]=Utils::manipulateValues($_POST['post-'.$name],$val);
            }
          }
          $ret=$table->saveRecord($record,$id);
          if (isset($_POST['submitclose'])){ 
            $action='view';
          } else {
            $action='edit';
            $_GET['action']='edit';
          }
        } 

        if ($action=='view'){

          $totalRecords = $table->count();
          $records = $table->order($sort,$dir)->range($page*15,15)->get();
            ?>
            <div class="col-md-10">
          <?php 
          Core::getAlerts();
          ?>
            <legend>View Taglines</legend>
             <div class="btn-group" style="padding-bottom:15px;">
                <button class="btn btn-primary" onclick="location.href='load.php?tbl=film&action=create'"><span class="glyphicon glyphicon-plus"></span> Create New Tagline</button>
             </div>

              <?php 

              $table->htmlTableHeader(
                  // array of headings
                  array(
                      "Title" =>"title",
                      "Year"=>"year",
                      "Cost"=>"cost"
                    ),
                  // array of options, in this case entries for dropdown
                  array(
                    'widths'=>'50|20|20'
                    ), true
                );
              if (count($records)>0){
                foreach ($records  as $record) {
                 $table->htmlTableRow($record,array(
                      'widths'=>'5|50|20|15'
                      ), true); 

                }
              }
              $table->htmlTableFooter();
              Query::doPagination($page,$totalRecords);
            ?>
            
        </div>
        <?php
        }

        if ($action=='edit' || $action=="create"){
          $record = $table->getFullRecord($id);
            ?>
            <div class="col-md-10">

             <?php 

            $ogmaForm = new Form();

            if ($action=="edit") $ogmaForm->addHeader("Edit Film "." : ".$record['id']);
            if ($action=="create") $ogmaForm->addHeader("Create Film");
           
            $ogmaForm->startTabHeaders();

            $ogmaForm->createTabHeader(array('main'=>'Main'),true);
            
            Actions::executeAction('film-tab-header');

            $ogmaForm->endTabHeaders();

            if ($action=="edit") $ogmaForm->createForm('load.php?tbl=film&action=update&amp;id='.$record['id']);
            if ($action=="create") $ogmaForm->createForm('load.php?tbl=film&action=createnew');

            $ogmaForm->startTabs();
            $ogmaForm->createTabPane('main',true);
            $ogmaForm->displayField('post-title', "Title" ,  $table->tableFields['title'], '',$record['title']);
            $ogmaForm->displayField('post-description', "Description" ,  $table->tableFields['description'], '',$record['description']);
            $ogmaForm->displayField('post-year', "Year of Release" ,  $table->tableFields['year'], '',$record['year']);
            $ogmaForm->displayField('post-duration', "Rental Duration (days)" ,  $table->tableFields['duration'], '',$record['duration']);
            $ogmaForm->displayField('post-rate', "Rating" ,  $table->tableFields['rate'], '',$record['rate']);
            $ogmaForm->displayField('post-length', "Length (mins)" ,  $table->tableFields['length'], '',$record['length']);
            $ogmaForm->displayField('post-cost', "Cost" ,  $table->tableFields['cost'], '',$record['cost']);
            $ogmaForm->displayField('post-features', "Features" ,  $table->tableFields['features'], '',$record['features']);
    
            $ogmaForm->displayField('post-id','ID', 'hidden', '',$record['id']);
            
            Actions::executeAction('film-tab-new');

            $ogmaForm->endTabs();
            
            $ogmaForm->formButtons(true);
            $ogmaForm->endForm();

            $ogmaForm->show();

            ?>
            </div>
        <?php
        } 
    }

   
}

?>
