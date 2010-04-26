{{* $Id:  $ *}}

{{*
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  <tr>
    <th class="title">Prescriptions ({{$prescriptions|@count}})</th>
  </tr>
  {{foreach from=$prescriptions item=_prescription}}
  <tr>
    <td class="text">
      <a href="#{{$_prescription->_id}}" onclick="loadSejour('{{$_prescription->object_id}}'); Prescription.reloadPrescSejour('{{$_prescription->_id}}','','','','','','');">
      {{$_prescription->_ref_patient->_view}}
      </a>
    </td>
  </tr>
  {{/foreach}}
</table>