<?php
if (session_id () == ''){ session_start (); }
include('lib/sql.settings.php');
include('lib/functions.php');
include('admin/site/class/datalink.class.php');
include('admin/site/class/database.object.php');
include('admin/site/class/image.class.php');
include('admin/site/class/devicedetector.class.php');
include('admin/site/class/site.class.php');
include('admin/site/class/module.class.php');
include('admin/users/class/user.class.php');
include('admin/users/class/email.class.php');
include('admin/content/class/content.class.php');
$site = new Site( $sqlparams );
$site->getDatalink()->setDebug( true );
include( $site->loadLangfile() );
$user = new User( $site->getDatalink() );
$site->inputCheck();
$user->jsessionCheck( $site );
?>
<!DOCTYPE html>
<html>
  <?php ob_start(); ?>
  <head>
    <title><?php echo $site->getTitle(); ?></title>
    <?php echo $site->metaVariables(); ?>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="Cache-control" content="public">
    <?php
    if ($site->getDevice() != 'desktop'){  //Portable device meta tags
    ?>
    <meta id="Viewport" name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="apple-touch-icon" href="<?php echo $site->getBaseroot().'styles/'.$site->getStyle(); ?>/icon.png">
    <?php
    }
    ?>
    <link rel="shortcut icon" type="image/png" href="<?php echo $site->getBaseroot().'styles/'.$site->getStyle(); ?>/icon.png">
    <link rel="stylesheet" href="<?php echo $site->getBaseroot().'styles/'.$site->getStyle().'/'.$site->getDevice(); ?>.css" type="text/css">
    <script type="text/javascript" src="<?php echo $site->getBaseroot(); ?>js/jquery/jquery-1.11.3.min.js"></script>
    <script type="text/javascript" src="<?php echo $site->getBaseroot(); ?>js/basic.js"></script>
  </head>
  <body>
    <div align="center">
      <div id="page">
        <div id="header">
          <?php include ('styles/'.$site->getStyle().'/'.$site->getStyle().'.php'); ?>
        </div>
        <div id="navigation">
          <?php
          if ($site->isConfigured()){
            echo $site->languageMenu();
            echo $site->contentMenu();
            echo $site->moduleMenu();
          }
          ?>
        </div>
        <div id="content">
          <?php
          switch ($site->getToken()){
            case 'config':
              include('admin/site/front/configuration.php');
              break;
            case 'content':
              if ($site->getMaintenance()
              and !$user->isAdmin()){
                $site->showMaintenance();
              }else{
                $path = $site->getPath();
                $content = new Content($site->getDatalink(), $path[0]);
                $content->show();
              }
              break;
            case 'frontend':
              $path = $site->getPath();
              $module = new Module($site->getDatalink(), $path[0]);
              if( $module->getId() != 0 
              and $module->getActive() ){
                if( ( $site->getMaintenance()
                and $module->getUrl() != 'login'
                and !$_SESSION['logged'] )
                or ( $site->getMaintenance()
                and $_SESSION['logged'] ) 
                and !$user->isAdmin() ){  //We allow the login module to display
                  $site->showMaintenance();
                }else{
                  include( $module->getLangfile() );
                  include( 'admin/'.$module->getFrontend() );
                }
              }
              break;
            case 'backend':
              $module = new Module($site->getDatalink(), $site->getAdmin());
              if ($module->getId() != 0
              and $user->checkPermission($module->getId())){
                if ($site->getMaintenance()
                and !$user->isAdmin()){
                  $site->showMaintenance();
                }else{
                  include($module->getLangfile());
                  include('admin/'.$module->getBackend());
                }
              }
              break;
          }
          ?>
        </div>
        <div id="footer">
          <?php echo $site->getFooter(); ?>
        </div>
      </div>
    </div>
    <?php 
      include ('lib/verifpop.php'); 
    ?>
  </body>
  <?php 
  ob_flush();
  ob_end_clean();
  ?>
</html>
<?php
$site->shut();
?>