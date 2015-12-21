<?php
class Content {
  
  private $id = 0;
  private $url = '';
  private $title = '';
  private $code = '';
  private $menu = 1;
  private $father = 0;
  private $corder = 0;
  private $datalink = null;

/*
 * CONSTRUCTOR
 */
 
  public function __construct($datalink, $cont = 0){
    $this->datalink = $datalink;
    $query = 'select id,
    url,
    title,
    code,
    menu,
    father,
    corder
    from int_content';
    if (is_numeric($cont)){
      $query .= ' where id="'.$cont.'" limit 1';
    }else{
      $query .= ' where url="'.$cont.'" limit 1';
    }
    $result = $this->datalink->dbQuery($query, 'result');
    if (isset($result[0])){
      $this->id = $result[0][0];
      $this->url = $result[0][1];
      $this->title = $result[0][2];
      $this->code = $result[0][3];
      $this->menu = $result[0][4];
      $this->father = $result[0][5];
      $this->corder = $result[0][6];
      $this->loadLanguage($_SESSION['lang']);
    }
  }
  
/*
 * GETTERS
 */
 
  public function getId(){
    return $this->id;
  }

  public function getUrl(){
    return $this->url;
  }
  
  public function getTitle(){
    return $this->title;
  }
  
  public function getCode(){
    return $this->code;
  }
  
  public function getMenu(){
    return $this->menu;
  }
  
  public function getFather(){
    return $this->father;
  }
  
  public function getCorder(){
    return $this->corder;
  }
  
/*
 * SETTERS
 */
  
  public function setUrl($url){
    $this->url = $url;
  }
  
  public function setTitle($title){
    $this->title = $title;
  }
  
  public function setCode($code){
    $this->code = $code;
  }
  
  public function setMenu($menu){
    $this->menu = $menu;
  }
  
  public function setFather($father){
    $this->father = $father;
  }
  
  public function setCorder($corder){
    $this->corder = $corder;
  }
  
/*
 * PUBLIC METHODS
 */
  
  public function loadLanguage($language){
    $query = 'select title, code from int_content_trad where id="'.$this->id.'" and lang="'.$language.'" limit 1';
    $result = $this->datalink->dbQuery($query, 'result');
    if (isset($result[0])){
      if ($result[0][0] != ''){
        $this->title = $result[0][0];
      }
      if ($result[0][1] != ''){
        $this->code = $result[0][1];
      }
    }
  }
  
  public function save(){
    if ($this->url == ''){
      $this->url = 'home';
    }
    if ($this->corder == 0){
      $result = $this->datalink->dbQuery('select corder from int_content where id!="'.$this->id.'" order by corder desc limit 1', 'result');
      if (isset($result[0])){
        $this->corder = $result[0][0] + 1;
      }else{
        $this->corder = $this->corder;
      }
    }
    if ($this->id == 0){
      $query = 'insert into int_content (
      url,
      title,
      code,
      menu,
      father,
      corder
      ) values (
      "'.$this->url.'",
      "'.$this->title.'",
      "'.$this->code.'",
      "'.$this->menu.'",
      "'.$this->father.'",
      "'.$this->corder.'"
      )';
    }else{
      $query = 'update int_content set
      url="'.$this->url.'",
      title="'.$this->title.'",
      code="'.$this->code.'",
      menu="'.$this->menu.'",
      father="'.$this->father.'",
      corder="'.$this->corder.'"
      where id="'.$this->id.'"';
    }
    if ($this->datalink->dbQuery($query, 'query') > 0){
      if ($this->id == 0){
        $query = 'select id from int_content order by id desc limit 1';
        $result = $this->datalink->dbQuery($query, 'result');
        if (isset($result[0])
        and $row = $result[0]){
          $this->id = $row[0];
        }
      }
      if ($this->isTranslated($_SESSION['lang'])){
        $query = 'update int_content_trad set 
        title="'.$this->title.'", 
        code="'.$this->code.'"
        where id="'.$this->id.'" and lang="'.$_SESSION['lang'].'"';
      }else{
        $query = 'insert into int_content_trad (
        id,
        lang,
        title,
        code
        ) values (
        "'.$this->id.'", 
        "'.$_SESSION['lang'].'", 
        "'.$this->title.'", 
        "'.$this->code.'"
        )';
      }
      $this->datalink->dbQuery($query, 'query');
      return '<p class="fine">'.Content_saved.'</p>';
    }else{
      return '';
    }
  }

  public function delete() {
    $query = 'delete from int_content where id="'.$this->id.'"';
    if ($this->datalink->dbQuery($query, 'query') > 0){
      $this->id = 0;
      $this->url = '';
      $this->title = '';
      $this->code = '';
      $this->menu = 0;
      $this->father = 0;
      $this->corder = 0;
      return '<p class="fine">'.Content_deleted.'</p>';
    }else{
      return '<p class="error">'.Content_not_deleted.'</p>';
    }
  }
  
  public function show() {
    echo str_replace ("src='uploads", "src='../uploads", $this->code);
  }
  
  public function isTranslated($lang){
    $query = 'select id from int_content_trad where lang="'.$lang.'" and id="'.$this->id.'"';
    return ($this->datalink->dbQuery($query, 'rows') > 0);
  }
  
  public function hasChildren(){
    return ($this->datalink->dbQuery('select id from int_content where father="'.$this->id.'" limit 1', 'rows') > 0);
  }
  
  public function isFatherOf($child){
    return ($this->datalink->dbQuery('select id from int_content where father="'.$this->id.'" and (url="'.$child.'" or id="'.$child.'")', 'rows') > 0);
  }

  public function moveUp(){
    $query = 'select id, corder
    from int_content
    where corder<'.$this->corder.'
    order by corder desc
    limit 1';
    $result = $this->datalink->dbQuery($query, 'result');
    if (isset($result[0])){
      $this->datalink->dbQuery('update int_content set corder='.$this->corder.' where id='.$result[0][0], 'query');
      $this->corder = $result[0][1];
      $this->save();
    }
  }

  public function moveDown(){
    $query = 'select id, corder
    from int_content
    where corder>'.$this->corder.'
    order by corder asc
    limit 1';
    $result = $this->datalink->dbQuery($query, 'result');
    if (isset($result[0])){
      $this->datalink->dbQuery('update int_content set corder='.$this->corder.' where id='.$result[0][0], 'query');
      $this->corder = $result[0][1];
      $this->save();
    }
  }
  
/*
 * PRIVATE METHODS
 */

}
?>