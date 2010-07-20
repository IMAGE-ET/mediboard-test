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
  viewListElement('{{$category_class}}', element_id);
  viewElement('{{$element_class}}', element_id);
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

Main.add(function () {
  var oForm = getForm("editElement-{{$element->_class_name}}");
  
  if(!$('produit_auto_complete') || !oForm.elements.produit) return;
  
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
  } );
});

</script>

<form name="editElement-{{$element->_class_name}}" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="{{$dosql}}" />
  <input type="hidden" name="{{$element->_spec->key}}" value="{{$element->_id}}" />
  {{if !$element->_id}}
  <input type="hidden" name="callback" value="refreshElement" />
  {{/if}}
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
  		<td>{{mb_field object=$element field=nom size=30}}</td>
  	</tr>
    {{if $element->_class_name == "CDMI"}}
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
          <button type="submit" class="submit" onclick="return onSubmitFormAjax(this.form, { onComplete: viewListElement.curry('{{$category_class}}','{{$element->_id}}') } );">
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

{{if $element->_ext_product && $element->_ext_product->_id}}
<table class="main tbl">
  <tr>
    <th colspan="10" class="title">Lots</th>
  </tr>
  <tr>
    <th>{{mb_title class=CProductOrderItemReception field=quantity}}</th>
    <th>{{mb_title class=CProductOrderItemReception field=date}}</th>
    <th>{{mb_title class=CProductOrderItemReception field=code}}</th>
    <th>{{mb_title class=CProductOrderItemReception field=lapsing_date}}</th>
  </tr>
  
  {{foreach from=$element->_ext_product->_ref_lots item=_lot}}
    <tr>
      <td>{{mb_value object=$_lot field=quantity}}</td>
      <td>{{mb_value object=$_lot field=date}}</td>
      <td>{{mb_value object=$_lot field=code}}</td>
      <td>{{mb_value object=$_lot field=lapsing_date}}</td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="10">{{tr}}CProductOrderItemReception.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>
{{/if}}