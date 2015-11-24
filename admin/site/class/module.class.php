<?php
class Module{
  
  private $id = 0;
  private $name = '';
  private $active = false;
  private $folder = '';
  private $backend = '';
  private $frontend = '';
  private $shortcut = false;
  private $url = '';
  private $order = 0;
  private $datalink = null;
  
/*
 * CONSTRUCTOR
 */
 
  public function __construct( $datalink, $module = 0 ){  //We can load by either id or url
    $this->datalink = $datalink;
    if( is_numeric( $module ) ){
      $query = 'select id,
      name,
      active,
      folder,
      backend,
      frontend,
      shortcut,
      url,
      corder
      from int_admin
      where id="'.$module.'"
      limit 1';
    }else{
      $query = 'select id,
      name,
      active,
      folder,
      backend,
      frontend,
      shortcut,
      url,
      corder
      from int_admin
      where url="'.$module.'"
      limit 1';
    }
    $result = $this->datalink->dbQuery( $query, 'result' );
    if( isset( $result[0] ) ){
      $this->id = $result[0][0];
      $this->name = $result[0][1];
      $this->active = $result[0][2];
      $this->folder = $result[0][3];
      $this->backend = $result[0][4];
      $this->frontend = $result[0][5];
      $this->shortcut = $result[0][6];
      $this->url = $result[0][7];
      $this->order = $result[0][8];
      $this->loadLanguage( $_SESSION['lang'] );
    }
  }
  
/*
 * GETTERS AND SETTERS
 */
  public function getId(){
    return $this->id;
  }

  public function getName(){
    return $this->name;
  }
  public function setName( $string ){
    $this->name = $string;
  }

  public function getActive(){
    return $this->active;
  }
  public function setActive( $boolean ){
    $this->active = $boolean;
  }

  public function getFolder(){
    return $this->folder;
  }
  public function setFolder( $string ){
    $this->folder = $string;
  }
  
  public function getBackend(){
    return $this->backend;
  }
  public function setBackend( $string ){
    $this->backend = $string;
  }
  
  public function getFrontend(){
    return $this->frontend;
  }
  public function setFrontend( $string ){
    $this->frontend = $string;
  }
  
  public function getShortcut(){
    return $this->shortcut;
  }
  public function setShortcut( $boolean ){
    $this->shortcut = $boolean;
  }
  
  public function getUrl(){
    return $this->url;
  }
  public function setUrl( $string ){
    $this->string = $string;
  }
  
  public function getOrder(){
    return $this->order;
  }
  public function setOrder( $integer ){
    $this->order = $integer;
  }
  
/*
 * PUBLIC METHODS
 */
 
  public function loadLanguage( $language ){
    $result = $this->datalink->dbQuery( 'select name from int_admin_trad where id="'.$this->id.'" and lang="'.$language.'" limit 1', 'result' );
    if( isset( $result[0] ) ){
      $this->name = $result[0][0];
    }
  }
  
  public function getLangfile(){
    $requested = 'admin/'.$this->folder.'/lang/'.$_SESSION['lang'].'.php';
    if( file_exists( $requested ) ){
      $file = $requested;
    }else{
      $file = 'admin/'.$this->folder.'/lang/en.php';
    }
    return $file;
  }

  public function saveConfig(){
    $errors = '';
    if( $this->id > 3 ){
      $query = 'update int_admin set name="'.$this->name.'", url="'.$this->url.'", shortcut="'.$this->shortcut.'", corder="'.$this->order.'", active="'.$this->active.'" where id="'.$this->id.'"';
      if( $this->datalink->dbQuery( $query, 'query' ) <= 0 ){
        $errors .= Module_configuration_not_saved;
      }
    }
    if( $this->isTranslated( $_SESSION['lang'] ) ){
      $query = 'update int_admin_trad set name="'.$this->name.'" where lang="'.$_SESSION['lang'].'" and id="'.$this->id.'"';
      $this->datalink->dbQuery( $query, 'query' );
    }else{
      $query = 'insert into int_admin_trad (id, lang, name) values ("'.$this->id.'", "'.$_SESSION['lang'].'", "'.$this->name.'")';
      if( $this->datalink->dbQuery( $query, 'query' ) <= 0 ){
        $errors .= Module_not_translated;
      }
    }
    if( $errors != '' ){
      return '<p class="error">'.$errors.'</p>';
    }else{
      return '<p class="fine">'.Module_data_saved.'</p>';
    }
  }
  
  public function moveUp(){
    $query = 'select id, corder
    from int_admin
    where corder<'.$this->order.'
    and active=1
    order by corder desc
    limit 1';
    $result = $this->datalink->dbQuery( $query, 'result' );
    if( isset( $result[0] ) ){
      $this->datalink->dbQuery( 'update int_admin set corder='.$this->order.' where id='.$result[0][0], 'query' );
      $this->order = $result[0][1];
      $this->datalink->dbQuery( 'update int_admin set corder='.$this->order.' where id='.$this->id, 'query' );
    }
  }

  public function moveDown(){
    $query = 'select id, corder
    from int_admin
    where corder>'.$this->order.'
    and active=1
    order by corder asc
    limit 1';
    $result = $this->datalink->dbQuery( $query, 'result' );
    if( isset( $result[0] ) ){
      $this->datalink->dbQuery( 'update int_admin set corder='.$this->order.' where id='.$result[0][0], 'query' );
      $this->order = $result[0][1];
      $this->datalink->dbQuery( 'update int_admin set corder='.$this->order.' where id='.$this->id, 'query' );
    }
  }

/*
 * PRIVATE METHODS
 */
 
  private function isTranslated( $lang ){
    $query = 'select name from int_admin_trad where lang="'.$lang.'" and id="'.$this->id.'"';
    return( $this->datalink->dbQuery( $query, 'rows' ) > 0 );
  }
  
}
?>