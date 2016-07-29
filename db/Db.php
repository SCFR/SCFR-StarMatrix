<?php namespace SCFR\db;
interface Db {
  protected static $_instance;
  
  private function __construct();

  public function create_tables();
  public static function get_db();
}
?>
