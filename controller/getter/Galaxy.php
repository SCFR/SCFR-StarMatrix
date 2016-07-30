<?php namespace SCFR\StarMatrix\controller\getter;
class Galaxy {

  private $systems;
  private $rsi_api;

  function __construct() {
    $this->rsi_api = \SCFR\StarMatrix\api\RSI::get_api();

    $this->rsi_api->LoginRSI();
    $raw_data = $this->rsi_api->get_api_data("https://robertsspaceindustries.com/api/starmap/bootup");

    $this->parse_raw_data($raw_data);
  }

  private function parse_raw_data($raw_data) {
    $galaxy = new \SCFR\StarMatrix\Item\Galaxy($raw_data);
    print_r($galaxy->get_systems());
  }
}
?>
