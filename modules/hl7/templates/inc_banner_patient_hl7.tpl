{{*
 * $Id$
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{if $patient->_id}}
  <table class="tbl">
    <tr>
      <th class="title">
        Patient sélectionné :<br/>
        {{$patient->_view}} {{mb_value object=$patient field=naissance}} [{{if $patient->_IPP}}{{$patient->_IPP}}{{else}}-{{/if}}]
        <button type="button" class="search" onclick="TestHL7.selectPatient('{{$patient->_id}}')">{{tr}}Continue{{/tr}}</button>
      </th>
    </tr>
  </table>
{{/if}}