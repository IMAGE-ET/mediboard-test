<?php /* $Id: CMbCalendar.class.php $ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision: 12920 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireLibraryFile("iCalcreator/iCalcreator.class");

class CMbCalendar extends vcalendar {
	private $calendrier=null;
	
  function __construct(){
		//Création de l'icalendar
		parent::__construct();
		
		//Ajout de quelques proporiétés
		$this->setProperty( 'method', 'PUBLISH' ); // required of some calendar software
		$this->setProperty( "x-wr-calname", "Calendar Sample" );
		$this->setProperty( "X-WR-CALDESC", "Calendar Description" );
		$this->setProperty( "X-WR-TIMEZONE", "Europe/London" );
		
  }
  
  //fonction permettant de créer un evènement de calendrier de façon simplifiée
  function addevent($lieu, $summary, $description, $comment,$guid,
      $start_year,$start_month,$start_day,$start_hour,$start_min,
      $finish_year,$finish_month,$finish_day,$finish_hour,$finish_min )
  {
    $vevent = $this->newComponent( 'vevent' );
    $start = array( 'year'=>$start_year, 'month'=>$start_month, 'day'=>$start_day, 'hour'=>$start_hour, 'min'=>$start_min, 'sec'=>0 );
    $vevent->setProperty( 'dtstart', $start );
    $end = array( 'year'=>$finish_year, 'month'=>$finish_month, 'day'=>$finish_day, 'hour'=>$finish_hour, 'min'=>$finish_min, 'sec'=>0 );
    $vevent->setProperty( 'dtend', $end );
    $vevent->setProperty( 'LOCATION', $lieu );
    $vevent->setProperty( 'UID', $guid );
    $vevent->setProperty( 'summary', $summary );
    $vevent->setProperty( 'description',$description );
    $vevent->setProperty( 'comment', $comment );
    $this->setComponent ( $vevent ,$guid ); // add event to calendar
  }
}
?>