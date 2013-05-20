<?php 

/**
 * Day of the month
 *  
 * @category Classes
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:\$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CMbDay
 * used to manage a day of the year
 */
class CMbDay {

  public $date;         // YYYY-MM-DD
  public $number;       // day nb of the year
  public $name;         // local name of the day. ex: Fête nationnale
  public $ferie;        // null|string (if set, the day is an holiday one.

  /**
   * constructor
   *
   * @param string $date date chosen
   */
  public function __construct($date = null) {
    if (!$date) {
      $date = CMbDT::date();
    }

    $this->date = $date;
    $this->number = (int) CMbDT::transform("", $date, "%j");
    $dateTmp = explode("-", $date);
    $this->name = CMbDate::$days_name[(int) $dateTmp[1]][(int) ($dateTmp[2]-1)];


    //jour férie ?
    $holidays = CMbDate::getHolidays($this->date);
    if (array_key_exists($this->date, $holidays)) {
      $this->ferie = $holidays[$this->date];
    }
  }
}