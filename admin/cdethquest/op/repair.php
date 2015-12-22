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
<p><?php echo HelpRepairing; ?></p>
<p><?php echo Active_language; ?> <b><?php echo $site->activeLanguage(); ?></b></p>
<?php
if( !isset( $_POST['item'] ) ){
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
  <div class="editiontitle"><?php echo Repairing_items; ?></div>
  <?php
  $query = 'select id from '.mod.'deth_item_repairing where name like "%'.$filter.'%" order by price asc, premium asc, name asc';
  $rows = $site->dbQuery( $query, 'rows' );
  $numpags = ceil( $rows / $regspag );
  $result = $site->dbQuery( $query, 'result', ( $regspag * $pag ) );
  $i = 0;
  if( $rows > 0 ){
    foreach( $result as $row ){
      if( $i < $regspag ){
        $item = new RepairingItem( $site->getDatalink(), $row[0] );
        echo '<form name="item'.$item->getId().'" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
        <div class="editionitem">
        <input type="hidden" name="pag" value="'.$pag.'">
        <input type="hidden" name="filter" value="'.$filter.'">
        <input type="hidden" name="item" value="'.$item->getId().'">
        <div class="field"><input type="submit" value="'.$item->getName().'"></div>
        <div class="field">';
        if ($item->getIcon() != ''){
          echo '<img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$item->getIcon().'">';
        }
        echo '</div>
        <div class="field"><b>'.$item->getPrice().'</b>'.Coins.'<br><b>'.$item->getPremium().'</b>'.Premium_coins.'</div>
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
  $item = new RepairingItem( $site->getDatalink(), $_POST['item'] );
  if( isset( $_POST['name'] )
  and isset( $_POST['price'] )
  and isset( $_POST['premium'] )
  and isset( $_POST['description'] )
  and isset( $_POST['armor'] ) ){
    $item->setName( $_POST['name'] );
    $item->setPrice( $_POST['price'] );
    $item->setPremium( $_POST['premium'] );
    $item->setDescription( $_POST['description'] );
    $item->setArmor( $_POST['armor'] );
    if( isset( $_FILES['image'] ) ){
      $item->setIcon( $site->imageUpload( $_FILES['image'], $module->getFolder() ) );
    }
    if( isset( $_POST['forsale'] ) ){
      $item->setForsale( true );
    }else{
      $item->setForsale( false );
    }
    if( isset( $_POST['deleteimage'] ) 
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
  ?>
  <form name="addnew" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
    <input type="hidden" name="pag" value="<?php echo $pag; ?>">
    <p><input type="submit" value="<?php echo Back_to_items; ?>"></p>
  </form>
  <h1><?php echo Editing_item.' '.$item->getName(); ?></h1>
  <form name="edit" onsubmit="event.preventDefault(); backend.post(this);" enctype="multipart/form-data" method="post" action="">
    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
    <input type="hidden" name="pag" value="<?php echo $pag; ?>">
    <input type="hidden" name="item" value="<?php echo $item->getId(); ?>">
    <div class="edblock">
      <p class="formimg">
        <?php
        if( $item->getIcon() == '' ){
          echo Item_image.'<br>
          <input type="file" name="image">';
        }else{
          echo Item_image.'<br>
          <img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$item->getIcon().'"><br>
          <input type="checkbox" name="deleteimage" value="1"> '.Delete_picture;
        }
        ?>
      </p>
      <p class="pinput"><?php echo Item_name; ?>:<br>
      <input type="text" name="name" value="<?php echo $item->getName(); ?>"></p>
      <p class="pinput"><?php echo Armor_amount; ?>:<br>
      <input type="number" name="armor" value="<?php echo $item->getArmor(); ?>" style="width:6em"></p>
      <p class="pinput"><?php echo Item_price; ?>:<br>
      <input type="number" name="price" value="<?php echo $item->getPrice(); ?>" style="width:6em"></p>
      <p class="pinput"><?php echo Premium_price; ?>:<br>
      <input type="number" name="premium" value="<?php echo $item->getPremium(); ?>" style="width:6em"></p>
      <p class="pinput"><input type="checkbox" name="forsale" value="1"<?php if( $item->getForsale() ){ echo ' checked'; } ?>> <?php echo For_sale; ?></p>
    </div>
    <div class="edblock">
      <p><?php echo Item_description; ?>:<br>
      <textarea name="description"><?php echo $item->getDescription(); ?></textarea></p>
    </div>
    <p><input type="submit" value="<?php echo Save_item; ?>">
    <input type="checkbox" name="delete" value="1"><?php echo Delete_item; ?></p>
  </form>
  <?php
}
?>