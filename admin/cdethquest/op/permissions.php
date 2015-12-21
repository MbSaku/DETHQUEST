<?php
$menu = Array(  //Main menu
  'administration' => 'Administration',
  'characters' => 'Characters',
  'maps' => 'World',
  'master' => 'Master',
  'items' => 'Items',
  'equipment' => 'Equipment',
  'account' => 'Account',
  'character' => 'Player_character',
  'game' => 'Game'
);
$smenu = Array(  //Sub-menus
  'administration' => Array (
    'administration' => 'Users',
    'charges' => 'Charges',
    'permissions' => 'Permissions'
  ),
  'characters' => Array (
    'characters' => 'PCs',
    'npcs' => 'NPCs',
    'classes' => 'Classes',
    'races' => 'Races'
  ),
  'maps' => Array (
    'maps' => 'Maps',
    'world' => 'Places',
  ),
  'master' => Array (
    'master' => 'Scenarios'
  ),
  'items' => Array (
    'items' => 'Heal',
    'repair' => 'Repair'
  ),
  'equipment' => Array (
    'equipment' => 'Equipment',
    'weapon' => 'Weapons',
    'armor' => 'Armor'
  ),
  'account' => Array (
    'account' => 'Settings'
  ),
  'character' => Array (
    'character' => 'Character_sheet',
    'inventory' => 'Inventory'
  ),
  'game' => Array (
    'game' => 'Mission',
    'city' => 'The_city'
  )
);
if (isset($_POST['rebuild'])){
  $query = 'truncate '.mod.'deth_permissions';
  $datalink->dbQuery($query, 'query');
  foreach ($_POST as $k => $v){
    $key = explode('-', $k);
    if (count ($key) == 2){
      $query = 'insert into '.mod.'deth_permissions (charge, access) values ("'.$key[0].'", "'.$key[1].'")';
      $datalink->dbQuery($query, 'query');
    }
  }
}
$charges = Array();
?>  
<p><?php echo HelpPermissions; ?></p>  
<form name="permissions" onsubmit="event.preventDefault(); backend.post(this);">
<input type="hidden" name="rebuild" value="1">
<table>
<tr><th colspan="2"> - </th>
  <?php
  $query = 'select id, name from '.mod.'deth_charges order by level asc';
  $result = $datalink->dbQuery($query, 'result');
  foreach ($result as $row){
    $charges[] = $row[0];
    echo '<th>'.$row[1].'</th>';
  }
  ?>
</tr>
  <?php
  foreach ($smenu as $murl => $submenu){
    foreach ($submenu as $smurl => $smet){
      echo '<tr><th>'.constant($menu[$murl]).'</th><td><b>'.constant($smenu[$murl][$smurl]).'</b></td>';
      foreach ($charges as $charge){
        echo '<td><input type="checkbox" name="'.$charge.'-'.$smurl.'"';
        $query = 'select charge from '.mod.'deth_permissions where charge="'.$charge.'" and access="'.$smurl.'"';
        if ($datalink->dbQuery($query, 'rows') > 0){ echo ' checked'; }
        echo ' value="1"></td>';
      }
    }
    echo '</tr>';
  }
  ?>
</table>
<p><input type="submit" name="rebuild" value="<?php echo Rebuild_permissions; ?>"></p>
</form>