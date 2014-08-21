<?php

/**
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Class for manage the cronjob
 */
class CCronJob extends CMbObject {

  /** @var integer Primary key */
  public $cronjob_id;
  public $name;
  public $description;
  public $active;
  public $params;
  public $execution;
  public $cron_login;
  public $cron_password;

  public $_frequently;
  public $_second;
  public $_minute;
  public $_hour;
  public $_day;
  public $_month;
  public $_week;

  public $_next_datetime = array();

  /** @var  \Cron\CronExpression */
  public $_cron_expression;

  public $_url;
  public $_params;

  /**
   * @see parent::getSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "cronjob";
    $spec->key    = "cronjob_id";
    return $spec;  
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    $backProps["cron_logs"] = "CCronJobLog cronjob_id";

    return $backProps;
  }

  /**
   * @see parent::getProps
   */
  function getProps() {
    $props = parent::getProps();

    $props["name"]          = "str notNull";
    $props["active"]        = "bool notNull default|1";
    $props["params"]        = "text notNull";
    $props["description"]   = "text";
    $props["execution"]     = "str notNull";
    $props["cron_login"]    = "str notNull maxLength|20";
    $props["cron_password"] = "password show|0 loggable|0";

    $props["_frequently"]     = "enum list|@yearly|@monthly|@weekly|@daily|@hourly";
    $props["_second"]       = "str";
    $props["_minute"]       = "str";
    $props["_hour"]         = "str";
    $props["_day"]          = "str";
    $props["_month"]        = "str";
    $props["_week"]         = "str";

    return $props;
  }

  /**
   * @see parent::updateFormFields
   */
  function updateFormFields() {
    parent::updateFormFields();

    $this->_view = $this->name;

    if (strpos($this->execution, "@") === 0) {
      $this->_frequently = $this->execution;
    }
    else {
      list($this->_second, $this->_minute, $this->_hour, $this->_day, $this->_month, $this->_week) = explode(" ", $this->execution);
    }

    $params = strtr($this->params, array("\r\n" => "&", "\n" => "&", " " => ""));
    parse_str($params, $this->_params);

  }

  /**
   * @see parent::check()
   */
  function check() {

    if ($msg = $this->checkExecutionLine()) {
      return $msg;
    }

    return parent::check();
  }

  /**
   * @see parent::store()
   */
  function store () {
    if ($this->cron_login && $this->cron_password === "") {
      $this->cron_password = null;
    }

    $parts = array(
      $this->_second !== null && $this->_second !== "" ? $this->_second : "0",
      $this->_minute !== null && $this->_minute !== "" ? $this->_minute : "*",
      $this->_hour   !== null && $this->_hour   !== "" ? $this->_hour   : "*",
      $this->_day    !== null && $this->_day    !== "" ? $this->_day    : "*",
      $this->_month  !== null && $this->_month  !== "" ? $this->_month  : "*",
      $this->_week   !== null && $this->_week   !== "" ? $this->_week   : "*"
    );

    if ($this->_frequently) {
      $this->execution = $this->_frequently;
    }
    else {
      $this->execution = implode(" ", $parts);
    }

    return parent::store();
  }

  /**
   * Verification of the line cron
   *
   * @return String|null
   */
  function checkExecutionLine() {
    //Durée - aucune vérification à effectuer
    if (strpos($this->execution, "@") === 0) {
      return null;
    }

    $parts = explode(" ", $this->execution);
    if (count($parts) !== 6) {
      return "Longueur de l'expression incorrecte";
    }

    foreach ($parts as $_index => $_part) {
      $virgules = explode(",", $_part);
      foreach ($virgules as $_virgule) {
        $slashs = explode("/", $_virgule);
        $count = count($slashs);
        if ($count > 2) {
          return "Plusieurs '/' détectées : $_part";
        }
        $left  = CMbArray::get($slashs, 0);
        $right = CMbArray::get($slashs, 1);
        if ($count == 2) {
          if (!$this->checkParts($_index, $right)) {
            return "Après '/' nombre obligatoire : $_part";
          }
        }

        $dashs = explode("-", $left);
        $count = count($dashs);
        if (count($dashs) > 2) {
          return "Plusieurs '-' détectées : $_part";
        }
        $left  = CMbArray::get($dashs, 0);
        $right = CMbArray::get($dashs, 1);
        if ($count == 2) {
          if (!$this->checkParts($_index, $right)) {
            return "Après '-' nombre obligatoire : $_part";
          }
          if ($right <= $left) {
            return "Borne collection incorrecte: $_part";
          }
        }

        if ($left !== "*" && !$this->checkParts($_index, $left)) {
          return "Nombre ou '*' erronée : $_part";
        }
      }
    }

    return null;
  }

  /**
   * Verify the part of the cron
   *
   * @param Integer $position part of the cron
   * @param String  $value    Value of the part
   *
   * @return bool
   */
  private function checkParts($position, $value) {
    $result = false;
    $regex = "#^[0-9]{1,2}$#";
    $min = 0;
    switch ($position) {
        //cas seconde
      case 0:
        //cas minute
      case 1:
        $max   = 60;
        break;
        //cas heure
      case 2:
        $max   = 23;
        break;
        //cas jour
      case 3:
        $min   = 1;
        $max   = 32;
        break;
        //cas mois
      case 4:
        $min   = 1;
        $max   = 13;
        break;
        //cas jour
      case 5:
        $max   = 8;
        $regex = "#^[0-7]{1}$#";
        break;
      default:
        return false;
    }

    if (preg_match($regex, $value)) {
      if ($value >= $min && $value < $max) {
        $result = true;
      }
    }

    return $result;
  }

  /**
   * Get the n futur execution
   *
   * @param int $next Nombre iteration of the cron job in the futur
   *
   * @return null|String[]
   */
  function getNextDate($next = 5) {
    $next_datetime = array();
    if (!$this->_cron_expression) {
      $this->getCronExpression();
    }

    try {
      for ($i = 0; $i < $next; $i++) {
        $next_datetime[] = $this->_cron_expression->getNextRunDate("now", $i, true)->format('Y-m-d H:i:s');
      }
    }
    catch (Exception $e) {
      return null;
    }

    return $this->_next_datetime = $next_datetime;
  }

  function getCronExpression() {
    if (!class_exists("\\Cron\\CronExpression", false)) {
      $this->loadLibrary();
    }

    return $this->_cron_expression = \Cron\CronExpression::factory($this->execution);
  }

  /**
   * Make the URL
   *
   * @return string
   */
  function makeUrl() {
    $base = CAppUI::conf("base_url");

    $this->_params["login"] = "$this->cron_login:$this->cron_password";

    $query = CMbString::toQuery($this->_params);
    $url = "$base/?$query";

    return $this->_url = $url;
  }

  /**
   * Load the library
   *
   * @return void
   */
  static function loadLibrary() {
    CAppUI::requireLibraryFile("cron-expression-1.0.3/src/Cron/FieldInterface");
    CAppUI::requireLibraryFile("cron-expression-1.0.3/src/Cron/AbstractField");
    CAppUI::requireLibraryFile("cron-expression-1.0.3/src/Cron/CronExpression");
    CAppUI::requireLibraryFile("cron-expression-1.0.3/src/Cron/DayOfMonthField");
    CAppUI::requireLibraryFile("cron-expression-1.0.3/src/Cron/DayOfWeekField");
    CAppUI::requireLibraryFile("cron-expression-1.0.3/src/Cron/FieldFactory");
    CAppUI::requireLibraryFile("cron-expression-1.0.3/src/Cron/SecondsField");
    CAppUI::requireLibraryFile("cron-expression-1.0.3/src/Cron/HoursField");
    CAppUI::requireLibraryFile("cron-expression-1.0.3/src/Cron/MinutesField");
    CAppUI::requireLibraryFile("cron-expression-1.0.3/src/Cron/MonthField");
    CAppUI::requireLibraryFile("cron-expression-1.0.3/src/Cron/YearField");
  }
}
