<?php
Abstract class DatabaseObject {   //Manages database operations (construct, save and delete) of an object in database.

  private $table = '';          //Table affected.
  private $vars = array();      //Array with object vars, must coincide with database columns.
  protected $datalink = null;   //Datalink oject accessible in child objects.
  
/*
 * CONSTRUCTOR
 */
  
  public function __construct ( $datalink, $table, $vars, $id = 0 ){  //All objects must have "id" numeric variable.
    $this->datalink = $datalink;
    $this->table = $table;
    $this->vars = $vars;
    unset( $this->vars['datalink'] );  //Variable for database link, not part of main object.
    if ( $id != 0 ){
      $query = 'select';
      $i = 0;
      foreach( $this->vars as $k => $v ){
        $i++;
        $query .= ' '.$k;
        if( $i < count( $this->vars ) ){
          $query .= ',';
        }
      }
      $query .= ' from '.$this->table.' where id="'.$id.'" limit 1';
      $result = $this->datalink->dbQuery( $query, 'result' );
      if( isset( $result[0] ) ){
        $i = 0;
        foreach( $this->vars as $k => $v ){
          $this->$k = $result[0][$i];
          $i++;
        }
      }
    }
    unset( $this->vars['id'] );  //Delete because we do not want to list "id" when we save the object.
  }
  
/*
 * METHODS
 */
    
  public function save(){
    if ($this->id == 0){
      $query = 'insert into '.$this->table.' (';
      $i = 0;
      foreach( $this->vars as $k => $v ){
        $i++;
        $query .= $k;
        if( $i < count( $this->vars ) ){
          $query .= ', ';
        }
      }
      $query .= ') values (';
      $i = 0;
      foreach( $this->vars as $k => $v ){
        $i++;
        $query .= '"'.$this->$k.'"';
        if( $i < count( $this->vars ) ){
          $query .= ', ';
        }
      }
      $query .= ')';
    }else{
      $query = 'update '.$this->table.' set ';
      $i = 0;
      foreach( $this->vars as $k => $v ){
        $i++;
        $query .= $k.'="'.$this->$k.'"';
        if( $i < count( $this->vars ) ){
          $query .= ', ';
        }
      }      
      $query .= ' where id="'.$this->id.'"';
    }
    if ( $this->datalink->dbQuery( $query, 'query' ) > 0 ){
      $query = 'select id from '.$this->table.' 
      order by id desc limit 1';
      $result = $this->datalink->dbQuery($query, 'result');
      if ( $this->id == 0
      and isset( $result[0] ) ){
        $this->id = $result[0][0];
      }
      return true;
    }else{
      return false;
    }
  }
  
  public function delete(){
    $query = 'delete from '.$this->table.'
    where id="'.$this->id.'"';
    if ($this->datalink->dbQuery($query, 'query') > 0){
      $this->id = 0;
      foreach( $this->vars as $k => $v ){
        $this->$k = null;
      }
      return true;
    }else{
      return false;
    }
  }
  
  protected function dbQuery( $query, $type = 'result' ){
    return $this->datalink->dbQuery( $query, $type );
  }
  
}
?>