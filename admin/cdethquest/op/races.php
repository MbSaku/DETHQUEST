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
  <p><?php echo HelpRaces; ?></p>  
  <p><?php echo Active_language; ?> <b><?php echo $site->activeLanguage(); ?></b></p>  
<?php
if (!isset($_POST['race'])){
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
    <input type="hidden" name="race" value="0">
    <p class="pinput"><input type="submit" value="<?php echo Add_race; ?>"></p>
  </form>
  <div class="editiontitle"><?php echo Races; ?></div>
  <?php
  $query = 'select id from '.mod.'deth_races where name like "%'.$filter.'%" order by playable desc, name asc';
  $rows = $site->getDatalink()->dbQuery($query, 'rows');
  $numpags = ceil ($rows / $regspag);
  $result = $site->getDatalink()->dbQuery($query, 'result', ($regspag * $pag));
  $i = 0;
  if ($rows > 0){
    foreach ($result as $row){
      if ($i < $regspag){
        $race = new Race($site->getDatalink(), $row[0]);
        echo '<form name="race'.$race->getId().'" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
        <div class="editionitem">
        <input type="hidden" name="pag" value="'.$pag.'">
        <input type="hidden" name="filter" value="'.$filter.'">
        <input type="hidden" name="race" value="'.$race->getId().'">
        <div class="field"><input type="submit" value="'.$race->getName().'"></div>
        <div class="field">';
        if ($race->getIcon() != ''){
          echo '<img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$race->getIcon().'">';
        }
        echo '</div>
        <div class="field">'.Race_offset.': <b>'.$race->calculateOffset().'%</b><br>
        '.Race_playable.': <b>'; 
        if ($race->getPlayable()){ echo Yes_playable; }else{ echo Not_playable; } 
        echo '</b></div>
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
  $race = new Race( $site->getDatalink(), $_POST['race'] );
  if( isset( $_POST['name'] )
  and isset( $_POST['hands'] )
  and isset( $_POST['xscale'] )
  and isset( $_POST['yscale'] )
  and isset( $_POST['description'] )
  and isset( $_POST['health'] )
  and isset( $_POST['speed'] )
  and isset( $_POST['strength'] )
  and isset( $_POST['dexterity'] )
  and isset( $_POST['constitution'] )
  and isset( $_POST['intelligence'] ) ){
    $race->setName( $_POST['name'] );
    $race->setHands( $_POST['hands'] );
    $race->setXscale( $_POST['xscale'] );
    $race->setYscale( $_POST['yscale'] );
    $race->setHands( $_POST['hands'] );
    $race->setDescription( $_POST['description'] );
    $race->setModHealth( $_POST['health'] );
    $race->setModSpeed( $_POST['speed'] );
    $race->setModStrength( $_POST['strength'] );
    $race->setModDexterity( $_POST['dexterity'] );
    $race->setModConstitution( $_POST['constitution'] );
    $race->setModIntelligence( $_POST['intelligence'] );
    if( isset( $_POST['playable'] ) ){
      $race->setPlayable( true );
    }else{
      $race->setPlayable( false );
    }
    if( isset( $_POST['armor'] ) ){
      $race->setArmor( true );
    }else{
      $race->setArmor( false );
    }
    if (isset($_FILES['icon'])){
      $race->setIcon( $site->imageUpload( $_FILES['icon'], $module->getFolder() ) );
    }
    if( isset( $_POST['delete'] ) ){
      echo $race->delete();
    }else{
      echo $race->save();
    }
    $race->clearClasses();
    foreach( $_POST as $key => $value ){
      $data = explode( '-', $key );
      if( count( $data ) == 2 
      and $data[0] == 'class'){
        $race->addClass( $data[1] );
      }
    }
  }
  if (isset($_POST['deleteicon'])){
    if( file_exists( $site->getRoot().'uploads/'.$module->getFolder().'/'.$race->getIcon() ) ){
      unlink( $site->getRoot().'uploads/'.$module->getFolder().'/'.$race->getIcon() );
    }
    $race->setIcon('');
    $race->save();
  }
  ?>
  <form name="addnew" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
    <input type="hidden" name="pag" value="<?php echo $pag; ?>">
    <p><input type="submit" value="<?php echo Back_to_races; ?>"></p>
  </form>
  <h1><?php echo Editing_race.' '.$race->getName(); ?></h1>
  <form name="editrace" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
    <input type="hidden" name="pag" value="<?php echo $pag; ?>">
    <input type="hidden" name="race" value="<?php echo $race->getId(); ?>">
    <div class="edblock">    
      <p class="pinput"><?php echo Race_name; ?><br>
      <input type="text" name="name" value="<?php echo $race->getName(); ?>"></p>      
      <p class="pinput"><?php echo Race_hands; ?><br>
      <input type="number" min="0" step="1" name="hands" value="<?php echo $race->getHands(); ?>" style="width:4em"></p>      
      <p class="pinput"><?php echo Race_xscale; ?><br>
      <input type="number" min="0.75" max="1.25" step="0.01" name="xscale" value="<?php echo $race->getXscale(); ?>" style="width:4em"></p>      
      <p class="pinput"><?php echo Race_yscale; ?><br>
      <input type="number" min="0.75" max="1.25" step="0.01" name="yscale" value="<?php echo $race->getYscale(); ?>" style="width:4em"></p>      
      <p class="pinput">
      <input type="checkbox" name="playable" value="1"<?php if ($race->getPlayable()){ echo ' checked'; } ?>> <?php echo Race_playable; ?></p>      
      <p class="pinput"><input type="checkbox" name="armor" <?php
      if ($race->getArmor()){
        echo ' checked';
      }
      ?>> <?php echo Armor_capable; ?></p>    
    </div>
    <div class="edblock">
      <p><?php echo Race_description; ?><br>
      <textarea name="description"><?php echo $race->getDescription(); ?></textarea></p>    
    </div>
    <div class="edblock">    
      <h2><?php echo Race_stats; ?></h2>    
      <p><?php echo Race_stats_help; ?></p>    
      <p><?php echo Race_offset; ?>: <span id="raceoffset"><?php echo $race->calculateOffset(); ?></span>%</p>    
      <table>
        <tr>
          <th>
            <?php echo Health; ?>
          </th>
          <td>
            <input type="number" name="health" id="health" value="<?php echo $race->getModHealth(); ?>" onkeyup="calcRaceOffset()" style="width:4em">%
          </td>
        </tr>
        <tr>
          <th>
            <?php echo Speed; ?>
          </th>
          <td>
            <input type="number" name="speed" id="speed" value="<?php echo $race->getModSpeed(); ?>" onkeyup="calcRaceOffset()" style="width:4em">%
          </td>
        </tr>
        <tr>
          <th>
            <?php echo Strength; ?>
          </th>
          <td>
            <input type="number" name="strength" id="strength" value="<?php echo $race->getModStrength(); ?>" onkeyup="calcRaceOffset()" style="width:4em">%
          </td>
        </tr>
        <tr>
          <th>
            <?php echo Dexterity; ?>
          </th>
          <td>
            <input type="number" name="dexterity" id="dexterity" value="<?php echo $race->getModDexterity(); ?>" onkeyup="calcRaceOffset()" style="width:4em">%
          </td>
        </tr>
        <tr>
          <th>
            <?php echo Constitution; ?>
          </th>
          <td>
            <input type="number" name="constitution" id="constitution" value="<?php echo $race->getModConstitution(); ?>" onkeyup="calcRaceOffset()" style="width:4em">%
          </td>
        </tr>
        <tr>
          <th>
            <?php echo Intelligence; ?>
          </th>
          <td>
            <input type="number" name="intelligence" id="intelligence" value="<?php echo $race->getModIntelligence(); ?>" onkeyup="calcRaceOffset()" style="width:4em">%
          </td>
        </tr>
      </table>
    </div>    
    <div class="edblock">
      <h2><?php echo Race_classes; ?></h2>    
      <?php
      $result = $site->getDatalink()->dbQuery('select id from '.mod.'deth_classes order by name asc', 'result');
      foreach ($result as $row){
        $class = new CharacterClass($site->getDatalink(), $row[0]);
        echo '<div class="inline-check"><input type="checkbox" name="class-'.$class->getId().'"';
        if ($race->hasClassAvailable($class->getId())){
          echo ' checked';
        }
        echo '> '.$class->getName().'</div>';
      }
      ?>    
    </div>
    <p><input type="submit" value="<?php echo Save_race; ?>">
    <input type="checkbox" name="delete" value="1"><?php echo Delete_race; ?></p>
    <?php 
    if ($race->getIcon() == ''){
      echo '<div class="formimg">'.Race_image.'<br>
      <input type="file" name="icon"></div>';
    }
    ?>
  </form>
  <?php
  if ($race->getIcon() != ''){
    echo '<form name="icon" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="filter" value="'.$filter.'">
    <input type="hidden" name="pag" value="'.$pag.'">
    <input type="hidden" name="race" value="'.$race->getId().'">
    <input type="hidden" name="deleteicon" value="1">
    <div class="formimg">
    <img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$race->getIcon().'"><br>
    <input type="submit" value="'.Delete_picture.'">
    </div>
    </form>';
  }
  if($race->getId() == 0){
    echo '<p>'.Save_race_to_edit_further.'</p>';
  }else{
    if( isset( $_POST['variant'] ) 
    and isset( $_POST['type'] ) ){      
      if( isset( $_FILES['maleimage'] ) 
      and isset( $_FILES['femaleimage'] ) ){
        $race->setVariantImages( $site->imageUpload( $_FILES['maleimage'], $module->getFolder() ), $site->imageUpload( $_FILES['femaleimage'], $module->getFolder() ), $_POST['variant'], $_POST['type'] );
      }else{
        if( isset( $_FILES['maleimage'] ) ){
          $race->setMaleVariant( $site->imageUpload( $_FILES['maleimage'], $module->getFolder() ), $_POST['variant'], $_POST['type'] );
        }
        if( isset( $_FILES['femaleimage'] ) ){
          $race->setFemaleVariant( $site->imageUpload( $_FILES['femaleimage'], $module->getFolder() ), $_POST['variant'], $_POST['type'] );
        }
      }
      if (isset($_POST['deletemaleimage'])){
        if( file_exists( $site->getRoot().'uploads/'.$module->getFolder().'/'.$race->getMaleVariant( $_POST['variant'], $_POST['type'] ) ) ){
          unlink($site->getRoot().'uploads/'.$module->getFolder().'/'.$race->getMaleVariant( $_POST['variant'], $_POST['type'] ) );
        }
        $race->setMaleVariant( "", $_POST['variant'], $_POST['type'] );
      }
      if (isset($_POST['deletefemaleimage'])){
        if( file_exists( $site->getRoot().'uploads/'.$module->getFolder().'/'.$race->getFemaleVariant( $_POST['variant'], $_POST['type'] ) ) ){
          unlink($site->getRoot().'uploads/'.$module->getFolder().'/'.$race->getFemaleVariant( $_POST['variant'], $_POST['type'] ) );
        }
        $race->setFemaleVariant( "", $_POST['variant'], $_POST['type'] );
      }
      if( isset( $_POST['deletevariant'] ) ){
        $race->deleteVariant( $_POST['variant'], $_POST['type'] );
      }
      if( isset( $_POST['name'] ) ){
        $race->setVariantName( $_POST['variant'], $_POST['name'], $_POST['type'] );
      }
    }
    $types = Array("body", "hair", "head", "face");
    foreach ($types as $type){
      switch( $type ){
        case 'body':
          echo '<h2>'.Body_appearance.'</h2>
          <p>'.Body_help.'</p>';
        break;
        case 'hair':
          echo '<h2>'.Hair_appearance.'</h2>
          <p>'.Hair_help.'</p>';
        break;
        case 'head':
          echo '<h2>'.Head_appearance.'</h2>
          <p>'.Head_help.'</p>';
        break;
        case 'face':
          echo '<h2>'.Face_appearance.'</h2>
          <p>'.Face_help.'</p>';
        break;
      }
      $result = $site->getDatalink()->dbQuery( 'select id, maleimage, femaleimage from '.mod.'deth_race_'.$type.' 
      where race="'.$race->getId().'" order by name asc', 'result' );
      foreach( $result as $row ){
        echo '<div class="edblock">
        <div class="editionitem">
        <form name="'.$type.$row[0].'" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="" enctype="multipart/form-data">
        <input type="hidden" name="filter" value="'.$filter.'">
        <input type="hidden" name="pag" value="'.$pag.'">
        <input type="hidden" name="race" value="'.$race->getId().'">
        <input type="hidden" name="type" value="'.$type.'">
        <input type="hidden" name="variant" value="'.$row[0].'">
        <div class="field">';
        if( $race->getMaleVariant( $row[0], $type ) == '' ){
          echo '<div class="pinput formimg">'.Male_image.'';
          if( $type == 'body' ){
            echo '<div class="equipment-preview">
            <img src="'.$_GET['root'].'admin/'.$module->getFolder().'/css/images/generic-male.png">
            </div><br>'; 
          }
          echo '<input type="file" name="maleimage"></div>';
        }else{
          echo '<div class="pinput formimg">'.Male_image.'<br>';
          if( $type == 'body' ){
            echo '<div class="equipment-preview">
            <img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$race->getMaleVariant( $row[0], $type ).'">
            </div><br>';
          }else{
            echo '<div class="charportrait"><div class="inner">
            <div class="charlayer" style="background-size:100% 100%;background-image:url('.$_GET['root'].'admin/'.$module->getFolder().'/css/images/generic-male.png)">
            <div class="charlayer" style="background-size:100% 100%;background-image:url('.$_GET['root'].'uploads/'.$module->getFolder().'/'.$race->getMaleVariant( $row[0], $type ).'"></div>
            </div>
            </div></div><br>';
          }
          echo '<input type="checkbox" value="1" name="deletemaleimage"> '.Delete_picture.'
          </div>';
        }
        echo '</div>
        <div class="field">';
        if( $race->getFemaleVariant( $row[0], $type ) == '' ){
          echo '<div class="pinput formimg">'.Female_image.'<br>';
          if( $type == 'body' ){
            echo '<div class="equipment-preview">
            <img src="'.$_GET['root'].'admin/'.$module->getFolder().'/css/images/generic-female.png">
            </div><br>';
          }
          echo '<input type="file" name="femaleimage"></div>';
        }else{
          echo '<div class="pinput formimg">'.Female_image.'<br>';
          if( $type == 'body' ){
            echo '<div class="equipment-preview">
            <img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$race->getFemaleVariant( $row[0], $type ).'">
            </div><br>';
          }else{
            echo '<div class="charportrait"><div class="inner">
            <div class="charlayer" style="background-size:100% 100%;background-image:url('.$_GET['root'].'admin/'.$module->getFolder().'/css/images/generic-female.png)">
            <div class="charlayer" style="background-size:100% 100%;background-image:url('.$_GET['root'].'uploads/'.$module->getFolder().'/'.$race->getFemaleVariant( $row[0], $type ).'"></div>
            </div>
            </div></div><br>';
          }
          echo '<input type="checkbox" value="1" name="deletefemaleimage"> '.Delete_picture.'
          </div>';
        }
        echo '
        </div>
        <div class="field">
        <p><input type="text" name="name" value="'.$race->getVariantName( $row[0], $type ).'" style="width:6em;">
        <input type="submit" value="'.Save_variant.'">
        <input type="checkbox" name="deletevariant" value="1">'.Check_to_delete.'</p>
        </div>
        </form>
        </div>
        </div>';
      }
      echo '
      <div class="editionitem">
      <form name="'.$type.'0" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="" enctype="multipart/form-data">
      <input type="hidden" name="filter" value="'.$filter.'">
      <input type="hidden" name="pag" value="'.$pag.'">
      <input type="hidden" name="race" value="'.$race->getId().'">
      <input type="hidden" name="type" value="'.$type.'">
      <input type="hidden" name="variant" value="0">
      <div class="field">
      <p class="pinput formimg">'.Male_image.'<br>';
      if( $type == 'body' ){
        echo '<div class="equipment-preview">
        <img src="'.$_GET['root'].'admin/'.$module->getFolder().'/css/images/generic-male.png">
        </div>'; 
      }
      echo '<input type="file" name="maleimage"></p>
      </div>
      <div class="field">
      <p class="pinput formimg">'.Female_image.'<br>';
      if( $type == 'body' ){
        echo '<div class="equipment-preview">
        <img src="'.$_GET['root'].'admin/'.$module->getFolder().'/css/images/generic-female.png">
        </div>'; 
      }
      echo '<input type="file" name="femaleimage"></p>
      </div>
      <div class="field">
      <p><input type="submit" value="'.Save_variant.'"></p>
      </div>
      </div>
      </form>';
    }    
    if( isset( $_POST['charname'] )
    and isset( $_POST['gender'] ) ){
      $race->addName( $_POST['charname'], $_POST['gender'] );
    }
    if( isset( $_POST['delname'] ) ){
      $race->delName( $_POST['delname'] );
    }
    if( isset( $_POST['chardialog'] )
    and isset( $_POST['gender'] ) ){
      $race->addDialog( $_POST['chardialog'], $_POST['gender'] );
    }
    if( isset( $_POST['deldialog'] ) ){
      $race->delDialog( $_POST['deldialog'] );
    }
    echo '<h2>'.Race_names_and_dialog.'</h2>    
    <p>'.Race_namedialog_help.'<p/>
    <div class="pinput">
    <div class="editiontitle">'.Male_names.'</div>';
    $result = $site->getDatalink()->dbQuery( 'select id, name from '.mod.'deth_race_names 
    where race="'.$race->getId().'" and gender="male" order by name asc', 'result' );
    foreach ($result as $row){
      echo '<form name="malename'.$row[0].'" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
      <input type="hidden" name="filter" value="'.$filter.'">
      <input type="hidden" name="pag" value="'.$pag.'">
      <input type="hidden" name="race" value="'.$race->getId().'">
      <div class="editionitem">
      <input type="hidden" name="delname" value="'.$row[0].'">
        <div class="field"><input type="submit" value="'.Delete_data.'"> '.$row[1].'</div>
      </div>
      </form>';
    }
    echo '<form name="malenames" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="filter" value="'.$filter.'">
    <input type="hidden" name="pag" value="'.$pag.'">
    <input type="hidden" name="race" value="'.$race->getId().'">
    <div class="editionitem">
    <input type="hidden" name="gender" value="male">
      <div class="field"">
        <input type="text" name="charname">
        <input type="submit" value="'.Add_data.'">
      </div>
    </div>
    </form>
    <div class="editiontitle">'.Male_dialog.'</div>';
    $result = $site->getDatalink()->dbQuery( 'select id, quote from '.mod.'deth_race_dialog 
    where race="'.$race->getId().'" and gender="male" order by id asc', 'result' );
    foreach( $result as $row ){
      echo '<form name="maledialog'.$row[0].'" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
      <input type="hidden" name="filter" value="'.$filter.'">
      <input type="hidden" name="pag" value="'.$pag.'">
      <input type="hidden" name="race" value="'.$race->getId().'">
      <div class="editionitem">
      <input type="hidden" name="deldialog" value="'.$row[0].'">
        <div class="field"><input type="submit" value="'.Delete_data.'"> '.$row[1].'</div>
      </div>
      </form>';
    }
    echo '
    <form name="maledialog" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="filter" value="'.$filter.'">
    <input type="hidden" name="pag" value="'.$pag.'">
    <input type="hidden" name="race" value="'.$race->getId().'">
    <div class="editionitem">
    <input type="hidden" name="gender" value="male">
      <div class="field"">
        <input type="text" name="chardialog">
        <input type="submit" value="'.Add_data.'">
      </div>
    </div>
    </form>
    
    </div>
    <div class="pinput">
    <div class="editiontitle">'.Female_names.'</div>';
    $result = $site->getDatalink()->dbQuery( 'select id, name from '.mod.'deth_race_names 
    where race="'.$race->getId().'" and gender="female" order by name asc', 'result' );
    foreach( $result as $row ){
      echo '<form name="femalename'.$row[0].'" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
      <input type="hidden" name="filter" value="'.$filter.'">
      <input type="hidden" name="pag" value="'.$pag.'">
      <input type="hidden" name="race" value="'.$race->getId().'">
      <div class="editionitem">
      <input type="hidden" name="delname" value="'.$row[0].'">
        <div class="field"><input type="submit" value="'.Delete_data.'"> '.$row[1].'</div>
      </div>
      </form>';
    }
    echo '
    <form name="femalenames" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="filter" value="'.$filter.'">
    <input type="hidden" name="pag" value="'.$pag.'">
    <input type="hidden" name="race" value="'.$race->getId().'">
    <div class="editionitem">
    <input type="hidden" name="gender" value="female">
      <div class="field"">
        <input type="text" name="charname">
        <input type="submit" value="'.Add_data.'">
      </div>
    </div>
    </form>
    
    <div class="editiontitle">'.Female_dialog.'</div>';
    $result = $site->getDatalink()->dbQuery( 'select id, quote from '.mod.'deth_race_dialog 
    where race="'.$race->getId().'" and gender="female" order by id asc', 'result' );
    foreach( $result as $row ){
      echo '<form name="femaledialog'.$row[0].'" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
      <input type="hidden" name="filter" value="'.$filter.'">
      <input type="hidden" name="pag" value="'.$pag.'">
      <input type="hidden" name="race" value="'.$race->getId().'">
      <div class="editionitem">
      <input type="hidden" name="deldialog" value="'.$row[0].'">
        <div class="field"><input type="submit" value="'.Delete_data.'"> '.$row[1].'</div>
      </div>
      </form>';
    }
    echo '
    <form name="femaledialog" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="filter" value="'.$filter.'">
    <input type="hidden" name="pag" value="'.$pag.'">
    <input type="hidden" name="race" value="'.$race->getId().'">
    <div class="editionitem">
    <input type="hidden" name="gender" value="female">
      <div class="field"">
        <input type="text" name="chardialog">
        <input type="submit" value="'.Add_data.'">
      </div>
    </div>
    </form>    
    </div>';
  }
  ?>
  <script>
  
  function calcRaceOffset(){
    var offset = parseInt($("#health").val()) 
    + parseInt($("#speed").val()) 
    + parseInt($("#strength").val()) 
    + parseInt($("#dexterity").val()) 
    + parseInt($("#constitution").val()) 
    + parseInt($("#intelligence").val());
    $("#raceoffset").html(offset);
  };
  
  </script>
  <?php
}
?>