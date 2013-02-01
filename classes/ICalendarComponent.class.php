<?php

class ICalendarComponent {
  /**
   * @var array[]
   */
  public $properties = array();

  /**
   * @var ICalendarComponent[]
   */
  public $components = array();

  /**
   * @var ICalendar
   */
  private $calendar;

  /**
   * @var string
   */
  protected $type;

  static $types = array(
    "VALARM",
    "VEVENT",
    "VFREEBUSY",
    "VJOURNAL",
    "VTIMEZONE",
    "VTODO",
  );

  /**
   * Component constructor
   *
   * @param ICalendar $calendar Parent calendar
   * @param string    $type     Type
   */
  public function __construct(ICalendar $calendar, $type) {
    $this->calendar = $calendar;
    $this->type = $type;
  }

  /**
   * Get component type
   *
   * @return string
   */
  public function getType(){
    return $this->type;
  }

  /**
   * @param string $name
   *
   * @return array
   */
  public function getProperty($name){
    if (!isset($this->properties[$name])) {
      return null;
    }

    return $this->properties[$name];
  }

  public function getPropertyValue($name) {
    if (!isset($this->properties[$name])) {
      return null;
    }

    return $this->properties[$name]["value"];
  }

  /**
   * @param string $type
   *
   * @return ICalendarComponent[]
   */
  public function getComponents($type){
    if (!isset($this->components[$type])) {
      return array();
    }

    return $this->components[$type];
  }

  /**
   * @return string
   */
  public function __toString(){
    $str = "<ul>";

    // Properties
    $count = count($this->properties);
    if ($count) {
      $str .= "<li>Properties ($count)<ul>";

      foreach ($this->properties as $_name => $_value) {
        if (isset($_value["value"])) {
          $_val = $_value['value'];
          $str .= "<li><strong>$_name</strong>: $_val</li>\n";
        }
        else {
          $count = count($_value);
          $str .= "<li><strong>$_name</strong> ($count)<ul>";

          foreach ($_value as $_val) {
            $_val = $_val['value'];
            $str .= "<li>$_val</li>\n";
          }
          $str .= "</ul></li>";
        }
      }
      $str .= "</ul>";
    }

    // Components
    $count = count($this->components);
    if ($count) {
      $str .= "<li>Components ($count)<ul>";

      foreach ($this->components as $_type => $_components) {
        $str .= "<li><strong>$_type</strong><ul>";

        foreach ($_components as $_component) {
          $str .= $_component;
        }
        $str .= "</ul></li>";
      }
      $str .= "</ul></li>";
    }

    return "$str</ul>";
  }
}
