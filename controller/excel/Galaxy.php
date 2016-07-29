<?php namespace SCFR\StarMatrix\controller\excel;

class Galaxy {
  private $col_order;
  private $set_headers;
  function __construct() {
    $this->rsi_api = new \SCFR\StarMatrix\api\RSI_API();
    $this->set_headers = false;
    $this->rsi_api->LoginRSI();
    $this->raw_data = $this->rsi_api->get_api_data("https://robertsspaceindustries.com/api/starmap/bootup");

    $this->process_raw();
  }

  private function process_raw() {
    $systems = $this->raw_data->systems->resultset;

    foreach($systems as $system) {
      $csv .= $this->system_to_csv($system);
    }
    echo "<pre>";
    print_r($csv);

  }

  private function system_to_csv($system) {
    $csv = "";
    if(!$this->set_headers) {
      $csv .= $this->do_headers($system);
    }

      echo "<hr />";
      print_r($system);
      echo "<hr />";
    foreach($this->col_order as $propertie) {
      if(isset($system->{$propertie}) && ( is_string($system->{$propertie}) || is_numeric($system->{$propertie}) ) ) {
        $string = $system->{$propertie};
        if(strpos($string,",") !== false || strpos($string, "\"") !== false || strpos($string,"\n") !== false){
          $string = "\"{$string}\"";

        }

        $csv .= $string.",";
      }
      else if(isset($system->{$propertie})) {
        if($propertie == "affiliation")
          $csv .= $system->{$propertie}[0]->name.",";
        else if($propertie == "thumbnail")
          $csv .= $system->{$propertie}->source.",";
      }
      else {
        $csv .= "null,";
      }

    }

    $csv .= "\n";

    return $csv;
  }

  private function do_headers($system) {
    $i = 0;
    $csv = "";
    foreach($system as $properties => $val) {
      $csv .= $properties.",";
      $this->col_order[] = (string) $properties;
      $i++;
    }
    $csv .= "\n";
    $this->set_headers = true;
    return $csv;
  }
}
require_once("../../api/RSI.php");
$d = new Galaxy();
