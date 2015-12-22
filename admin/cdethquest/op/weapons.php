<?php
if( isset( $_POST['pag'] ) ){
  $pag = $_POST['pag'];
}else{
  $pag = 0;
}
if( isset( $_POST['filter'] ) ){
  $filter = $_POST['filter'];
}else{
  $filter = '';
}
$regspag = 20;
?>
<p><?php echo HelpWeapons; ?></p>  
<p><?php echo Active_language; ?> <b><?php echo $site->activeLanguage(); ?></b></p>  
<?php
if (!isset($_POST['item'])){
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
    <input type="hidden" name="item" value="0">
    <p class="pinput"><input type="submit" value="<?php echo Add_item; ?>"></p>
  </form>  
  <div class="editiontitle"><?php echo Weapons; ?></div>  
  <?php
  $query = 'select id from '.mod.'deth_item_weapon where name like "%'.$filter.'%" or type like "%'.$filter.'%" order by type asc, price asc, premium asc, name asc';
  $rows = $site->dbQuery( $query, 'rows' );
  $numpags = ceil( $rows / $regspag );
  $result = $site->dbQuery( $query, 'result', ( $regspag * $pag ) );
  $i = 0;
  if( $rows > 0 ){
    foreach( $result as $row ){
      if( $i < $regspag ){
        $item = new Weapon( $site->getDatalink(), $row[0] );
        echo '<form name="item'.$item->getId().'" onsubmit="event.preventDefault();backend.post(this);" method="post" action="">
        <div class="editionitem">
        <input type="hidden" name="pag" value="'.$pag.'">
        <input type="hidden" name="filter" value="'.$filter.'">
        <input type="hidden" name="item" value="'.$item->getId().'">
        <div class="field"><b>'.$item->getName().'</b></div>
        <div class="field">';
        if ($item->getIcon() != ''){
          echo '<img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$item->getIcon().'">';
        }
        echo '</div>
        <div class="field">'.$item->getType().'<br>'.$item->getHands().' '.Item_hands.'</div>
        <div class="field"><b>'.$item->getPrice().'</b>'.Coins.'<br><b>'.$item->getPremium().'</b>'.Premium_coins.'</div>
        <div class="field"><input type="submit" value="'.Edit.'"></div>
        </div>
        </form>';
      }
      $i++;
    }
    echo '<div class="pags">';
    $dots = false;
    for( $a = 0; $a < $numpags; $a++ ){
      if( $a > $numpags - 2
      or $a < 3
      or( $a > $pag - 5 and $a < $pag + 5 ) ){
        echo '<form name="pag'.$a.'" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
        <input type="hidden" name="pag" value="'.$a.'">
        <input type="hidden" name="filter" value="'.$filter.'">
        <input type="submit" value="';
        if( $a == $pag ){ echo '['.$a.']'; }else{ echo $a; }
        echo '">
        </form>';
        $dots = false;
      }else{
        if( !$dots ){
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
  $item = new Weapon( $site->getDatalink(), $_POST['item'] );
  if( isset( $_POST['name'] )
  and isset( $_POST['price'] )
  and isset( $_POST['premium'] )
  and isset( $_POST['type'] )
  and isset( $_POST['description'] )
  and isset( $_POST['attacks'] )
  and isset( $_POST['range'] )
  and isset( $_POST['impact'] )
  and isset( $_POST['damage'] )
  and isset( $_POST['piercing'] )
  and isset( $_POST['clipsize'] )
  and isset( $_POST['hands'] ) ){
    $item->setName( $_POST['name'] );
    $item->setPrice( $_POST['price'] );
    $item->setPremium( $_POST['premium'] );
    $item->setType( $_POST['type'] );
    if( isset( $_POST['close'] ) ){
      $item->setClose( true );
    }else{
      $item->setClose( false );
    }
    if( isset( $_POST['forsale'] ) ){
      $item->setForsale( true );
    }else{
      $item->setForsale( false );
    }
    $item->setDescription( $_POST['description'] );
    $item->setAttacks( $_POST['attacks'] );
    $item->setRange( $_POST['range'] );
    $item->setImpact( $_POST['impact'] );
    $item->setDamage( $_POST['damage'] );
    $item->setPiercing( $_POST['piercing'] );
    $item->setClipsize( $_POST['clipsize'] );
    $item->setHands($_POST['hands']);
    if( isset( $_FILES['icon'] ) ){
      $item->setIcon( $site->imageUpload( $_FILES['icon'], $module->getFolder() ) );
    }
    if( isset( $_FILES['maleimage'] ) ){
      $item->setMaleimage( $site->imageUpload( $_FILES['maleimage'], $module->getFolder() ) );
    }
    if( isset( $_FILES['femaleimage'])){
      $item->setFemaleimage($site->imageUpload($_FILES['femaleimage'], $module->getFolder()));
    }
    if( isset( $_POST['deleteicon'] ) 
    or isset( $_POST['delete'] ) ){
      if( file_exists( $site->getRoot().'uploads/'.$module->getFolder().'/'.$item->getIcon() ) ){
        unlink( $site->getRoot().'uploads/'.$module->getFolder().'/'.$item->getIcon() );
      }
      $item->setIcon( '' );
      $item->save();
    }
    if( isset( $_POST['delete'] ) ){
      echo $item->delete();
    }else{
      echo $item->save();
    }
  }
  if( isset( $_POST['deletemaleimage'] ) ){
    if( file_exists($site->getRoot().'uploads/'.$module->getFolder().'/'.$item->getMaleimage() ) ){
      unlink( $site->getRoot().'uploads/'.$module->getFolder().'/'.$item->getMaleimage() );
    }
    $item->setMaleimage( '' );
    $item->save();
  }
  if( isset( $_POST['deletefemaleimage'] ) ){
    if( file_exists( $site->getRoot().'uploads/'.$module->getFolder().'/'.$item->getFemaleimage() ) ){
      unlink( $site->getRoot().'uploads/'.$module->getFolder().'/'.$item->getFemaleimage() );
    }
    $item->setFemaleimage( '' );
    $item->save();
  }
  ?>
  <form name="addnew" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
    <input type="hidden" name="pag" value="<?php echo $pag; ?>">
    <p><input type="submit" value="<?php echo Back_to_items; ?>"></p>
  </form>
  <h1><?php echo Editing_item.' '.$item->getName(); ?></h1>
  <form name="editweapon" onsubmit="event.preventDefault(); backend.post(this);" enctype="multipart/form-data" method="post" action="">
    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
    <input type="hidden" name="pag" value="<?php echo $pag; ?>">
    <input type="hidden" name="item" value="<?php echo $item->getId(); ?>">
    <div class="edblock">
      <p class="formimg">
        <?php
        if( $item->getIcon() == '' ){
          echo Item_image.'<br>
          <input type="file" name="icon">';
        }else{
          echo Item_image.'<br>
          <img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$item->getIcon().'"><br>
          <input type="checkbox" name="deleteicon" value="1"> '.Delete_picture;
        }
        ?>
      </p>
      <p class="pinput"><?php echo Item_name; ?>:<br>
      <input type="text" name="name" value="<?php echo $item->getName(); ?>"></p>
      <p class="pinput"><?php echo Item_type; ?>:<br>
      <input type="text" name="type" id="typeinput" value="<?php echo $item->getType(); ?>"
      onkeyup="backend.autocomplete('typelist','weapon','type',this.value,'typeinput')"><br>
      <span id="typelist" onclick="this.innerHTML = ''"></span></p>
      <p class="pinput"><?php echo Item_price; ?>:<br>
      <input type="number" name="price" value="<?php echo $item->getPrice(); ?>" style="width:6em"></p>
      <p class="pinput"><?php echo Premium_price; ?>:<br>
      <input type="number" name="premium" value="<?php echo $item->getPremium(); ?>" style="width:6em"></p>
      <p><?php echo Item_description; ?>:<br>
      <textarea name="description"><?php echo $item->getDescription(); ?></textarea></p>
    </div>
    <div class="edblock">
      <p class="pinput"><?php echo Item_attacks; ?>:<br>
      <input type="number" name="attacks" value="<?php echo $item->getAttacks(); ?>" style="width:6em"></p>
      <p class="pinput"><?php echo Item_range; ?>:<br>
      <input type="number" name="range" value="<?php echo $item->getRange(); ?>" style="width:6em"></p>
      <p class="pinput"><?php echo Item_impact; ?>:<br>
      <input type="number" name="impact" value="<?php echo $item->getImpact(); ?>" style="width:6em"></p>
      <p class="pinput"><?php echo Item_damage; ?>:<br>
      <input type="number" name="damage" value="<?php echo $item->getDamage(); ?>" style="width:6em"></p>
      <p class="pinput"><?php echo Item_piercing; ?>:<br>
      <input type="number" name="piercing" value="<?php echo $item->getPiercing(); ?>" style="width:6em"></p>
      <p class="pinput"><?php echo Item_clipsize; ?>:<br>
      <input type="number" name="clipsize" value="<?php echo $item->getClipsize(); ?>" style="width:6em"></p>    
      <p class="pinput"><?php echo Item_hands; ?>:<br>
      <input type="number" name="hands" value="<?php echo $item->getHands(); ?>" style="width:6em"></p>
      <p class="pinput"><input type="checkbox" name="close" value="1"<?php if( $item->getClose() ){ echo ' checked'; } ?>> <?php echo Close_combat; ?></p>
      <p class="pinput"><input type="checkbox" name="forsale" value="1"<?php if( $item->getForsale() ){ echo ' checked'; } ?>> <?php echo For_sale; ?></p>
    </div>
    <p><input type="submit" value="<?php echo Save_item; ?>">
    <input type="checkbox" name="delete" value="1"><?php echo Delete_item; ?></p>
    <?php 
    if ($item->getMaleimage() == ''){
      echo '<div class="formimg pinput">'.Item_male_image.':<br>
      <div class="equipment-preview" style="background-image:url('.$_GET['root'].'admin/'.$module->getFolder().'/css/images/generic-male.png)">
      </div><br>
      <input type="file" name="maleimage">
      </div>';
    }
    if( $item->getFemaleimage() == '' ){
      echo '<div class="formimg pinput">'.Item_female_image.':<br>
      <div class="equipment-preview" style="background-image:url('.$_GET['root'].'admin/'.$module->getFolder().'/css/images/generic-female.png)">
      </div><br>
      <input type="file" name="femaleimage">
      </div>';
    }
    ?>    
  </form>
  <?php
  if( $item->getMaleimage() != '' ){
    echo '<form name="maleimage" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="filter" value="'.$filter.'">
    <input type="hidden" name="pag" value="'.$pag.'">
    <input type="hidden" name="item" value="'.$item->getId().'">
    <input type="hidden" name="deletemaleimage" value="1">
    <div class="formimg pinput">
    <div class="equipment-preview" style="background-image:url('.$_GET['root'].'admin/'.$module->getFolder().'/css/images/generic-male.png)">
    <img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$item->getMaleimage().'">
    </div><br>
    <input type="submit" value="'.Delete_picture.'">
    </div>
    </form>';
  }
  if( $item->getFemaleimage() != '' ){
    echo '<form name="image" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="filter" value="'.$filter.'">
    <input type="hidden" name="pag" value="'.$pag.'">
    <input type="hidden" name="item" value="'.$item->getId().'">
    <input type="hidden" name="deletefemaleimage" value="1">
    <div class="formimg pinput">
    <div class="equipment-preview" style="background-image:url('.$_GET['root'].'admin/'.$module->getFolder().'/css/images/generic-female.png)">
    <img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$item->getFemaleimage().'">
    </div><br>
    <input type="submit" value="'.Delete_picture.'">
    </div>
    </form>';
  }
}
?>