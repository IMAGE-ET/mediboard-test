{{* $Id: inc_references_list.tpl 7948 2010-01-30 18:54:06Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 *}}

<table class="tbl" id="tab-references" style="display: none;">
  <tr>
     <th style="width: 0.1%;">{{mb_title class=CProductReference field=code}}</th>
     <th>{{mb_title class=CProductReference field=societe_id}}</th>
     <th style="width: 0.1%">{{mb_title class=CProductReference field=price}}</th>
     <th colspan="2" style="width: 0.1%">{{mb_title class=CProductReference field=_cond_price}}</th>
     <th colspan="2" style="width: 0.1%">{{mb_title class=CProductReference field=_unit_price}}</th>
  </tr>
  
	{{foreach from=$product->_ref_references item=_reference}}
  {{assign var=_product value=$_reference->_ref_product}}
  <tr>
    <td {{if $_reference->cancelled}}class="cancelled"{{/if}}>
      <a href="?m=dPstock&amp;tab=vw_idx_reference&amp;reference_id={{$_reference->_id}}">
        <strong onmouseover="ObjectTooltip.createEx(this, '{{$_reference->_guid}}')">
          {{if $_reference->code}}
            {{mb_value object=$_reference field=code}}
          {{else}}
            [Aucun code]
          {{/if}}
        </strong>
      </a>
    </td>
    <td>
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_reference->_ref_societe->_guid}}')">
        {{mb_value object=$_reference field=societe_id}}
      </span>
    </td>

    <td style="text-align: right;">{{mb_value object=$_reference field=price}}</td>
    <td style="text-align: right;">
      <label title="{{$_reference->quantity}} {{$_product->packaging}}">
        {{mb_value object=$_reference field=quantity}} 
      </label>
      x
   </td>
    <td style="text-align: right;">{{mb_value object=$_reference field=_cond_price}}</td>
    <td style="text-align: right;">
      <label title="{{$_reference->quantity}} {{$_product->packaging}} x {{$_product->quantity}} {{$_product->item_title}}">
        {{mb_value object=$_reference field=_unit_quantity}} x
      </label>
     </td>
    <td style="text-align: right;"><strong>{{mb_value object=$_reference field=_unit_price}}</strong></td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="10">{{tr}}CProductReference.none{{/tr}}</td>
  </tr>
  {{/foreach}}
 </table>
