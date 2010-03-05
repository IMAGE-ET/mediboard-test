{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include module=system template=inc_pagination change_page="changePage" 
    total=$total current=$start step=$dPconfig.dPstock.CProduct.pagination_size}}

<table class="tbl">
  <tr>
    <th colspan="10">{{mb_title class=CProduct field=name}}</th>
  </tr>
  <tr>
    <th style="width: 1%;">{{mb_title class=CProduct field=code}}</th>
    <th>{{mb_title class=CProduct field=societe_id}}</th>
    <th>{{mb_title class=CProduct field=quantity}}</th>
    <th>{{mb_label class=CProduct field=item_title}}</th>
    <th>{{mb_title class=CProduct field=packaging}}</th>
  </tr>
  {{foreach from=$list_products item=_product}}
    <tbody class="hoverable">
    <tr {{if $_product->_id == $product_id}}class="selected"{{/if}}>
      <td colspan="10" style="font-weight: bold;">
        <a href="?m={{$m}}&amp;tab=vw_idx_product&amp;product_id={{$_product->_id}}">
          {{$_product->name|truncate:80}}
        </a>
      </td>
    </tr>
    <tr {{if $_product->_id == $product_id}}class="selected"{{/if}}>
      <td style="padding-left: 1em;" {{if $_product->cancelled}}class="cancelled"{{/if}}>
        {{if $_product->code}}
          {{mb_value object=$_product field=code}}
        {{else}}
          &ndash;
        {{/if}}
			</td>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_product->_ref_societe->_guid}}')">
          {{$_product->_ref_societe}}
        </span>
      </td>
			<td style="text-align: right;">
				{{$_product->quantity}}
			</td>
      <td>
        {{$_product->item_title|spancate:25}}
      </td>
      <td>{{$_product->packaging}}</td>
    </tr>
    </tbody>
  {{foreachelse}}
    <tr>
      <td colspan="6">{{tr}}CProduct.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>