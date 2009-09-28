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

<script type="text/javascript">

// refresh apres une sauvegarde ou une suppression
refreshElement = function(element_id){
  viewListElement('{{$category_class}}', element_id);
  viewElement('{{$element_class}}', element_id);
}

{{if $element->_class_name == "CDM"}}
  generateCode = function(){
	  var oForm = document.forms["editElement-{{$element->_class_name}}"];
	  var url = new Url;
	  url.setModuleAction("dmi", "httpreq_edit_element");
	  url.addParam("generate_code", true);
	  url.addParam("category_dm_id", oForm.category_dm_id.value);	  
	  url.addParam("nom", oForm.nom.value);
  	url.addParam("description", oForm.description.value);
	  url.addParam("in_livret", $V(oForm.in_livret));
	  url.requestUpdate("edit_CDM", { waitingText: null } );
	}
{{/if}}

Main.add(function () {
  var oForm = document.forms["editElement-{{$element->_class_name}}"];
  prepareForm(oForm);
  updateFieldsDM = function(selected) {
    dn = selected.childElements();
    $V(oForm.code, dn[0].innerHTML);
    $V(oForm.nom, dn[3].innerHTML.stripTags().strip());
  }
  if($('produit_auto_complete')){
	  urlAuto = new Url();
	  urlAuto.setModuleAction("dPmedicament", "httpreq_do_medicament_autocomplete");
	  urlAuto.autoComplete(oForm.produit, "produit_auto_complete", {
	    minChars: 3,
	    updateElement: updateFieldsDM,
      callback: 
        function(input, queryString){
          return (queryString + "&hors_specialite=1"); 
        }
    } );
  }
});

</script>

<form name="editElement-{{$element->_class_name}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="{{$dosql}}" />
  <input type="hidden" name="{{$element->_spec->key}}" value="{{$element->_id}}" />
  {{if !$element->_id}}
  <input type="hidden" name="callback" value="refreshElement" />
  {{/if}}
  <table class="form">
  	<tr>
  		<th class="category {{if $element->_id}}modify{{/if}}" colspan="10">
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
      <th>
        Recherche de DM
      </th>
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
  		<td>{{mb_field object=$element field=nom}}</td>
  	</tr>
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
  		      <button type="button"  class="tick" onclick="generateCode();">Générer</button>
  		    {{else}}
  		      {{mb_field object=$element field=code}}
  		    {{/if}}
  		  {{else}}
  		    {{mb_value object=$element field=code}}
  		  {{/if}}
  		</td>
  	</tr>
  	<tr>
  		<th>{{mb_label object=$element field=in_livret}}</th>
  		<td>
	  		{{if !$element->_id}}
	  		  {{mb_field object=$element field=in_livret}}
	  	  {{else}}
	  	    {{mb_value object=$element field=in_livret}}
	  	  {{/if}}
	  	</td>
  	</tr>
  	<tr>
		  <td colspan="2" class="button">
		  	{{if $element->_id}}
		  	  <button type="button" class="trash" onclick="this.form.del.value = 1; submitFormAjax(this.form, 'systemMsg', { onComplete: function() { refreshElement('0') } } )">{{tr}}Delete{{/tr}}</button>
			    <button type="button" class="submit" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function(){ viewListElement('{{$category_class}}','{{$element->_id}}') } } );">{{tr}}Modify{{/tr}}</button>
			  {{else}}
				  <button type="button" class="submit" onclick="submitFormAjax(this.form, 'systemMsg');">{{tr}}Save{{/tr}}</button>
			  {{/if}}
		  </td>
		</tr>
  </table>
</form>