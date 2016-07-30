<?php /*
Plugin Name: SCFR StarMatrix
Plugin URI: https://github.com/Superd22/SCFR-StarMatrix
Description: SCFR StarMatrix
Author: Super d
Version: 1.0
Author URI: https://github.com/Superd22/
Text Domain: StarMatrix
*/
namespace SCFR\StarMatrix;

error_reporting(-1);
require_once("event/listener.php");
require_once("controller/settings.php");
require_once("api/RSI.php");
//require_once("controller/excel/Galaxy.php");
require_once("controller/getter/Astrum.php");
require_once("controller/getter/Galaxy.php");
require_once("controller/getter/System.php");

require_once("item/Item.php");
require_once("item/Affiliation.php");
require_once("item/Astrum.php");
require_once("item/Galaxy.php");
require_once("item/Species.php");
require_once("item/System.php");
require_once("item/Tunnel.php");

require_once("db/Db.php");
require_once("db/Wpdb.php");

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

$sm = new Main();
$sm->init_plugin();
register_activation_hook(__FILE__, array($sm, 'init_plugin'));

$test = new controller\getter\Galaxy();
die();
?>
