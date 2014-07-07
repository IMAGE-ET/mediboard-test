{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage 
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include template=inc_pref spec=str  var=VitaleVisionDir}}
{{mb_include template=inc_pref spec=bool var=VitaleVision}}
{{mb_include template=inc_pref spec=bool var=vCardExport}}
{{mb_include template=inc_pref spec=str  var=medecin_cps_pref}}
{{mb_include template=inc_pref spec=bool var=sort_atc_by_date}}
{{mb_include template=inc_pref spec=enum var=update_patient_from_vitale_behavior values="choice|never|always"}}
