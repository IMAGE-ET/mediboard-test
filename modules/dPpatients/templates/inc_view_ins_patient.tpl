{{*
 * $Id$
 *  
 * @category DPpatients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}


{{if $patient->_count_ins}}
  <div style="float: right;">
    <img src="images/icons/carte_vitale.png"
         title="{{tr}}CPatient-date-lecture-vitale{{/tr}} : {{mb_value object=$patient field="date_lecture_vitale" format=relative}}"
         onclick="Patient.openINS({{$patient->_id}})"/>
  </div>
{{/if}}