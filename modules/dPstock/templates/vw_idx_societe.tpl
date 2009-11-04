{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="main">
  <tr>
    <td class="halfPane" rowspan="2">
      <a class="button new" href="?m=dPstock&amp;tab=vw_idx_societe&amp;societe_id=0">{{tr}}CSociete.create{{/tr}}</a>
      <table class="tbl">
        <tr>
          <th>{{tr}}CSociete-name{{/tr}}</th>
          <th>{{tr}}CSociete-contact_name{{/tr}}</th>
          <th>{{tr}}CSociete-address{{/tr}}</th>
          <th>{{tr}}CSociete-phone{{/tr}}</th>
          <th>{{tr}}CSociete-email{{/tr}}</th>
        </tr>
        {{foreach from=$list_societes item=curr_societe}}
        <tr {{if $curr_societe->_id == $societe->_id}}class="selected"{{/if}}>
          <td class="text">
            <a href="?m=dPstock&amp;tab=vw_idx_societe&amp;societe_id={{$curr_societe->_id}}" title="{{tr}}CSociete.modify{{/tr}}">
              {{$curr_societe->_view}}
            </a>
          </td>
          <td class="text">{{mb_value object=$curr_societe field=contact_name}} {{mb_value object=$curr_societe field=contact_surname}}</td>
          <td class="text">
            {{$curr_societe->address|nl2br}}<br />{{mb_value object=$curr_societe field=postal_code}} {{mb_value object=$curr_societe field=city}}
          </td>
          <td>{{mb_value object=$curr_societe field=phone}}</td>
          <td><a href="mailto:{{$curr_societe->email}}">{{$curr_societe->email}}</a></td>
        </tr>
        {{/foreach}}       
        
      </table>
    </td>
    <td class="halfPane">
      {{if $can->edit}}
			{{mb_include_script module="dPpatients" script="autocomplete"}}
			<script type="text/javascript">
			Main.add(function () {
			  InseeFields.initCPVille("edit_societe", "postal_code", "city", "phone");
			});
			</script>

      <form name="edit_societe" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

      <input type="hidden" name="dosql" value="do_societe_aed" />
	    <input type="hidden" name="societe_id" value="{{$societe->_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $societe->_id}}
          <th class="title modify" colspan="2">{{tr}}CSociete.modify{{/tr}} {{$societe->_view}}</th>
          {{else}}
          <th class="title" colspan="2">{{tr}}CSociete.create{{/tr}}</th>
          {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$societe field="name"}}</th>
          <td>{{mb_field object=$societe field="name"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$societe field="address"}}</th>
          <td>{{mb_field object=$societe field="address"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$societe field="postal_code"}}</th>
          <td>{{mb_field object=$societe field="postal_code"}}</td>
        </tr>
        <tr> 
          <th>{{mb_label object=$societe field="city"}}</th>
          <td>{{mb_field object=$societe field="city"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$societe field="phone"}}</th>
          <td>{{mb_field object=$societe field="phone"}}</td>
        </tr>
         <tr>
          <th>{{mb_label object=$societe field="fax"}}</th>
          <td>{{mb_field object=$societe field="fax"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$societe field="siret"}}</th>
          <td>{{mb_field object=$societe field="siret"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$societe field="email"}}</th>
          <td>{{mb_field object=$societe field="email"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$societe field="contact_name"}}</th>
          <td>{{mb_field object=$societe field="contact_name"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$societe field="contact_surname"}}</th>
          <td>{{mb_field object=$societe field="contact_surname"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="4">
            {{if $societe->_id}}
            <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
            <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$societe->_view|smarty:nodefaults|JSAttribute}}'})">
              {{tr}}Delete{{/tr}}
            </button>
            {{else}}
            <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
            {{/if}}
          </td>
        </tr> 
      </table>
      </form>
      {{/if}}
      
      {{if $societe->_id}}
      <button class="new" type="button" onclick="window.location='?m=dPstock&amp;tab=vw_idx_reference&amp;reference_id=0&amp;societe_id={{$societe->_id}}'">
        {{tr}}CProductReference.create{{/tr}}
      </button>
      <table class="tbl">
        <tr>
          <th class="title" colspan="4">{{tr}}CSociete-back-product_references{{/tr}}</th>
        </tr>
        <tr>
           <th>{{tr}}CProduct{{/tr}}</th>
           <th>{{tr}}CProductReference-quantity{{/tr}}</th>
           <th>{{tr}}CProductReference-price{{/tr}}</th>
           <th>{{tr}}CProductReference-_unit_price{{/tr}}</th>
         </tr>
         {{foreach from=$societe->_ref_product_references item=curr_reference}}
         <tr>
           <td><a href="?m={{$m}}&amp;tab=vw_idx_reference&amp;reference_id={{$curr_reference->_id}}" title="{{tr}}CProductReference.modify{{/tr}}">{{$curr_reference->_ref_product->_view}}</a></td>
           <td>{{mb_value object=$curr_reference field=quantity}}</td>
           <td>{{mb_value object=$curr_reference field=price}}</td>
           <td>{{mb_value object=$curr_reference field=_unit_price}}</td>
         </tr>
         {{foreachelse}}
         <tr>
           <td class="button" colspan="4">{{tr}}CProductReference.none{{/tr}}</td>
         </tr>
         {{/foreach}}
       </table>
      <button class="new" type="button" onclick="window.location='?m=dPproduct&amp;tab=vw_idx_product&amp;product_id=0&amp;societe_id={{$societe->_id}}'">
        {{tr}}CProduct.create{{/tr}}
      </button>
      <table class="tbl">
        <tr>
          <th class="title" colspan="3">{{tr}}CSociete-back-products{{/tr}}</th>
        </tr>
        <tr>
           <th>{{tr}}CProduct-name{{/tr}}</th>
           <th>{{tr}}CProduct-description{{/tr}}</th>
           <th>{{tr}}CProduct-code{{/tr}}</th>
         </tr>
         {{foreach from=$societe->_ref_products item=curr_product}}
         <tr>
           <td><a href="?m={{$m}}&amp;tab=vw_idx_product&amp;product_id={{$curr_product->_id}}" title="{{tr}}CProduct.create{{/tr}}">{{$curr_product->_view}}</a></td>
           <td>{{mb_value object=$curr_product field=description}}</td>
           <td>{{mb_value object=$curr_product field=code}}</td>
         </tr>
         {{foreachelse}}
         <tr>
           <td class="button" colspan="3">{{tr}}CProduct.none{{/tr}}</td>
         </tr>
         {{/foreach}}
       </table>
    </td>
  </tr>
  {{/if}}
</table>