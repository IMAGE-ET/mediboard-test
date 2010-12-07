{{* $Id: vw_idx_delivrance.tpl 9733 2010-08-04 14:03:11Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision: 9733 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{main}}
   $("table-loss").gridHighlight();
{{/main}}

<table class="main tbl" id="table-loss">
  <tr>
    <th></th>
    {{foreach from=$dates item=_date}}
      <th>{{$_date|date_format:"%d/%m"}}</th>
    {{/foreach}}
  </tr>
  
{{foreach from=$table item=_values key=_service_id}}
  <tr>
    <th>{{$services.$_service_id}}</th>
    {{foreach from=$_values key=_date item=v}}
      <td>{{$v}}</td>
    {{/foreach}}
  </tr>
{{/foreach}}

</table>