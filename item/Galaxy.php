<?php namespace SCFR\StarMatrix\Item;
class Galaxy extends Item {
  protected $systems_count;
  protected $tunnels_count;
  protected $species_count;
  protected $affiliations_count;

  function __construct($raw_data) {
    $this->raw_data = $raw_data;
    $proceed = true;
    try {
      $this->do_count("systems");
      $this->do_count("tunnels");
      $this->do_count("species");
      $this->do_count("affiliations");
    }
    catch(\Exception $e) {
      $proceed = false;
    }

    if($proceed) {
    }



  }

  public function get_systems() {
    if(isset($this->raw_data->systems) && isset($this->raw_data->systems->resultset))
      return $this->raw_data->systems->resultset;
    else return false;
  }

  private function do_count($name, $actual = "totalrows") {
    $prop_name = $name."_count";
    if(isset($this->raw_data->{$name}) && isset($this->raw_data->{$name}->{$actual}))
      $this->{$prop_name} = (integer) $this->raw_data->{$name}->{$actual};
    else throw new \Exception("Galaxy: No {$name}");
  }

}
?>
