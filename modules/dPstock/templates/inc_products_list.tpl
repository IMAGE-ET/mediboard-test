{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include module=system template=inc_pagination change_page="changePage" 
    total=$total current=$start step=16}}

<table class="tbl">
  <tr>
    <th colspan="4">{{mb_title class=CProduct field=name}}</th>
  </tr>
  <tr>
    <th>{{mb_title class=CProduct field=societe_id}}</th>
    <th>{{mb_title class=CProduct field=code}}</th>
    <th>{{mb_title class=CProduct field=_quantity}}</th>
    <th>{{mb_title class=CProduct field=packaging}}</th>
  </tr>
  {{foreach from=$list_products item=curr_product}}
    <tbody class="hoverable">
    <tr {{if $curr_product->_id == $product_id}}class="selected"{{/if}}>
      <td colspan="4">
        <a href="?m={{$m}}&amp;tab=vw_idx_product&amp;product_id={{$curr_product->_id}}">
          {{$curr_product->name|truncate:90}}
        </a>
      </td>
    </tr>
    <tr {{if $curr_product->_id == $product_id}}class="selected"{{/if}}>
      <td style="padding-left: 2em;">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_product->_ref_societe->_guid}}')">
          {{$curr_product->_ref_societe}}
        </span>
      </td>
      <td>{{$curr_product->code}}</td>
      <td title="{{$curr_product->_quantity}}">
        {{$curr_product->_quantity|truncate:35}}
      </td>
      <td>{{$curr_product->packaging}}</td>
    </tr>
    </tbody>
  {{foreachelse}}
    <tr>
      <td colspan="6">{{tr}}CProduct.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>