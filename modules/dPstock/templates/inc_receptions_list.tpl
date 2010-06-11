{{* $Id:  $ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7769 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include module=system template=inc_pagination current=$start change_page=changePage}}

<table class="main tbl">
  <tr>
    <th style="width: 1%">{{mb_title class=CProductReception field="reference"}}</th>
    <th>{{mb_title class=CProductReception field="societe_id"}}</th>
    <th>{{mb_title class=CProductReception field="date"}}</th>
    <th>Nombre d'elements</th>
    <th></th>
  </tr>
  {{foreach from=$receptions item=_reception}}
  <tr>
    <td>
      <strong onmouseover="ObjectTooltip.createEx(this, '{{$_reception->_guid}}')">
        {{mb_value object=$_reception field="reference"}}
      </strong>
    </td>
    <td>{{mb_value object=$_reception field="societe_id"}}</td>
    <td>{{mb_value object=$_reception field="date"}}</td>
    <td>{{$_reception->_count_reception_items}}</td>
    <td style="width: 1%">
      <button type="button" class="print" onclick="printReception('{{$_reception->_id}}');">Bon de réception</button>
    </td>
  </tr>
  {{/foreach}}
</table>