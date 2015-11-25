<?php
class Ajaxsite {
  
  private $browser = '?';
  private $datalink = null;
  private $debug = false;
  private $modfiles = 0755;
  private $root = '';
  private $title = 'My new website';
  
/*
 * CONSTRUCTOR
 */
 
  public function __construct($datalink, $debug = false){
    $this->datalink = $datalink;
    $this->debug = $debug;
    $browser = $this->checkBrowser();
    $this->browser = $browser['name'].' '.$browser['version'].' ('.$browser['platform'].')';
    $query = 'select title
    from int_information
    limit 1';
    $result = $this->datalink->dbQuery( $query, 'result' );
    if( isset( $result[0] ) ){
      $this->title = $result[0][0];
    }
    register_shutdown_function( 'shutdown', $this );
  }
  
/*
 * GETTERS AND SETTERS
 */
 
  public function getDatalink(){
    return $this->datalink;
  }
  
  public function getRoot(){
    return $this->root;
  }
  public function setRoot( $string ){
    $this->root = $string;
  }
  
  public function getDebug(){
    return $this->debug;
  }
  public function setDebug( $boolean ){
    $this->debug = $boolean;
  }
  
  public function getTitle(){
    return $this->title;
  }
  
/*
 * PUBLIC METHODS
 */
 
  public function inputCheck(){  //Processes post and get variables
    $log = '';
    if( $this->debug ){
      echo "<script>console.log('AJAX INPUT:')</script>";
    }
    foreach( $_POST as $key => $value ){
      switch( $key ){
        case 'code':
          $_POST[$key] = strip_tags( $_POST[$key],'<p><a><h1><h2><h3><h4><table><tr><th><td><div><ul><ol><li><sub><sup><img><iframe><script><strong><b><br><embed><object><param><span><em>' ); 
          $_POST[$key] = str_replace( '"', "'", $_POST[$key] ); //HTML code
        break;
        default:
          $_POST[$key] = strip_tags( $_POST[$key] );
          $_POST[$key] = str_replace('"', '&#34;', $_POST[$key] );
          $_POST[$key] = str_replace("'", '&#39;', $_POST[$key] );
      }
      $value = $_POST[$key];
      if( $this->debug
      and $key != 'code' ){
        echo "<script>console.log('POST: ".$key." => ".str_replace( Array( "\n", "\r" ), " ", $value )."')</script>";
      }
      if( $key == 'password' ){
        $value = '****';
      }
      $log .= 'POST['.$key.'] => '.$value."\r\n";
    }
    foreach( $_GET as $key => $value ){
      $_GET[$key] = str_replace( ' ', '_', $_GET[$key] );
      $_GET[$key] = str_replace( array( '"', "'", ",", ";", "=" ), '', $_GET[$key] );
      if( $this->debug ){
        echo "<script>console.log('GET: ".$key." => ".$_GET[$key]."')</script>";
      }
      $log .= 'GET['.$key.'] => '.$value."\r\n";
    }
    if( isset( $_FILES ) ){
      foreach( $_FILES as $key => $value ){
        if( $this->debug ){
          echo "<script>console.log('FILES: ".$key."')</script>";
        }
        $log .= 'FILES['.$key.'] => '.var_export( $value, true )."\r\n";
      }
    }
    if( isset( $_POST['lang'] ) ){
      $_SESSION['lang'] = $_POST['lang'];
    }
    if( $this->debug ){
      foreach( $_SESSION as $k => $v ){
        if( !is_array( $v ) ){
          echo "
          <script>console.log('SESSION: ".$k." => ".$v."')</script>";
        }
      }
    }
    if( count( $_POST ) > 0 ){
      $this->addLog( $log );
    }
  }
 
  public function addLog( $data ){
    $query = 'delete from int_overwatch where instant<'.( time() - (86400 * 180) );  //Logging since half a year ago
    $this->datalink->dbQuery( $query, 'query' );
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
    "ajax",
    "'.$this->browser.'",
    "'.$_SERVER['REMOTE_ADDR'].'",
    "'.$_SESSION['username'].'",
    "'.$data.'"
    )';
    $this->datalink->dbQuery( $query, 'query' );
  }
  
  public function activeLanguage(){
    $query = 'select name
    from int_lang
    where lang="'.$_SESSION['lang'].'"';
    $result = $this->datalink->dbQuery( $query, 'result' );
    if( isset( $result[0] ) ){
      return $result[0][0];
    }else{
      return '?';
    }
  }
  
  public function imageUpload( $file, $dir ) {
    $ret = '';
    $mwidth = 1024;  //Maximum size of uploaded pictures.
    $mheight = 768;
    $imagename = str_replace( array( ' ', "'" ), '_', $file['name'] ); //Store relevant information
    if( $file['error'] > 0 ) {
      switch( $file['error'] ){
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
      $ext = pathinfo( $file['name'], PATHINFO_EXTENSION );
      switch( $ext ){
        case 'jpg':
        case 'JPG':
        case 'jpeg':
        case 'JPEG':
        case 'png':
        case 'PNG':
        case 'gif':
        case 'GIF':
          $image = new Image( $file['tmp_name'] );
          $imageinfo = getimagesize( $file['tmp_name'] );
          if( $imageinfo[0] > $imageinfo[1] 
          and $imageinfo[0] > $mwidth ){
            $image->resizeToWidth( $mwidth );  //Picture width
          }
          if( $imageinfo[1] > $imageinfo[0] 
          and $imageinfo[1] > $mheight ){
            $image->resizeToHeight( $mheight );  //Picture height
          }
          $validate = false;
          while( !$validate ){
            if( file_exists( $this->root.'uploads/'.$dir.'/'.$imagename ) ){
              $imagename = '-'.$imagename;
            }else{
              $validate = true;
            }
          }
          if( $image->save( $this->root.'uploads/'.$dir.'/'.$imagename ) ){
            $ret = $imagename;
            chmod( $this->root.'uploads/'.$dir.'/'.$imagename, $this->modfiles );  //Uploading done
          }
        break;
      }
    }
    return $ret;
  }

  public function fileUpload( $file, $dir ) {
    $ret = '';
    $name = str_replace( array( ' ', "'" ), '_', $file['name'] ); //Store relevant information
    if( $file['error'] > 0 ) {
      switch( $file['error'] ){
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
      $validate = false;
      while( !$validate ){
        if( file_exists( $this->root.'uploads/'.$dir.'/'.$name ) ){
          $name = '-'.$name;
        }else{
          $validate = true;
        }
      }
      if( move_uploaded_file( $file['tmp_name'], $this->root.'uploads/'.$dir.'/'.$name ) ){
        $ret = $name;
      }
    }
    return $ret;
  }

  public function adminEmail(){
    $result = $this->datalink->dbQuery('select email from int_user where charge=1 limit 1', 'result');
    if (isset ($result[0])){
      return $result[0][0]; //Administrator Email
    }else{
      return '?';
    }
  }

  public function dirList( $directory ){
    $results = array ();  //Files and folder names
    $files = array ();    //Useful file names at the end of execution
    if( is_dir( $directory ) ){
      $results = scandir( $directory );
    }
    foreach( $results as $file ){
      if( $file != '.' 
      and $file != '..' ){ //Not significant used folder symbols
        $files[] = $file;
      }
    }
    sort( $files );
    return $files;
  }

/*
 * PRIVATE METHODS
 */
 
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
    $known = array( 'Version', $ub, 'other' );
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

}
?>