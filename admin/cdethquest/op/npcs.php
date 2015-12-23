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
<p><?php echo HelpNPCs; ?></p>
<?php
if (!isset($_POST['character'])){
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
    <input type="hidden" name="character" value="0">
    <p class="pinput">
      <select name="race">
        <?php
        $result = $site->dbQuery( 'select id from '.mod.'deth_races order by name asc' );
        foreach( $result as $row ){
          $race = new Race( $site->getDatalink(), $row[0] );
          echo '<option value="'.$race->getId().'">'.$race->getName().'</option>';
        }
        ?>
      </select>
      <input type="submit" value="<?php echo Create_npc; ?>"></p>
  </form>
  <div class="editiontitle"><?php echo NPCs; ?></div>
  <?php
  $query = 'select id from '.mod.'deth_characters where name like "%'.$filter.'%" and pc="0" order by name asc';
  $rows = $site->dbQuery( $query, 'rows' );
  $numpags = ceil( $rows / $regspag );
  $result = $site->dbQuery( $query, 'result', ( $regspag * $pag ) );
  $i = 0;
  if( $rows > 0 ){
    foreach( $result as $row ){
      if( $i < $regspag ){
        $character = new PlayerCharacter( $site->getDatalink(), $row[0] );
        $race = new Race( $site->getDatalink(), $character->getRace() );
        $class = new CharacterClass( $site->getDatalink(), $character->getClass() );
        echo '<form name="char'.$character->getId().'" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
        <div class="editionitem">
        <input type="hidden" name="pag" value="'.$pag.'">
        <input type="hidden" name="filter" value="'.$filter.'">
        <input type="hidden" name="character" value="'.$character->getId().'">
        <div class="field">
          <b>'.$character->getName().'</b><br>
          '.$race->getName().' '.$class->getName().'<br>
          '.Level.' <b>'.$character->getLevel().'</b>
        </div>
        <div class="field">
          <div class="charportrait">
            <div class="inner">'.$character->renderHtml( $module->getFolder(), true ).'</div>
          </div>
        </div>
        <div class="field">
        
        </div>
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
  $character = new PlayerCharacter( $site->getDatalink(), $_POST['character'] );
  if( isset( $_POST['race'] )
  and $character->getId() == 0 ){
    $race = new Race( $site->getDatalink(), $_POST['race'] );
    $character->setRace( $_POST['race'] );
    $character->loadRandomNPC();
    $character->save();
  }
  if( isset( $_POST['name'] )
  and isset( $_POST['health'] )
  and isset( $_POST['level'] )
  and isset( $_POST['coins'] ) 
  and isset( $_POST['premium'] ) 
  and isset( $_POST['class'] ) 
  and isset( $_POST['gender'] ) ){
    $race = new Race( $site->getDatalink(), $character->getRace() );
    $character->setGender( $_POST['gender'] );
    $character->setName( $_POST['name'] );
    $character->setHealth( $_POST['health'] );
    $character->setCoins( $_POST['coins'] );
    $character->setPremium( $_POST['premium'] );
    if( $_POST['class'] != $character->getClass() ){
      $character->setClass( $_POST['class'] );
      $character->applyClassToStats();
    }
    if( isset( $_POST['reloadbody'] ) ){
      $character->setBody( $race->randomAppearance( 'body' ) );
      $character->setHair( $race->randomAppearance( 'hair' ) );
    }
    if( isset( $_POST['reloadface'] ) ){
      $character->setFace( $race->randomAppearance( 'face' ) );
    }
    if( isset( $_POST['faceless'] ) ){
      $character->setFace( 0 );
    }
    if( isset( $_POST['rename'] ) ){
      $character->setName( $race->getRandomName( $_POST['gender'] ) );
    }
    if (isset($_POST['delete'])){
      echo $character->delete();
    }else{
      echo $character->save();
    }
  }
  if( isset( $_POST['operation'] ) 
  and isset( $_POST['invrow'] ) ){
    switch( $_POST['operation'] ){
      case 'unequip':
        echo $character->unequip( $_POST['invrow'], true );
      break;
      case 'equip':
        echo $character->equip( $_POST['invrow'] );
      break;
      case 'reload':
        echo $character->reload( $_POST['invrow'] );
      break;
      case 'deleterow':
        $line = new Inventory( $site->getDatalink(), $_POST['invrow'] );
        if( $line->getPlayercharacter() == $character->getId() ){
          $line->delete();
        } 
      break;
      case 'giveitem':
        $data = explode( '-' , $_POST['item'] );
        if( count( $data ) == 2 ){
          switch( $data[0] ){
            case 'healing':
              $item = new HealingItem( $site->getDatalink(), $data[1] );
            break;
            case 'repairing':
              $item = new RepairingItem( $site->getDatalink(), $data[1] );
            break;
            case 'weapon':
              $item = new Weapon( $site->getDatalink(), $data[1] );
            break;
            case 'armor':
              $item = new Armor( $site->getDatalink(), $data[1] );
            break;
            case 'equipment':
            default:
              $item = new Equipment( $site->getDatalink(), $data[1] );
            break;
          }
          $character->setEquipment( $data[0], $item, false );
        }
      break;
    }
  }
  $race = new Race( $site->getDatalink(), $character->getRace() );
  $class = new CharacterClass( $site->getDatalink(), $character->getClass() );
?>
  <form name="addnew" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
    <input type="hidden" name="pag" value="<?php echo $pag; ?>">
    <p><input type="submit" value="<?php echo Back_to_characters; ?>"></p>
  </form>
  <div class="charactersheet">
    <div class="stats">
      <p><?php 
        echo '<span class="desc">'.$character->getName().'</span><br>
        <b>'.$race->getName().' '.$class->getName().'</b>';
      ?></p>
      <p><?php 
        echo Level.' <span class="out">'.$character->getLevel().'</span>';
      ?></p>
      <p><?php 
        echo Wealth.': <span class="out">'.number_format( $character->getCoins(), 0, ',', '.' ).Coins.'</span>'; 
      ?></p>
      <div class="charstats">
        <div class="stat">
          <div class="name"><?php echo Health; ?></div>
          <div class="value"><?php echo $character->getHealth().' / '.$character->getMaxhealth(); ?></div>
        </div>
        <div class="stat">
          <div class="name"><?php echo Speed; ?></div>
          <div class="value"><?php echo $character->getSpeed(); ?></div>
        </div>
        <div class="stat">
          <div class="name"><?php echo Strength; ?></div>
          <div class="value"><?php echo $character->getStrength(); ?></div>
        </div>
        <div class="stat">
          <div class="name"><?php echo Dexterity; ?></div>
          <div class="value"><?php echo $character->getDexterity(); ?></div>
        </div>
        <div class="stat">
          <div class="name"><?php echo Constitution; ?></div>
          <div class="value"><?php echo $character->getConstitution(); ?></div>
        </div>
        <div class="stat">
          <div class="name"><?php echo Intelligence; ?></div>
          <div class="value"><?php echo $character->getIntelligence(); ?></div>
        </div>
      </div>
    </div>
    <div class="character">
      <?php echo $character->renderHtml( $module->getFolder() ); ?>
    </div>
    <div class="equipsheet">      
      <form name="equip" method="post" action="" onsubmit="event.preventDefault();backend.post(this, false);">
      <input type="hidden" name="filter" value="<?php echo $filter; ?>">
      <input type="hidden" name="pag" value="<?php echo $pag; ?>">
      <input type="hidden" name="character" value="<?php echo $character->getId(); ?>">
      <input type="hidden" name="operation" value="equip">
      <p><?php echo Hands.': <b>'.$character->remainingHands().'</b>'; ?><br>
      <?php 
      echo Equip.': <select name="invrow">
      <option value="0">'.Select_item.'</option>';
      $inventory = array_merge( $character->getInventory( 'weapon' ), $character->getInventory( 'armor' ), $character->getInventory( 'equipment' ) );
      foreach( $inventory as $line ){
        $info = '';
        switch( $line->getType() ){
          case 'armor':
          default:
            $item = new Armor( $site->getDatalink(), $line->getItem() );
            $info = $line->getValue().'/'.$item->getHitpoints();
          break;
          case 'weapon':
            $item = new Weapon( $site->getDatalink(), $line->getItem() );
            if( $item->getClipsize() > 0 ){
              $info = $line->getValue().'/'.$item->getClipsize().' ('.$line->getMax().')';
            }
          break;
          case 'equipment':
            $item = new Equipment( $site->getDatalink(), $line->getItem() );
          break;          
        }
        echo '<option value="'.$line->getId().'"><b>'.$item->getName().'</b> '.$info.'</option>';
      }
      echo '</select> <input type="submit" value="'.Equip.'">';
      ?>
      </p>
      </form>
      <?php
      echo '<div class="equip-title">'.Weapons.'</div>';
      $weapons = $character->getEquipment( 'weapon' );
      foreach( $weapons as $weapondata ){
        $weapon = new Weapon( $site->getDatalink(), $weapondata[1] );
        echo '<div class="equip-row">
        <div class="equip-block name">';
        if( $weapon->getIcon() != '' ){
          echo '<img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$weapon->getIcon().'" title="'.$weapon->getDescription().'">';
        }
        echo '</div>
        <div class="equip-block">
        <b>'.$weapon->getName().'</b>';
        if( $weapon->getClipsize() > 0 ){
          echo '<br>'.Ammo.': <b>'.$weapondata[2].'</b> / '.$weapon->getClipsize().'<br>'.Clips.': <b>'.$weapondata[3].'</b>';
        }
        echo '</div>
        <div class="equip-block">';
        if( $weapon->getClipsize() > 0 and $weapondata[2] < $weapon->getClipsize() and $weapondata[3] > 0 ){
          echo '<form name="reload'.$weapondata[0].'" method="post" action="" onsubmit="event.preventDefault();backend.post(this,false);">
          <input type="hidden" name="filter" value="'.$filter.'">
          <input type="hidden" name="pag" value="'.$pag.'">
          <input type="hidden" name="character" value="'.$character->getId().'">
          <input type="hidden" name="operation" value="reload">
          <input type="hidden" name="invrow" value="'.$weapondata[0].'">
          <input type="submit" value="'.Reload.'">
          </form>';
        }
        echo '<form name="unequip'.$weapondata[0].'" method="post" action="" onsubmit="event.preventDefault();backend.post(this,false);">
        <input type="hidden" name="filter" value="'.$filter.'">
        <input type="hidden" name="pag" value="'.$pag.'">
        <input type="hidden" name="character" value="'.$character->getId().'">
        <input type="hidden" name="operation" value="unequip">
        <input type="hidden" name="invrow" value="'.$weapondata[0].'">
        <input type="submit" value="'.Unequip.'">
        </form>
        </div>
        </div>';
      }
      echo '<div class="equip-title">'.Armor.'</div>';
      $armors = $character->getEquipment( 'armor' );
      foreach( $armors as $armordata ){
        $armor = new Armor( $site->getDatalink(), $armordata[1] );
        echo '<div class="equip-row">
        <div class="equip-block name">';
        if( $armor->getIcon() != '' ){
          echo '<img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$armor->getIcon().'" title="'.$armor->getDescription().'">';
        }
        echo '</div>
        <div class="equip-block">
        <b>'.$armor->getName().'</b>
        <br>'.Hitpoints.': <b>'.$armordata[2].'</b> / '.$armor->getHitpoints().'<br>'.Protection.': <b>'.$armor->getProtection().'</b>
        </div>
        <div class="equip-block">
        <form name="unequip'.$armordata[0].'" method="post" action="" onsubmit="event.preventDefault();backend.post(this,false);">
        <input type="hidden" name="filter" value="'.$filter.'">
        <input type="hidden" name="pag" value="'.$pag.'">
        <input type="hidden" name="character" value="'.$character->getId().'">
        <input type="hidden" name="operation" value="unequip">
        <input type="hidden" name="invrow" value="'.$armordata[0].'">
        <input type="submit" value="'.Unequip.'">
        </form>
        </div>
        </div>';
      }
      echo '<div class="equip-title">'.Equipment.'</div>';
      $equipment = $character->getEquipment( 'equipment' );
      foreach( $equipment as $equipmentdata ){
        $item = new Equipment( $site->getDatalink(), $equipmentdata[1] );
        echo '<div class="equip-row">
        <div class="equip-block name">';
        if( $item->getIcon() != '' ){
          echo '<img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$item->getIcon().'" title="'.$item->getDescription().'">';
        }
        echo '</div>
        <div class="equip-block">
        <b>'.$item->getName().'</b>';
        echo '</div>
        <div class="equip-block">
        <form name="unequip'.$equipmentdata[0].'" method="post" action="" onsubmit="event.preventDefault();backend.post(this,false);">
        <input type="hidden" name="filter" value="'.$filter.'">
        <input type="hidden" name="pag" value="'.$pag.'">
        <input type="hidden" name="character" value="'.$character->getId().'">
        <input type="hidden" name="operation" value="unequip">
        <input type="hidden" name="invrow" value="'.$equipmentdata[0].'">
        <input type="submit" value="'.Unequip.'">
        </form>
        </div>
        </div>';
      }
      ?>
    </div>
    <div class="equipsheet">
      <?php
      echo '<h1>'.Inventory.'</h1>
      <form name="giveitem" onsubmit="event.preventDefault(); backend.post(this,false);" method="post" action="">
        <input type="hidden" name="filter" value="'.$filter.'">
        <input type="hidden" name="pag" value="'.$pag.'">
        <input type="hidden" name="character" value="'.$character->getId().'">
        <input type="hidden" name="invrow" value="0">
        <input type="hidden" name="operation" value="giveitem">
        <p><select name="item">';
        $types = array( 'healing', 'repairing', 'weapon', 'armor', 'equipment' );
        foreach( $types as $type ){
          $result = $site->dbQuery( 'select id from '.mod.'deth_item_'.$type.' order by name asc' );
          foreach( $result as $row ){
            switch( $type ){
              case 'healing':
                $item = new HealingItem( $site->getDatalink(), $row[0]);
              break;
              case 'repairing':
                $item = new RepairingItem( $site->getDatalink(), $row[0]);
              break;
              case 'weapon':
                $item = new Weapon( $site->getDatalink(), $row[0]);
              break;
              case 'armor':
                $item = new Armor( $site->getDatalink(), $row[0]);
              break;
              case 'equipment':
                $item = new Equipment( $site->getDatalink(), $row[0]);
              break;
            }
            echo '<option value="'.$type.'-'.$item->getId().'">'.$item->getName().'</option>';
          }
        }
        echo '</select> <input type="submit" value="'.Add.'"></p>
      </form>';
      $inventory = $character->getInventory();
      foreach( $inventory as $line ){
        $value = '';
        switch( $line->getType() ){
          case 'armor':
          default:
            $item = new Armor( $site->getDatalink(), $line->getItem() );
            $value = Hitpoints.': <b>'.$line->getValue().'</b> / '.$item->getHitpoints().'<br>'.Protection.': <b>'.$item->getProtection().'</b>';
          break;
          case 'weapon':
            $item = new Weapon( $site->getDatalink(), $line->getItem() );
            if( $item->getClipsize() > 0 ){
              $value = Ammo.': <b>'.$line->getValue().'</b> / '.$item->getClipsize().'<br>'.Clips.': <b>'.$line->getMax().'</b>';
            }
          break;
          case 'equipment':
            $item = new Equipment( $site->getDatalink(), $line->getItem() );
            if( $item->getMaxhealth() != 0 ){ $value .= Health_boost.': <b>'.$item->getMaxhealth().'</b><br>'; }
            if( $item->getSpeed() != 0 ){ $value .= Speed_boost.': <b>'.$item->getSpeed().'</b><br>'; }
            if( $item->getStrength() != 0 ){ $value .= Strength_boost.': <b>'.$item->getStrength().'</b><br>'; }
            if( $item->getDexterity() != 0 ){ $value .= Dexterity_boost.': <b>'.$item->getDexterity().'</b><br>'; }
            if( $item->getConstitution() != 0 ){ $value .= Constitution_boost.': <b>'.$item->getConstitution().'</b><br>'; }
            if( $item->getIntelligence() != 0 ){ $value .= Intelligence_boost.': <b>'.$item->getIntelligence().'</b><br>'; }
          break;
          case 'healing':
            $item = new HealingItem( $site->getDatalink(), $line->getItem() );
            $value = Health_amount.': <b>'.$item->getHealth().'</b>';
          break;
          case 'repairing':
            $item = new RepairingItem( $site->getDatalink(), $line->getItem() );
            $value = Armor_amount.': <b>'.$item->getArmor().'</b>';
          break;
          default:
            $item = new Equipment( $site->getDatalink(), $line->getItem() );
          break;          
        }
        echo '<div class="editionitem">
        <form name="rem'.$line->getId().'" onsubmit="event.preventDefault(); backend.post(this,false);" method="post" action="">
        <input type="hidden" name="filter" value="'.$filter.'">
        <input type="hidden" name="pag" value="'.$pag.'">
        <input type="hidden" name="character" value="'.$character->getId().'">
        <input type="hidden" name="invrow" value="'.$line->getId().'">
        <input type="hidden" name="operation" value="deleterow">
        <div class="field">
          <b>'.$item->getName().'</b><br>
          '.$value.'
        </div>
        <div class="field">
          <input type="submit" value="'.Delete.'">
        </div>
        </form>
        </div>';
      }
      ?>
    </div>
  </div>
  <form name="editchar" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
  <input type="hidden" name="filter" value="<?php echo $filter; ?>">
  <input type="hidden" name="pag" value="<?php echo $pag; ?>">
  <input type="hidden" name="character" value="<?php echo $character->getId(); ?>">
  <div class="edblock">
    <p class="pinput"><?php echo Character_name; ?>:<br>
    <input type="text" name="name" value="<?php echo $character->getName(); ?>"></p>
    <p class="pinput"><?php echo Health; ?>:<br>
    <input type="number" style="width:6em" name="health" value="<?php echo $character->getHealth(); ?>"></p>
    <p><?php echo Wealth; ?>:<br>
    <input type="number" style="width:6em" name="coins" value="<?php echo $character->getCoins(); ?>"></p>
    <p><?php echo Premium_wealth; ?>:<br>
    <input type="number" style="width:6em" name="premium" value="<?php echo $character->getPremium(); ?>"></p>
  </div>
  <div class="edblock">
    <p class="pinput"><?php echo Gender; ?>:<br>
      <select name="gender">
        <option value="male"<?php if( $character->getGender() == 'male' ){ echo ' selected'; } ?>><?php echo Male; ?></option>
        <option value="female"<?php if( $character->getGender() == 'female' ){ echo ' selected'; } ?>><?php echo Female; ?></option>
      </select>
    </p>
    <p class="pinput"><?php echo Class_name; ?>:<br>
      <select name="class">
        <?php
        $classes = $race->getClasses();
        foreach( $classes as $optionclass ){
          echo '<option value="'.$optionclass->getId().'"';
          if( $character->getClass() == $optionclass->getId() ){
            echo ' selected';
          }
          echo '>'.$optionclass->getName().'</option>';
        }
        ?>
      </select>
    </p>
    <p class="pinput"><?php echo Level; ?>:<br>
    <input type="number" style="width:6em" name="level" value="<?php echo $character->getLevel(); ?>"></p>
    <p><input type="checkbox" name="rename" value="1"><?php echo Random_name; ?></p>
    <p><input type="checkbox" name="reloadbody" value="1"><?php echo Random_body; ?></p>
    <p><input type="checkbox" name="reloadface" value="1"><?php echo Random_face; ?></p>
    <p><input type="checkbox" name="faceless" value="1"><?php echo Faceless; ?></p>
  </div>
  <p><input type="submit" value="<?php echo Save_character; ?>">
  <input type="checkbox" name="delete" value="1"><?php echo Delete_character; ?></p>
  </form>
<?php
}
?>