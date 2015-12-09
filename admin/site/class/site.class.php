<?php
class Site {

  private $title = 'My new website';  //Default values.
  private $keywords = '';
  private $footer = 'powered by JULIUS';
  private $device = 'desktop';
  private $style = 'julius';
  private $maintenance = 1;
  private $maintenancetext = 'Site under maintenance';
  private $baseroot = '';
  private $baselink = '';
  private $browser = 'Unknown';
  private $path = array ();
  private $admin = '';
  private $freereg = false;
  private $modfiles = 0755;
  private $datalink = null;
  private $debug = false;
  private $microini = 0;
  private $forcedLang = '';
  private $token = 'content';
  
/*
 * CONSTRUCTOR
 */
  
  public function __construct( $sqlparams, $debug = false ){
    $this->microini = microtime();
    $this->debug = $debug;
    $pathstr = '';
    $this->datalink = new Datalink( $sqlparams, $debug );  //Database connection (debug)
    if( !$this->isInstalled() ){
      $this->datalink->sqlDump( 'admin/site/install/install.sql' );
    }
    $this->loadLanguage();
    $this->loadPath();
    $this->loadToken();
    $this->loadInformation();
    $this->loadAppearance();
    $browser = $this->checkBrowser();
    $this->browser = $browser['name'].' '.$browser['version'].' ('.$browser['platform'].')';
    if( !isset( $this->path[0] )
    or $this->path[0] == $_SERVER['HTTP_HOST'] ){
      $this->path[0] = 'home';
      header( 'Location: '.$this->getBaselink().'/home', true, 301 );  //Redirecting.
      exit();
    }
    if( $this->debug ){
      echo "
      <script>console.log('DEVICE: ".$this->device."');</script>";
      foreach( $this->path as $key => $value ){
        echo "
        <script>console.log('PATH: ".$key." => ".$value."');</script>";
      }
      echo "
      <script>console.log('TYPE SERVED: ".$this->token."');</script>";
    }
    register_shutdown_function( 'shutdown', $this );
  }
  
/*
 * GETTERS
 */
 
  public function getTitle(){
    return $this->title;
  }
  public function setTitle( $string ){
    $this->title = $string;
  }
  
  public function getKeywords(){
    return $this->keywords;
  }
  public function setKeywords( $text ){
    $this->keywords = $text;
  }
  
  public function getFooter(){
    return $this->footer;
  }
  public function setFooter( $text ){
    $this->footer = $text;
  }
  
  public function getDevice(){
    return $this->device;
  }
  public function setDevice( $string ){
    $this->device = $string;
  }
  
  public function getStyle(){
    return $this->style;
  }
  public function setStyle( $string ){
    $this->style = $string;
  }
  
  public function getMaintenance(){
    return $this->maintenance;
  }
  public function setMaintenance( $boolean ){
    $this->maintenance = $boolean;
  }
  
  public function getMaintenancetext(){
    return $this->maintenancetext;
  }
  public function setMaintenancetext( $text ){
    $this->maintenancetext = $text;
  }
  
  public function getBaseroot(){
    return $this->baseroot;
  }
  
  public function getBaselink(){
    return $this->baselink;
  }
  
  public function getBrowser(){
    return $this->browser;
  }
  
  public function getPath(){
    return $this->path;
  }
  
  public function getAdmin(){
    return $this->admin;
  }
  
  public function getFreereg(){
    return $this->freereg;
  }
  public function setFreereg( $boolean ){
    $this->freereg = $boolean;
  }
  
  public function getDatalink(){
    return $this->datalink;
  }
  public function setDatalink( $datalink ){
    $this->datalink = $datalink;
  }
  
  public function getForcedLang(){
    return $this->forcedLang;
  }
  
  public function getToken(){
    return $this->token;
  }
  
  public function getDebug(){
    return $this->debug;
  }
  
  public function getModfiles(){
    return $this->modfiles;
  }
  
/*
 * PUBLIC METHODS
 */
 
  public function loadToken(){
    if( !$this->isConfigured() ){
      $this->token = 'config';
    }else{
      if( $this->getAdmin() != '' ){
        $this->token = 'backend';
      }else{
        $query = 'select id 
        from int_admin
        where url="'.$this->path[0].'"';
        if( $this->datalink->dbQuery( $query, 'rows' ) > 0 ){
          $this->token = 'frontend';
        }
      }
    }
  }
  
  public function loadLangfile(){
    $requested = 'lib/lang/'.$_SESSION['lang'].'.php';
    if( file_exists( $requested ) ){
      $file = $requested;
    }else{
      $file = 'lib/lang/en.php';
    }
    return $file;
  }

  public function dirList( $directory ){
    $results = array();  //Files and folder names
    $files = array();    //Useful file names at the end of execution
    if( is_dir( $directory ) ){
      $results = scandir ( $directory );
    }
    foreach( $results as $file ){
      if( $file != '.' and $file != '..' ){ //Not significant used folder symbols
        $files[] = $file;
      }
    }
    sort( $files );
    return $files;
  }

  public function inputCheck(){  //Processes post and get variables
    $log = '';
    foreach ($_POST as $key => $value){
      switch ($key){
        case 'code':
          $_POST[$key] = strip_tags ($_POST[$key],'<p><a><h1><h2><h3><h4><table><tr><th><td><div><ul><ol><li><sub><sup><img><iframe><script><strong><b><br><embed><object><param><span><em>'); 
          $_POST[$key] = str_replace ('"', "'", $_POST[$key]); //HTML code
        break;
        default:
          $_POST[$key] = strip_tags ($_POST[$key]);
          $_POST[$key] = str_replace ('"', '&#34;', $_POST[$key]);
          $_POST[$key] = str_replace ("'", '&#39;', $_POST[$key]);
      }
      $value = $_POST[$key];
      if( $this->debug){
        if( $key == 'code'){
          $value = '<html code>';
        }
        echo "
        <script>console.log('POST: ".$key." => ".$value."')</script>";
      }
      if( $key == 'password'){
        $value = '****';
      }
      $log .= 'POST['.$key.'] => '.$value."\r\n";
    }
    foreach ($_GET as $key => $value){
      $_GET[$key] = str_replace (' ', '_', $_GET[$key]);
      $_GET[$key] = str_replace (array ('"', "'", ",", ";","="), '', $_GET[$key]);
      if( $this->debug){
        echo "
        <script>console.log('GET: ".$key." => ".$_GET[$key]."')</script>";
      }
      $log .= 'GET['.$key.'] => '.$value."\r\n";
    }
    if( isset($_FILES)){
      foreach ($_FILES as $key => $value){
        if( $this->debug){
          echo "
          <script>console.log('FILES: ".$key."')</script>";
        }
        $log .= 'FILES['.$key.'] => '.var_export($value, true)."\r\n";
      }
    }
    foreach ($this->path as $key => $value){
      if( $value != ''){
        $log .= 'PATH: '.$key.' => '.$value."\r\n";
      }
    }
    if( isset($_POST['lang'])){
      $_SESSION['lang'] = $_POST['lang'];
    }
    if( $this->debug){
      foreach ($_SESSION as $k => $v){
        if( !is_array($v)){
          echo "
          <script>console.log('SESSION: ".$k." => ".$v."')</script>";
        }
      }
    }
    if( count($_POST) > 0){
      $this->addLog($log);
    }
  }

  public function getMetas(){
    $metas = Array ();
    $query = 'select name,
    value
    from int_metas
    limit 10';
    if( $result = $this->datalink->dbQuery($query, 'result')){
      foreach ($result as $variable){
        $metas[$variable[0]] = $variable[1];
      }
    }
    return $metas;
  }

  public function saveMetas($metas){
    $errors = '';
    if( count($metas) > 0){
      $this->datalink->dbQuery('truncate int_metas', 'query');
      $i = 1;
      foreach ($metas as $name => $value){
        if( $i <= 10
        and $name != ''
        and $value != ''){
          $query = 'insert into int_metas (
          name,
          value
          ) values (
          "'.$name.'", 
          "'.$value.'"
          )';
          if( $this->datalink->dbQuery($query, 'query') <= 0){
            $error = Meta_variables_not_saved;
          }
        }
        $i++;
      }
    }
    return $errors;
  }

  public function isConfigured(){
    $query = 'select id 
    from int_user 
    where charge=1
    limit 1';
    return ($this->datalink->dbQuery($query, 'rows') > 0);
  }

  public function imageUpload ($file, $dir) {
    $ret = '';
    $mwidth = 1024;  //Maximum size of uploaded pictures.
    $mheight = 768;
    $imagename = str_replace (array (' ', "'"), '_', $file['name']); //Store relevant information
    if( $file['error'] > 0) {
      switch ($file['error']){
      case 1:
      case 2:
        echo '<p class="error">'.File_uploaded_is_too_big.'.</p>';
        break;
      case 3:
        echo '<p class="error">'.File_uploaded_partially.'.</p>';
        break;
      case 4: break;
      default:
        echo '<p class="error">'.File_not_uploaded.'.</p>';
      }
    }else{
      $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
      switch ($ext){
        case 'jpg':
        case 'JPG':
        case 'jpeg':
        case 'JPEG':
        case 'png':
        case 'PNG':
          $image = new Image($file['tmp_name']);
          $imageinfo = (getimagesize ($file['tmp_name']));
          if( $imageinfo[0] > $imageinfo[1] 
          and $imageinfo[0] > $mwidth){
            $image->resizeToWidth($mwidth);  //Picture width
          }
          if( $imageinfo[1] > $imageinfo[0] 
          and $imageinfo[1] > $mheight){
            $image->resizeToHeight($mheight);  //Picture height
          }
          $validate = false;
          while (!$validate){
            if( file_exists ('uploads/'.$dir.'/'.$imagename)){
              $imagename = '-'.$imagename;
            }else{
              $validate = true;
            }
          }
          if( $image->save ('uploads/'.$dir.'/'.$imagename)){
            $ret = $imagename;
            chmod ('uploads/'.$dir.'/'.$imagename, $this->modfiles);  //Uploading done
          }
          break;
      }
    }
    return $ret;
  }

  public function fileUpload ($file, $dir) {
    $ret = '';
    $nom = str_replace (array (' ', "'"), '_', $file['name']); //Store relevant information
    if( $file['error'] > 0) {
      switch ($file['error']){
      case 1:
      case 2:
        echo '<p class="error">'.File_uploaded_is_too_big.'.</p>';
        break;
      case 3:
        echo '<p class="error">'.File_uploaded_partially.'.</p>';
        break;
      case 4: break;
      default:
        echo '<p class="error">'.File_not_uploaded.'.</p>';
      }
    }else{
      $validar = false;
      while (!$validar){
        if( file_exists ('uploads/'.$dir.'/'.$nom)){
          $nom = '-'.$nom;
        }else{
          $validar = true;
        }
      }
      if( move_uploaded_file($file['tmp_name'], 'uploads/'.$dir.'/'.$nom)){
        $ret = $nom;
      }
    }
    return $ret;
  }

  public function languageMenu(){  //Language menu code
    $html = '';
    $query = 'select distinct lang, name
    from int_lang';
    $result = $this->datalink->dbQuery($query, 'result');
    if( count($result) > 1){
      $html .= '
      <form name="language" method="post" action="">
      <ul class="langmenu">';
      foreach ($result as $language){
        $html .= '<li><input type="submit" name="lang" class="'.$language[0].'" value="'.$language[0].'" title="'.$language[1].'"></li>';
      }
      $html .= '
      </ul>
      </form>
      ';
    }
    return $html;
  }

  public function moduleMenu(){  //Module shortcut menu code
    $html = '';
    $query = 'select id
    from int_admin
    where active=1
    and shortcut=1
    order by corder asc';
    if( $result = $this->datalink->dbQuery($query, 'result')){
      if( count($result) > 0){
        $html .= '
        <ul class="modulemenu">';
        foreach ($result as $row){
          $module = new Module($this->datalink, $row[0]);
          $html .= '<li><a href="'.$this->getBaselink().'/'.$module->getUrl().'"';
          if( isset ($this->path[0])
          and $this->path[0] == $module->getUrl()){
            $html .= ' class="active"';
          }
          $html .= '>';
          if( $_SESSION['logged']
          and $module->getUrl() == 'login'){
            $html .= '<b>'.$_SESSION['username'].'</b>';
          }else{
            $html .= $module->getName();
          }
          $html .= '</a></li>';
        }
        $html .= '
        </ul>
        ';
      }
    }
    return $html;
  }
  
  public function contentMenu(){  //Content menu code
    $html = '';
    $query = 'select id from int_content
    where menu=1
    and father=0
    order by corder asc';
    if( $result = $this->datalink->dbQuery($query, 'result')){
      if( count($result) > 0){
        $html .= '
        <ul class="contentmenu">';
        foreach ($result as $row){
          $dad = new Content($this->datalink, $row[0]);
          $html .= '<li class="hoverable"><a ';
          if( $this->path[0] == $dad->getUrl()
          or $dad->isFatherOf($this->path[0])){
            $html .= ' class="active"';
          }
          if( !$dad->hasChildren()){
            $html .= ' href="'.$this->baselink.'/'.$dad->getUrl().'"';
          }
          $html .= '>'.$dad->getTitle().'</a>';
          $query = 'select id from int_content
          where menu=1
          and father="'.$dad->getId().'"
          order by corder asc';
          if( $subresult = $this->datalink->dbQuery($query, 'result')){
            if( count($result) > 0){
              $html .= '
              <div class="subcontentmenu">';
              foreach ($subresult as $subrow){
                $child = new Content($this->datalink, $subrow[0]);
                $html .= '<p><a href="'.$this->baselink.'/'.$child->getUrl().'"';
                if( $this->path[0] == $child->getUrl()){
                  $html .= ' class="active"';
                }
                $html .= '>'.$child->getTitle().'</a></p>';
              }
              $html .= '</div>';
            }
          }
          $html .= '</li>';
        }
        $html .= '</ul>';
      }
    }
    return $html;
  }

  public function privateMenu($user){
    $html = '';
    $query = 'select id
    from int_admin
    where active=1
    order by corder asc';
    if( $result = $this->datalink->dbQuery($query, 'result')){
      if( count($result) > 0){
        $html .= '<ul class="privatemenu">';
        foreach ($result as $row){
          $module = new Module($this->datalink, $row[0]);
          if( $user->checkPermission($module->getId())){
            $html .= '
            <li><a href="'.$this->getBaselink().'?adm='.$module->getUrl().'"';
            if( file_exists( 'admin/'.$module->getFolder().'/icon.png' ) ){
              $html .= ' style="background-image:url('."'".$this->getBaseroot().'admin/'.$module->getFolder().'/icon.png'."'".')"';
            }
            $html .= '>'.$module->getName().'</a></li>';
          }
        }
        $html .= '</ul>';
      }
    }
    return $html;
  }
  
  public function metaVariables(){
    $html = '';
    $query = 'select name,
    value
    from int_metas
    limit 10';
    if( $result = $this->datalink->dbQuery($query, 'result')){
      foreach ($result as $meta){
        $html .= '
        <meta name="'.$meta[0].'" content="'.$meta[1].'">';
      }
    }
    return $html;
  }

  public function saveConfiguration(){
    $error = '';
    $query = 'truncate int_information';
    $this->datalink->dbQuery($query, 'query');
    $query = 'insert into int_information (
    title,
    footer,
    maintenance,
    maintenancetext,
    register
    ) values (
    "'.$this->getTitle().'",
    "'.$this->getFooter().'",
    "'.$this->getMaintenance().'",
    "'.$this->getMaintenancetext().'",
    "'.$this->getFreereg().'"
    )';
    if( $this->datalink->dbQuery($query, 'query') <= 0){
       $error = '<p class="error">'.Site_configuration_not_saved.'</p>';
    }
    return $error;
  }
  
  public function addLog( $data ){
    $query = 'delete from int_overwatch where instant<'.( time() - (86400 * 180) );  //Logging since half a year ago
    $this->datalink->dbQuery($query, 'query');
    $query = 'insert into
    int_overwatch (
    instant,
    device,
    browser,
    ip,
    username,
    data
    ) values (
    "'.time().'",
    "'.$this->device.'",
    "'.$this->browser.'",
    "'.$_SERVER['REMOTE_ADDR'].'",
    "'.$_SESSION['username'].'",
    "'.$data.'"
    )';
    $this->datalink->dbQuery( $query, 'query' );
  }
  
  public function shut(){
    $this->datalink->closeDb();
    if( $this->debug){
      echo "<script>console.log('TIME TAKEN: ".(microtime() - $this->microini)."s');</script>";
    }
  }
  
  public function showMaintenance(){
    echo '<p class="error">'.nl2br($this->maintenancetext).'</p>';
  }

  public function saveLang($reference, $name){
    $errors = '';
    $query = 'select lang, name
    from int_lang
    where lang="'.$reference.'"';
    if( $this->datalink->dbQuery($query, 'rows') > 0){
      $query = 'update int_lang
      set name="'.$name.'"
      where lang="'.$reference.'"';
    }else{
      $query = 'insert into int_lang (
      lang,
      name
      ) values (
      "'.$reference.'",
      "'.$name.'"
      )';
    }
    if( $this->datalink->dbQuery($query, 'query') <= 0){
      $errors .= Language_not_saved;
    }
    return $errors;
  }
  
  public function forceLanguage($reference = ''){
    $query = 'update int_lang set forced=0';
    $this->datalink->dbQuery($query, 'query');
    if( $reference != ''){
      $query = 'update int_lang set forced=1
      where lang="'.$reference.'"';
      $this->datalink->dbQuery($query, 'query');
      $this->forcedLang = $reference;
    }
  }
  
  public function deleteLang($reference){
    $errors = '';
    $query = 'select lang, name, forced
    from int_lang
    order by name asc';
    if( $this->getDatalink()->dbQuery($query, 'rows') > 1){
      $query = 'delete from int_lang
      where lang="'.$reference.'"';
      if( $this->datalink->dbQuery($query, 'query') <= 0){
        $errors = Language_could_not_be_deleted;
      }
    }else{
      $errors = Language_could_not_be_deleted;
    }
    return $errors;
  }
  
  public function addTheme($uploadedzip){
    $errors = '';
    if( $uploadedzip['error'] > 0) {
      switch ($uploadedzip['error']){
        case 1:
        case 2:
          echo '<p class="error">'.File_uploaded_is_too_big.'.</p>';
          break;
        case 3:
          echo '<p class="error">'.File_uploaded_partially.'.</p>';
          break;
        case 4: break;
        default:
          echo '<p class="error">'.File_not_uploaded.'.</p>';
      }
      echo '</p>';
    }else{
      $errors = '';
      $ext = pathinfo($uploadedzip['name'], PATHINFO_EXTENSION);
      switch ($ext){
        case 'zip':
        case 'ZIP':
          $zip = new ZipArchive();
          $zip->open($uploadedzip['tmp_name']);
          $basevalue = explode ('/', $zip->getNameIndex(0));
          $basevalue = $basevalue[0];
          if( $this->debug){
            echo "<script>console.log('THEME-ZIP[0] ".$basevalue."');</script>";
          }
          if( $zip->getFromName($basevalue.'/'.$basevalue.'.php')
          and $zip->getFromName($basevalue.'/icon.png')
          and $zip->getFromName($basevalue.'/desktop.css')
          and $zip->getFromName($basevalue.'/mobile.css')
          and $zip->getFromName($basevalue.'/tablet.css')){  //Checking existence of main needed files
             $query = 'insert into int_styles (
             name,
             folder,
             active
             ) values (
             "'.str_replace('.'.$ext, '', $uploadedzip['name']).'",
             "'.$basevalue.'",
             "0"
             )';
             if( $this->getDatalink()->dbQuery($query, 'query') > 0){
               $zip->extractTo('styles/');
             }else{
               $errors .= Theme_not_installed;
             }
          }else{
            $errors .= Zip_theme_format_not_correct;
          }
          break;
          default:
            $errors .= Zip_theme_format_not_correct;
            break;
      }
      if( $errors != ''){
        return '<p class="error">'.$errors.'</p>';
      }else{
        return '<p class="fine">'.Theme_installed.'</p>';
      }
    }
  }
  
  public function delTheme($themefolder){
    if( $themefolder != ''){
      $this->rrmdir('styles/'.$themefolder);
      if( !file_exists('styles/'.$themefolder)){
        return '<p class="fine">'.Theme_deleted.'</p>';
      }else{
        return '<p class="error">'.Theme_was_not_deleted.'</p>';
      }
    }
    return '<p class="error">'.Theme_was_not_deleted.'</p>';
  }

  public function addModule($uploadedzip){
    $errors = '';
    if( $uploadedzip['error'] > 0) {
      switch ($uploadedzip['error']){
        case 1:
        case 2:
          echo '<p class="error">'.File_uploaded_is_too_big.'.</p>';
          break;
        case 3:
          echo '<p class="error">'.File_uploaded_partially.'.</p>';
          break;
        case 4: break;
        default:
          echo '<p class="error">'.File_not_uploaded.'.</p>';
      }
      echo '</p>';
    }else{
      $errors = '';
      $ext = pathinfo($uploadedzip['name'], PATHINFO_EXTENSION);
      switch ($ext){
        case 'zip':
        case 'ZIP':
          $zip = new ZipArchive();
          $zip->open($uploadedzip['tmp_name']);
          $basevalue = explode ('/', $zip->getNameIndex(0));
          $basevalue = $basevalue[0];
          if( $this->debug){
            echo "<script>console.log('MODULE-ZIP[0] ".$basevalue."');</script>";
          }
          if( $zip->getFromName($basevalue.'/backend.php')
          and $zip->getFromName($basevalue.'/frontend.php')
          and $zip->getFromName($basevalue.'/lang/en.php')
          and $zip->getFromName($basevalue.'/install/install.sql')
          and $zip->getFromName($basevalue.'/install/uninstall.sql')){  //Checking existence of main needed files
            $query = 'select corder from int_admin order by corder desc limit 1';
            $result = $this->datalink->dbQuery($query, 'result');
            if( isset ($result[0])
            and $row = $result[0]){
              $order = $row[0];
            }else{
              $order = 3;
            }
            $query = 'insert into int_admin (
            name,
            url,
            folder,
            backend,
            frontend,
            shortcut,
            corder,
            active
            ) values (
            "'.str_replace('.'.$ext, '', $uploadedzip['name']).'",
            "'.$basevalue.'",
            "'.$basevalue.'",
            "'.$basevalue.'/backend.php",
            "'.$basevalue.'/frontend.php",
            "0",
            "'.($order + 1).'",
            "0"
            )';
            if( $this->getDatalink()->dbQuery($query, 'query') > 0){
              $zip->extractTo('admin/');
              mkdir ('uploads/'.$basevalue);
              $this->datalink->sqlDump('admin/'.$basevalue.'/install/install.sql');
            }else{
              $errors .= Module_not_installed;
            }
          }else{
            $errors .= Zip_module_format_not_correct;
          }
          break;
          default:
            $errors .= Zip_module_format_not_correct;
            break;
      }
      if( $errors != ''){
        return '<p class="error">'.$errors.'</p>';
      }else{
        return '<p class="fine">'.Module_installed.'</p>';
      }
    }
  }
  
  public function delModule($modulefolder){
    if( $modulefolder != ''){
      $this->datalink->sqlDump('admin/'.$modulefolder.'/install/uninstall.sql');
      $this->rrmdir('admin/'.$modulefolder);
      $this->rrmdir('uploads/'.$modulefolder);
      if( !file_exists('admin/'.$modulefolder)
      and !file_exists('uploads/'.$modulefolder)){
        return '<p class="fine">'.Module_deleted.'</p>';
      }else{
        return '<p class="error">'.Module_was_not_deleted.'</p>';
      }
    }
    return '<p class="error">'.Module_was_not_deleted.'</p>';
  }

  public function activeLanguage(){
    $query = 'select name
    from int_lang
    where lang="'.$_SESSION['lang'].'"';
    $result = $this->datalink->dbQuery($query, 'result');
    if( isset($result[0])){
      return $result[0][0];
    }else{
      return '?';
    }
  }
  
  public function adminEmail(){
    $result = $this->datalink->dbQuery('select email from int_user where charge=1 limit 1', 'result');
    if( isset ($result[0])){
      return $result[0][0]; //Administrator Email
    }else{
      return '?';
    }
  }
  
  public function dbQuery( $query, $type ){
    return( $this->datalink->dbQuery( $query, $type ) );
  }
  
/*
 * PRIVATE METHODS
 */
 
  private function isInstalled(){  //Checks if the system is properly installed
    $query = 'select int_admin.id,
    int_charges.id
    from int_admin, int_charges
    where int_admin.url="site"
    and int_charges.id="1"
    limit 1';
    return ($this->datalink->dbQuery($query, 'rows') > 0);
  }

  private function loadInformation(){
    $query = 'select title,
    footer,
    maintenance,
    maintenancetext,
    register
    from int_information
    limit 1';
    $result = $this->datalink->dbQuery($query, 'result');
    if( isset($result[0])
    and count($result[0]) == 5){
      $this->title = $result[0][0];
      $this->footer = $result[0][1];
      $this->maintenance = $result[0][2];
      $this->maintenancetext = $result[0][3];
      $this->freereg = $result[0][4];
    }
    switch($this->token){
      case 'content':
        if( isset($this->path[0])
        and $this->path[0] != 'home'){
          $query = 'select int_content_trad.title
          from int_content_trad, int_content
          where int_content.url="'.$this->path[0].'"
          and int_content.id=int_content_trad.id
          and int_content_trad.lang="'.$_SESSION['lang'].'"';
          $result = $this->datalink->dbQuery($query, 'result');
          if( isset($result[0])
          and $row = $result[0]){
            $this->title = $row[0].' - '.$this->title;
          }
        }
      break;
      case 'frontend':
        if( isset($this->path[0])){
          $query = 'select int_admin_trad.name
          from int_admin_trad, int_admin
          where int_admin.url="'.$this->path[0].'"
          and int_admin.id=int_admin_trad.id
          and int_admin_trad.lang="'.$_SESSION['lang'].'"';
          $result = $this->datalink->dbQuery($query, 'result');
          if( isset($result[0])
          and $row = $result[0]){
            $this->title = $row[0].' - '.$this->title;
          }
        }
      break;
    }
  }
  
  private function loadLanguage(){
    $query = 'select lang 
    from int_lang 
    where forced=1';
    $result = $this->datalink->dbQuery($query, 'result');
    if( isset($result[0])){
      $this->forcedLang = $result[0][0];
    }
    if( !isset ($_SESSION['lang']) 
    or !$this->langExists($_SESSION['lang'])){
      if( $this->forcedLang != ''){
        $_SESSION['lang'] = $this->forcedLang;  //Force a language on first visit
      }else{
        $_SESSION['lang'] = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);  //Browser preference as default language
      }
    }
  }

  private function langExists($lang){
    $query = 'select lang from int_lang where lang="'.$lang.'"';
    return ($this->datalink->dbQuery($query, 'rows') > 0);
  }
  
  private function loadAppearance(){
    $query = 'select folder
    from int_styles
    where active=1
    limit 1';
    $result = $this->datalink->dbQuery($query, 'result');
    if( isset($result[0])){
      $this->style = $result[0][0];
      $device = new DeviceDetector();
      if( $device->isMobile()){
        $this->device =  'mobile';
      } 
      if( $device->isTablet()){
        $this->device =  'tablet';
      }
    }
  }

  private function loadPath(){
    if( isset ($_SERVER['HTTPS']) 
    and $_SERVER['HTTPS'] != ''){
      $protocol = 'https';
    }else{
      $protocol = 'http';
    }
    $this->baseroot = $protocol.'://'.$_SERVER['HTTP_HOST'].str_replace ('index.php', '', $_SERVER['SCRIPT_NAME']);  //System root url for file linking
    $this->baselink = $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];  //Base site link.
    $pathstr = str_replace ($_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].'/', '', $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);  //Url asked
    $this->path = explode ('/', $pathstr);
    if( isset ($_GET['adm']) 
    and isset ($_SESSION['logged']) 
    and $_SESSION['logged'] == 1){  //Logged in
      $this->admin = $_GET['adm'];  //Refered module url
      $this->path[0] = '';
    }
  }
  
  private function checkBrowser() { 
    $u_agent = $_SERVER['HTTP_USER_AGENT']; 
    $bname = 'Unknown';
    $ub = 'Unknown';
    $platform = 'Unknown';
    $version= "";
    //First get the platform?
    if( preg_match( '/linux/i', $u_agent ) ){
        $platform = 'linux';
    }
    elseif( preg_match( '/macintosh|mac os x/i', $u_agent ) ){
      $platform = 'mac';
    }
    elseif( preg_match( '/windows|win32/i', $u_agent ) ){
      $platform = 'windows';
    }
    // Next get the name of the useragent yes seperately and for good reason
    if( preg_match( '/MSIE/i', $u_agent ) && !preg_match( '/Opera/i', $u_agent ) ){ 
      $bname = 'Internet Explorer'; 
      $ub = "MSIE"; 
    } 
    elseif( preg_match( '/Firefox/i', $u_agent ) ){ 
      $bname = 'Mozilla Firefox'; 
      $ub = "Firefox"; 
    } 
    elseif( preg_match( '/Chrome/i', $u_agent ) ){ 
      $bname = 'Google Chrome'; 
      $ub = "Chrome"; 
    } 
    elseif( preg_match( '/Safari/i', $u_agent ) ){ 
      $bname = 'Apple Safari'; 
      $ub = "Safari"; 
    } 
    elseif( preg_match( '/Opera/i', $u_agent ) ){ 
      $bname = 'Opera'; 
      $ub = "Opera"; 
    } 
    elseif( preg_match( '/Netscape/i', $u_agent ) ){ 
      $bname = 'Netscape'; 
      $ub = "Netscape"; 
    }
    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>'.join( '|', $known ).')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if( !preg_match_all( $pattern, $u_agent, $matches ) ){
        // we have no matching number just continue
    }
    // see how many we have
    $i = count( $matches['browser'] );
    if( $i != 1 
    and isset( $matches['version'] ) 
    and count( $matches['version'] ) == 2 ){
      //we will have two since we are not using 'other' argument yet
      //see if version is before or after the name
      if( strripos( $u_agent, "Version" ) < strripos( $u_agent, $ub ) ){
        $version = $matches['version'][0];
      }else{
        $version = $matches['version'][1];
      }
    }else{
      $version = $matches['version'][0];
    }
    // check if we have a number
    if( $version == null || $version == "" ){ $version = "?"; }
    return array(
      'userAgent' => $u_agent,
      'name'      => $bname,
      'version'   => $version,
      'platform'  => $platform,
      'pattern'   => $pattern
    );
  } 

  private function rrmdir($dir) {
    foreach(glob($dir . '/*') as $file){
      if(is_dir($file)){
        $this->rrmdir($file); 
      }else{
        unlink($file); 
      } 
    }
    rmdir($dir);
  }
  
}
?>
