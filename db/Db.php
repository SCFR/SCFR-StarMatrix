<?php namespace SCFR\StarMatrix\db;
interface Db {
  function create_tables();
  static function get_db();
}
?>
