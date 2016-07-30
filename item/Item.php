<?php namespace SCFR\StarMatrix\Item;
class Item {
  public function is_different(Item $item) {
    $changes = $add = array();
    foreach($this as $propertie => $val) {
      if(!isset($item->{$propertie}) || $item->{$propertie} != $val) {
        $changes[] = $propertie;
      }
    }

    foreach($item as $foreign_propertie => $foreign_val) {
      if(!isset($this->{$foreign_propertie})) {
        $add[] = $foreign_propertie;
      }
    }

    if(sizeof($changes) > 0 || sizeof($add) > 0) return array(
      "MODIFIED" => $changes,
      "ADDED"    => $add,
    );
    else return false;
  }
}
?>
