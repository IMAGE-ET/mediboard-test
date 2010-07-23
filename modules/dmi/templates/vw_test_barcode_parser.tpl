{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage {subpackage}
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="main tbl">
  {{foreach from=$results item=result key=barcode}}
  <tr>
    <td>{{$barcode}}</td>
    <td>{{$result.good|@mbExport}}</td>
    <td class="{{$result.ok|ternary:'ok':'error'}}">{{$result.parsed|@mbExport}}</td>
  </tr>
  {{/foreach}}
</table>