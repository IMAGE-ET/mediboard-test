{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $element->_class_name == "CDMI"}}
  {{assign var=dosql value="do_dmi_aed"}}
  {{assign var=category_class value="CDMICategory"}}
{{/if}}
{{if $element->_class_name == "CDM"}}
  {{assign var=dosql value="do_dm_aed"}}
  {{assign var=category_class value="CCategoryDM"}}
{{/if}}

<button type="button" class="new" onclick="viewElement('{{$element->_class_name}}', '0')">
  {{tr}}{{$element->_class_name}}-title-create{{/tr}}
</button>

<script type="text/javascript">

// refresh apres une sauvegarde ou une suppression
refreshElement = function(element_id){
  viewListElement('{{$element->_class_name}}', element_id);
  viewElement('{{$element->_class_name}}', element_id);
}

{{if $element->_class_name == "CDM"}}
  generateCode = function(){
	  var oForm = getForm("editElement-{{$element->_class_name}}");
	  var url = new Url("dmi", "httpreq_edit_element");
	  url.addParam("generate_code", true);
	  url.addParam("category_dm_id", oForm.category_dm_id.value);	  
	  url.addParam("nom", oForm.nom.value);
  	url.addParam("description", oForm.description.value);
	  url.addParam("in_livret", $V(oForm.in_livret));
	  url.requestUpdate("edit_CDM");
	}
{{/if}}

printBarcode = function (object_id) {
	var url = new Url("dPstock", "print_reception_barcodes");
	url.addParam("lot_id", object_id);
	url.addParam("suppressHeaders", 1 );
	url.popup(800, 800);
}

Main.add(function () {
  var oForm = getForm("editElement-{{$element->_class_name}}");
  new BarcodeParser.inputWatcher(oForm.elements._product_code, {field: "ref"});
  new BarcodeParser.inputWatcher(oForm.elements._scc_code, {field: "scc_prod"});
  
  var lotForm = getForm("create-lot-{{$element->_class_name}}");
  if (lotForm) {
    var codeInput = lotForm.elements.code;
    BarcodeParser.watchInput(codeInput, {
      field: "lot",
      onAfterRead: function(parsed){
        var dateView = "";
        if (parsed.comp.per) {
          dateView = Date.fromDATE(parsed.comp.per).toLocaleDate();
        }
        codeInput.form.lapsing_date.value = dateView;
      }
    });
  }
  
  {{if $element->_class_name == "CDM"}}
    var url = new Url("dPmedicament", "httpreq_do_medicament_autocomplete");
    url.autoComplete(oForm.elements.produit, "produit_auto_complete", {
      minChars: 3,
      updateElement: function(selected) {
        var dn = selected.childElements();
        $V(oForm.code, dn[0].innerHTML);
        $V(oForm.nom, dn[3].innerHTML.stripTags().strip());
      },
      callback: function(input, queryString){
        return queryString + "&hors_specialite=1"; 
      }
    });
  {{else}}
    var url = new Url("system", "httpreq_field_autocomplete");
    url.addParam("class", "CProduct");
    url.addParam("field", "product_id");
    url.addParam("limit", 30);
    url.addParam("view_field", "name");
    url.addParam("show_view", true);
    url.addParam("input_field", "product_id_autocomplete_view");
    url.autoComplete(oForm.elements.product_id_autocomplete_view, "product_id_autocomplete", {
      minChars: 3,
      method: "get",
      select: "view",
      dropdown: true,
      afterUpdateElement: function(field,selected){
        $V(field.form["product_id"], selected.getAttribute("id").split("-")[2]);
        if ($V(field.form.elements.nom) == "") {
          $V(field.form.elements.nom, selected.down('.view').innerHTML);
        }
      }
    });
  {{/if}}
});

</script>

<form name="editElement-{{$element->_class_name}}" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="{{$dosql}}" />
  <input type="hidden" name="{{$element->_spec->key}}" value="{{$element->_id}}" />
  <input type="hidden" name="callback" value="refreshElement" />
  
  <table class="form">
  	<tr>
  		<th class="title text {{if $element->_id}}modify{{/if}}" colspan="10">
  			{{if $element->_id}}
	      	{{tr}}{{$element->_class_name}}-title-modify{{/tr}} '{{$element->_view}}'
				{{else}}
				  {{tr}}{{$element->_class_name}}-title-create{{/tr}}
				{{/if}}
  		</th>
  	</tr>
  	{{if $element->_class_name == "CDMI"}}
    <tr>
      <th>{{mb_label object=$element field=category_id}}</th>
      <td>
        <select name="category_id" class="{{$element->_props.category_id}}">
          <option value="">&mdash; Choisir une catégorie</option>
          {{foreach from=$categories item=_category}}
          <option value="{{$_category->_id}}" {{if $_category->_id == $element->category_id}}selected="selected"{{/if}}>
            {{$_category->_view}}
          </option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    {{if !$element->_id}}
      <tr>
        <td colspan="2">
          <div class="small-info text">
            Pour créer un produit associé au DMI (recommandé s'il n'existe pas dans la liste "Produit"), veuillez renseigner le code
            ci-dessous. Sinon choisissez le dans la liste. 
          </div>
        </td>
      </tr>
      <tr>
        <th>{{mb_label object=$element field=code}}</th>
        <td>{{mb_field object=$element field=code}}</td>
      </tr>
    {{/if}}
    <tr>
      <th>{{mb_label object=$element field=product_id}}</th>
      <td>
        {{mb_field object=$element field=product_id hidden=true}}
        <input type="text" name="product_id_autocomplete_view" value="{{$element->_ref_product}}" class="autocomplete" size="45" />
        <div class="autocomplete" id="product_id_autocomplete" style="display:none;"></div>
      </td>
    </tr>
    {{/if}}
    {{if $element->_class_name == "CDM"}}
    <tr>
      <th>Recherche de DM</th>
      <td>
        <input type="text" name="produit" />
        <div class="autocomplete" id="produit_auto_complete" style="display:none;"></div>
      </td>
    </tr>
    <tr>
      <th>{{mb_label object=$element field=category_dm_id}}</th>
      <td>
        <select name="category_dm_id" class="{{$element->_props.category_dm_id}}">
          <option value="">&mdash; Choisir une catégorie</option>
          {{foreach from=$categories item=_category}}
          <option value="{{$_category->_id}}" {{if $_category->_id == $element->category_dm_id}}selected="selected"{{/if}}>
            {{$_category->_view}}
          </option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    {{/if}}
  	<tr>
  		<th>{{mb_label object=$element field=nom}}</th>
  		<td>{{mb_field object=$element field=nom size=45}}</td>
  	</tr>
    {{if $element->_class_name == "CDMI"}}
    <tr>
      <th>{{mb_label object=$element field=_product_code}}</th>
      <td>{{mb_field object=$element field=_product_code}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$element field=_scc_code}}</th>
      <td>{{mb_field object=$element field=_scc_code}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$element field=code_lpp}}</th>
      <td>{{mb_field object=$element field=code_lpp}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$element field=type}}</th>
      <td>{{mb_field object=$element field=type}}</td>
    </tr>
    {{/if}}
  	<tr>
  		<th>{{mb_label object=$element field=description}}</th>
  		<td>{{mb_field object=$element field=description}}</td>
  	</tr>
    {{* 
  	<tr>
  		<th>{{mb_label object=$element field=code}}</th>
  		<td>
  		  {{if !$element->_id}}
  		    {{if $element->_class_name == "CDM"}}
  		      {{mb_field object=$element field=code readonly="readonly"}}
  		      <button type="button" class="tick" onclick="generateCode();">Générer</button>
  		    {{else}}
  		      {{mb_field object=$element field=code}}
  		    {{/if}}
  		  {{else}}
  		    {{mb_value object=$element field=code}}
          <button type="button" class="search notext" onclick="location.href='?m=dPstock&amp;tab=vw_idx_product&amp;product_id={{$element->_ext_product->_id}}'"></button>
  		  {{/if}}
  		</td>
  	</tr>
    *}}
  	<tr>
  		<th>{{mb_label object=$element field=in_livret}}</th>
  		<td>
  			{{assign var=elt_class value=$element->_class_name}}
  			{{if $dPconfig.dmi.$elt_class.product_category_id}}
				  {{if !$element->_id}}
		  		  {{mb_field object=$element field=in_livret}}
		  	  {{else}}
		  	    {{mb_value object=$element field=in_livret}}
		  	  {{/if}}
				{{else}}
				  <div class="small-warning">
				  	Les {{tr}}{{$elt_class}}{{/tr}} ne sont liés à aucune catégorie
				  </div>
				{{/if}}
	  	</td>
  	</tr>
  	<tr>
		  <td colspan="2" class="button">
		  	{{if $element->_id}}
          <button type="submit" class="submit" onclick="return onSubmitFormAjax(this.form, { onComplete: viewListElement.curry('{{$element->_class_name}}','{{$element->_id}}') } );">
            {{tr}}Save{{/tr}}
          </button>
		  	  <button type="button" class="trash" onclick="this.form.del.value = 1; return onSubmitFormAjax(this.form, { onComplete: refreshElement.curry('0') } )">
		  	    {{tr}}Delete{{/tr}}
          </button>
			  {{else}}
				  <button type="submit" class="submit">
				    {{tr}}Save{{/tr}}
          </button>
			  {{/if}}
		  </td>
		</tr>
  </table>
</form>

{{if $element->_ref_product && $element->_ref_product->_id}}

{{assign var=class_name value=$element->_class_name}}

<script type="text/javascript">
  refreshDMI = function(lot_id) {
    refreshElement({{$element->_id}});
  }
  
  deleteLot = function(lot_id) {
    var form = getForm('delete-lot-{{$class_name}}');
    $V(form.order_item_reception_id, lot_id);
    confirmDeletion(form, {typeName:'', objName:'ce lot', ajax: true});
  }
</script>

<form name="delete-lot-{{$class_name}}" action="" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="dPstock" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_order_item_reception_aed" />
  <input type="hidden" name="del" value="1" />
  <input type="hidden" name="callback" value="refreshDMI" />
  {{mb_key object=$lot}}
</form>

<form name="create-lot-{{$class_name}}" action="" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="dPstock" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_order_item_reception_aed" />
  <input type="hidden" name="date" value="now" />
  <input type="hidden" name="callback" value="refreshDMI" />
  
<table class="main tbl">
  <tr>
    <th colspan="10" class="title">Lots</th>
  </tr>
  <tr>
    <th style="width: 0.1%;"></th>
    <th>{{mb_title class=CProductOrderItemReception field=quantity}}</th>
    <th>{{mb_title class=CProductOrderItemReception field=date}}</th>
    <th>{{mb_title class=CProductOrderItemReception field=code}}</th>
    <th>{{mb_title class=CProductOrderItemReception field=lapsing_date}}</th>
    <th style="width: 0.1%;"></th>
  </tr>
  
  <tr>
    <td></td>
    <td>{{mb_field object=$lot field=quantity increment=true form="create-lot-$class_name" size=1}}</td>
    <td>
      <select name="_reference_id" class="notNull" style="width: 12em;">
        {{if $element->_ref_product->_ref_references|@count}}
          <optgroup label="Références">
            {{foreach from=$element->_ref_product->_ref_references item=_reference}}
              <option value="{{$_reference->_id}}" selected="selected">{{$_reference->_ref_societe}} ({{$_reference->quantity}})</option>
            {{/foreach}}
          </optgroup>
        {{/if}}
        
        <optgroup label="Fournisseurs">
          <option value="" disabled="disabled" {{if !$element->_ref_product->_ref_references|@count}}selected="selected"{{/if}}> &ndash; Choisir un fournisseur</option>
          {{foreach from=$list_societes item=_societe}}
            <option value="{{$_societe->_id}}-{{$element->_ref_product->_id}}" {{if !$element->_ref_product->_ref_references|@count && $_societe->_id == $element->_ref_product->societe_id}}selected="selected"{{/if}}>{{$_societe}}</option>
          {{/foreach}}
        </optgroup>
      </select>
    </td>
    <td>{{mb_field object=$lot field=code}}</td>
    <td>{{mb_field object=$lot field=lapsing_date prop="str mask|99/99/9999" size=10}}</td>
    <td>
      <button type="submit" class="tick notext"></button>
    </td>
  </tr>
  
  {{foreach from=$element->_ref_product->_ref_lots item=_lot}}
    <tr>
      <td><button type="button" class="trash notext" onclick="deleteLot({{$_lot->_id}})"></button></td>
      <td>{{mb_value object=$_lot field=quantity}}</td>
      <td>{{mb_value object=$_lot field=date}}</td>
      <td>{{mb_value object=$_lot field=code}}</td>
      <td>{{mb_value object=$_lot field=lapsing_date}}</td>
      <td>
        <button type="button" class="barcode notext" onclick="printBarcode('{{$_lot->_id}}');"></button>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="10">{{tr}}CProductOrderItemReception.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>

</form>
{{/if}}