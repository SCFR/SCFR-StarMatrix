<?php /*
 Plugin Name: WPHPBB
 Plugin URI: https://github.com/Superd22/WPHPBB-Site
 Description: Wordpress-PhpBB bridge
 Author: Super d
 Version: 1.0
 Author URI: https://github.com/Superd22/
 Text Domain: WPHPBB
*/
  namespace SCFR\StarMatrix\;

  require_once("event/listener.php");

  class Main {
    function __construct() {
      $this->listener = new event\Listener();
    }

  }
  $StarMatrix = new Main();
?>
