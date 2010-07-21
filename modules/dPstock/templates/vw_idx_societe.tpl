{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


<script type="text/javascript">
function refreshSocietesList(){
  var url = new Url("dPstock", "httpreq_vw_societes_list");
  url.addFormData("filterSociete");
  url.requestUpdate("list-societe");
  return false;
}

function changePage(page){
  $V(getForm("filterSociete").start, page);
}

Main.add(function(){
  refreshSocietesList();
  Control.Tabs.create("societe-tabs", true);

  var editForm = getForm("edit_societe");
  BarcodeParser.watchInput(editForm.manufacturer_code, {size: 10, field: "scc_manuf"});
});

</script>

{{mb_include_script module=dPstock script=barcode}}

<table class="main">
  <tr>
    <td class="halfPane">
      <form name="filterSociete" method="get" action="" onsubmit="return refreshSocietesList()">
        <input type="hidden" name="start" value="0" onchange="this.form.onsubmit()" />
        
        <input type="text" name="keywords" value="" onchange="$V(this.form.start, 0)" />
        <button type="submit" class="search notext">{{tr}}Filter{{/tr}}</button>
        
        <label>
          <input type="checkbox" name="suppliers" value="1" {{if $suppliers}}checked="checked"{{/if}}
                 onchange="$V(this.form.start, 0); this.form.onsubmit()" />
          Distributeurs
        </label>
        
        <label>
          <input type="checkbox" name="manufacturers" value="1" {{if $manufacturers}}checked="checked"{{/if}}
                 onchange="$V(this.form.start, 0); this.form.onsubmit()" />
          Fabricants
        </label>
        
        <label>
          <input type="checkbox" name="inactive" value="1" {{if $inactive}}checked="checked"{{/if}}
                 onchange="$V(this.form.start, 0); this.form.onsubmit()" />
          Sociétés inactives
        </label>
      </form>
    </td>
  </tr>
  <tr>
    <td id="list-societe"></td>
    <td class="halfPane" rowspan="2">
      <a class="button new" href="?m=dPstock&amp;tab=vw_idx_societe&amp;societe_id=0">{{tr}}CSociete-title-create{{/tr}}</a>
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
          <th class="title modify" colspan="2">{{tr}}CSociete-title-modify{{/tr}} {{$societe->_view}}</th>
          {{else}}
          <th class="title" colspan="2">{{tr}}CSociete-title-create{{/tr}}</th>
          {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$societe field="name"}}</th>
          <td>{{mb_field object=$societe field="name"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$societe field="code"}}</th>
          <td>{{mb_field object=$societe field="code"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$societe field="distributor_code"}}</th>
          <td>{{mb_field object=$societe field="distributor_code"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$societe field="customer_code"}}</th>
          <td>{{mb_field object=$societe field="customer_code"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$societe field="manufacturer_code"}}</th>
          <td>
            {{mb_field object=$societe field="manufacturer_code"}}
             <span style="display: none; color: red;">Ce n'est pas un code valide</span>
          </td>
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
        {{* <tr>
          <th>{{mb_label object=$societe field="departments"}}</th>
          <td>{{mb_field object=$societe field="departments"}}</td>
        </tr>
         *}}
        <tr>
          <th>{{mb_label object=$societe field="carriage_paid"}}</th>
          <td>{{mb_field object=$societe field="carriage_paid"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$societe field="delivery_time"}}</th>
          <td>{{mb_field object=$societe field="delivery_time"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="4">
            {{if $societe->_id}}
            <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
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
      <ul class="control_tabs" id="societe-tabs">
        <li><a href="#societe-references" class="{{if $societe->_ref_product_references|@count == 0}}empty{{/if}}">{{tr}}CSociete-back-product_references{{/tr}} <small>({{$societe->_ref_product_references|@count}})</small></a></li>
        <li><a href="#societe-products" class="{{if $societe->_ref_products|@count == 0}}empty{{/if}}">{{tr}}CSociete-back-products{{/tr}} <small>({{$societe->_ref_products|@count}})</small></a></li>
      </ul>
      <hr class="control_tabs" />
      
      <div id="societe-references" style="display: none;">
        <button class="new" type="button" onclick="window.location='?m=dPstock&amp;tab=vw_idx_reference&amp;reference_id=0&amp;societe_id={{$societe->_id}}'">
          {{tr}}CProductReference-title-create{{/tr}}
        </button>
        <table class="tbl">
          <tr>
            <th>{{mb_title class=CProductReference field=product_id}}</th>
            <th>{{mb_title class=CProductReference field=supplier_code}}</th>
            <th>{{mb_title class=CProductReference field=quantity}}</th>
            <th>{{mb_title class=CProductReference field=price}}</th>
            <th>{{mb_title class=CProductReference field=_cond_price}}</th>
          </tr>
          {{foreach from=$societe->_ref_product_references item=curr_reference}}
          <tr>
            <td>
              <a href="?m={{$m}}&amp;tab=vw_idx_reference&amp;reference_id={{$curr_reference->_id}}">
                {{mb_value object=$curr_reference field=product_id}}
              </a>
            </td>
            <td>{{mb_value object=$curr_reference field=supplier_code}}</td>
            <td>{{mb_value object=$curr_reference field=quantity}}</td>
            <td>{{mb_value object=$curr_reference field=price}}</td>
            <td>{{mb_value object=$curr_reference field=_cond_price}}</td>
          </tr>
          {{foreachelse}}
          <tr>
            <td colspan="10">{{tr}}CProductReference.none{{/tr}}</td>
          </tr>
          {{/foreach}}
        </table>
      </div>
      
      <div id="societe-products" style="display: none;">
        <button class="new" type="button" onclick="window.location='?m=dPstock&amp;tab=vw_idx_product&amp;product_id=0&amp;societe_id={{$societe->_id}}'">
          {{tr}}CProduct-title-create{{/tr}}
        </button>
        <table class="tbl">
          <tr>
            <th>{{tr}}CProduct-name{{/tr}}</th>
            <th>{{tr}}CProduct-description{{/tr}}</th>
            <th>{{tr}}CProduct-code{{/tr}}</th>
          </tr>
          {{foreach from=$societe->_ref_products item=curr_product}}
          <tr>
            <td>
              <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_product->_guid}}')">
                {{$curr_product->_view}}
              </span>
            </td>
            <td>{{mb_value object=$curr_product field=description}}</td>
            <td>{{mb_value object=$curr_product field=code}}</td>
          </tr>
          {{foreachelse}}
          <tr>
            <td colspan="3">{{tr}}CProduct.none{{/tr}}</td>
          </tr>
          {{/foreach}}
        </table>
      </div>
    </td>
  </tr>
  {{/if}}
</table>