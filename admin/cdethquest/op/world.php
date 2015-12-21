<?php
if (isset($_POST['pag'])){
  $pag = $_POST['pag'];
}else{
  $pag = 0;
}
if (isset($_POST['filter'])){
  $filter = $_POST['filter'];
}else{
  $filter = '';
}
$regspag = 20;
?>
  <h1><?php echo Places; ?></h1>
  <p><?php echo HelpPlaces; ?></p>
  <p><?php echo Active_language; ?> <b><?php echo $site->activeLanguage(); ?></b></p>
<?php
if (!isset($_POST['place'])){
?>
  <form name="usersearch" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="pag" value="<?php echo $pag; ?>">
    <p class="pinput"><?php echo Search_by_name; ?><br>
    <input type="text" name="filter" value="<?php echo $filter; ?>">
    <input type="submit" value="<?php echo Search; ?>"></p>
  </form>
  <form name="addnew" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
    <input type="hidden" name="pag" value="<?php echo $pag; ?>">
    <input type="hidden" name="place" value="0">
    <p class="pinput"><input type="submit" value="<?php echo Add_place; ?>"></p>
  </form>
  <div class="editiontitle"><?php echo Places; ?></div>
  <?php
  $query = 'select id from '.mod.'deth_places where name like "%'.$filter.'%" order by name asc';
  $rows = $site->getDatalink()->dbQuery($query, 'rows');
  $numpags = ceil ($rows / $regspag);
  $result = $site->getDatalink()->dbQuery($query, 'result', ($regspag * $pag));
  $i = 0;
  if ($rows > 0){
    foreach ($result as $row){
      if ($i < $regspag){
        $place = new Place( $site->getDatalink(), $row[0] );
        echo '<form name="place'.$place->getId().'" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
        <div class="editionitem">
        <input type="hidden" name="pag" value="'.$pag.'">
        <input type="hidden" name="filter" value="'.$filter.'">
        <input type="hidden" name="place" value="'.$place->getId().'">
        <div class="field"><input type="submit" value="'.$place->getName().'"></div>
        <div class="field">';
        if ( $place->getImage() != '' ){
          echo '<img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$place->getImage().'">';
        }
        echo '</div>
        </div>
        </form>';
      }
      $i++;
    }
    echo '<div class="pags">';
    $dots = false;
    for ($a = 0; $a < $numpags; $a++){
      if ($a > $numpags - 2
      or $a < 3
      or ($a > $pag - 5 and $a < $pag + 5)){
        echo '<form name="pag'.$a.'" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
        <input type="hidden" name="pag" value="'.$a.'">
        <input type="hidden" name="filter" value="'.$filter.'">
        <input type="submit" value="';
        if ($a == $pag){ echo '['.$a.']'; }else{ echo $a; }
        echo '">
        </form>';
        $dots = false;
      }else{
        if (!$dots){
          echo ' ... ';
          $dots = true;
        }
      }
    }
    echo '</div>';
  }else{
    echo '<p class="error">'.No_data_to_show.'</p>';
  }
}else{
  $place = new Place( $site->getDatalink(), $_POST['place'] );
  if( isset( $_POST['name']) 
  and isset( $_POST['description'] )){
    $place->setName( $_POST['name'] );
    $place->setDescription( $_POST['description'] );
    if(isset($_FILES['image'])){
      $place->setImage( $site->imageUpload( $_FILES['image'], $module->getFolder() ) );
    }
    if (isset($_POST['delete'])){
      echo $place->delete();
    }else{
      echo $place->save();
    }
  }
  if( isset($_POST['deleteimage'] ) ){
    if( file_exists( $site->getRoot().'uploads/'.$module->getFolder().'/'.$place->getImage() ) ){
      unlink( $site->getRoot().'uploads/'.$module->getFolder().'/'.$place->getImage() );
    }
    $place->setImage( '' );
    $place->save();
  }
  if( isset( $_FILES['texture'] ) 
  and isset( $_POST['type'] ) ){
    $square = new Square( $site->getDatalink() );
    $square->setPlace( $place->getId() );
    $square->setType( $_POST['type'] );
    
    $square->setTexture( $site->imageUpload( $_FILES['texture'], $module->getFolder() ) );
    echo $square->save();
  }
  if( isset( $_POST['delsquare'] ) ){
    $square = new Square( $site->getDatalink(), $_POST['delsquare'] );
    if( file_exists( $site->getRoot().'uploads/'.$module->getFolder().'/'.$square->getTexture() ) ){
      unlink( $site->getRoot().'uploads/'.$module->getFolder().'/'.$square->getTexture() );
    }
    echo $square->delete();
  }
?>
  <form name="addnew" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
    <input type="hidden" name="pag" value="<?php echo $pag; ?>">
    <p><input type="submit" value="<?php echo Back; ?>"></p>
  </form>
  <h1><?php echo Editing_place.' '.$place->getName(); ?></h1>
  <form name="editrace" onsubmit="event.preventDefault(); backend.post(this);" enctype="multipart/form-data" method="post" action="">
    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
    <input type="hidden" name="pag" value="<?php echo $pag; ?>">
    <input type="hidden" name="place" value="<?php echo $place->getId(); ?>">
    <p class="pinput"><?php echo Place_name; ?><br>
    <input type="text" name="name" value="<?php echo $place->getName(); ?>"></p>    
    <p><?php echo Place_description; ?><br>
    <textarea name="description"><?php echo $place->getDescription(); ?></textarea></p>    
    <p><input type="submit" value="<?php echo Save_place; ?>">
    <input type="checkbox" name="delete" value="1"><?php echo Delete_place; ?></p>
    <?php 
    if ($place->getImage() == ''){
      echo '<div class="formimg">'.Place_image.'<br>
      <input type="file" name="image"></div>';
    }
    ?>
  </form>
<?php
  if ($place->getImage() != ''){
    echo '<form name="image" onsubmit="event.preventDefault(); backend.post(this,false);" method="post" action="">
    <input type="hidden" name="filter" value="'.$filter.'">
    <input type="hidden" name="pag" value="'.$pag.'">
    <input type="hidden" name="place" value="'.$place->getId().'">
    <input type="hidden" name="deleteimage" value="1">
    <div class="formimg">
    <img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$place->getImage().'"><br>
    <input type="submit" value="'.Delete_picture.'">
    </div>
    </form>';
  }  
  if( $place->getId() == 0  ){
    echo '<p>'.Save_place_to_edit_textures.'</p>';
  }else{
    echo '<h2>'.Place_textures.'</h2>';
    $types = Array( 'floor', 'wall', 'pit', 'door', 'sprite' );
    foreach( $types as $type ){
      echo '<div class="placesquares">';
      switch( $type ){
        case 'floor':
          echo '<div class="editiontitle">'.Floor.'</div>';
        break;
        case'wall':
          echo '<div class="editiontitle">'.Wall.'</div>';
        break;
        case 'pit':
          echo '<div class="editiontitle">'.Pit.'</div>';
        break;
        case 'door':
          echo '<div class="editiontitle">'.Door.'</div>';
        break;
        case 'sprite':
          echo '<div class="editiontitle">'.Sprite.'</div>';
        break;
      }
      echo '<div class="place-texture">
      <form name="addtexture'.$type.'" onsubmit="event.preventDefault(); backend.post(this,false);" method="post" action="">
      <input type="hidden" name="filter" value="'.$filter.'">
      <input type="hidden" name="pag" value="'.$pag.'">
      <input type="hidden" name="place" value="'.$place->getId().'">
      <input type="hidden" name="type" value="'.$type.'">
      <input type="file" name="texture">
      <input type="submit" value="'.Upload.'">
      </form>
      </div><br>';
      $result = $site->getDatalink()->dbQuery( 'select id from '.mod.'deth_squares where place="'.$place->getId().'" and type="'.$type.'"', 'result' );
      foreach( $result as $row ){
        $square = new Square( $site->getDatalink(), $row[0] );
        echo '<div class="place-texture">
        <form name="deltexture'.$type.'" onsubmit="event.preventDefault(); backend.post(this,false);" method="post" action="">
        <input type="hidden" name="filter" value="'.$filter.'">
        <input type="hidden" name="pag" value="'.$pag.'">
        <input type="hidden" name="place" value="'.$place->getId().'">
        <input type="hidden" name="delsquare" value="'.$square->getId().'">
        <img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$square->getTexture().'"><br>
        <input type="submit" value="X">
        </form>
        </div>';
      }
      echo '</div>';
    }
  }
}
?>