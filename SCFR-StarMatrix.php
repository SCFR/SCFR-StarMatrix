<?php /*
Plugin Name: WPHPBB
Plugin URI: https://github.com/Superd22/WPHPBB-Site
Description: Wordpress-PhpBB bridge
Author: Super d
Version: 1.0
Author URI: https://github.com/Superd22/
Text Domain: WPHPBB
*/
namespace SCFR\StarMatrix;

require_once("event/listener.php");
require_once("controller/settings.php");
require_once("api/RSI.php");
//require_once("controller/excel/Galaxy.php");
require_once("controller/getter/Astrum.php");
require_once("controller/getter/Galaxy.php");
require_once("controller/getter/System.php");

class Main {
  private $listener;
  private $db;

  function __construct() {
    $this->listener = new event\Listener();
    $this->db = db\Wpdb::get_db();
  }

  public function init_plugin() {
    $this->db->create_tables();
  }

}

$StarMatrix = new Main();
register_activation_hook(__FILE__, array(&$StarMatrix, 'init_plugin'));

//$test = new controller\getter\Galaxy();
?>
