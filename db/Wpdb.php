<?php namespace SCFR\db;
class Wpdb implements Db {
  protected $wpdb;
  protected static $_instance;

  private function __construct() {
    global $wpdb;

    if(isset($wpdb))
    $this->wpdb = $wpdb;
    else throw new \Exception('$wpdb is not set');
    $this->settings = \SCFR\StarMatrix\controller\Settings::get_settings();
  }

  public static function get_db() {
    if(is_null(self::$_instance))
    self::$_instance = new Wpdb();

    return self::$_instance;
  }

  public function create_tables() {
    $charset_collate = $wpdb->get_charset_collate();

    $sqls = array(
      "CREATE TABLE IF NOT EXISTS ".$this->settings["galaxy_table"]."(
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      version VARCHAR(10) NOT NULL,
      date_parsed DATETIME,
      UNIQUE KEY id (id)
      ) $charset_collate;"
      ,
      "CREATE TABLE IF NOT EXISTS ".$this->settings["systems_table"]." (
      id mediumint(12) NOT NULL AUTO_INCREMENT,
      system_id mediumint(9) NOT NULL,
      galaxy_id mediumint(9) NOT NULL,
      UNIQUE KEY id (id)
      ) $charset_collate;"
      ,
      "CREATE TABLE IF NOT EXISTS ".$this->settings["astrum_table"]." (
      id mediumint(15) NOT NULL AUTO_INCREMENT,
      astrum_id mediumint(15) NOT NULL,
      system_id mediumint(9) NOT NULL,
      UNIQUE KEY id (id)
      ) $charset_collate;"
    );
    $wpdb->show_errors();
    foreach($sqls as $sql) {
      if($wpdb->query($sql) === false) die($wpdb->print_error());
    }
  }

}
?>
