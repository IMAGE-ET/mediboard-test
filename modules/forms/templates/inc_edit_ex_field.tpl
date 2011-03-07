{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
toggleListCustom = function(radio) {
  var enableList = (radio.value == "concept" && radio.checked);
  var form = radio.form;
  
  var input = form.concept_id_autocomplete_view;
  var select = form._spec_type;
  
  if (input) {
    input.up(".dropdown").down(".dropdown-trigger").setVisibility(enableList);
    input.disabled = input.readOnly = !enableList;
  }
  
  if (enableList) {
    //$V(select, "none");
  }
  else {
    $V(input, "");
    $V(form.concept_id, "");
  }
  
  select.disabled = select.readOnly = !!$V(form.ex_class_field_id) || enableList;
  
  ExFieldSpec.edit(form);
}

Main.add(function(){
  var radio = getForm("editField")._concept_type[0];
  toggleListCustom.defer(radio);
	
  var fields = {{$other_fields|@json}};
	var form = getForm("editField");
	
  ExFieldSpec.edit(form);
  form.elements._locale.select();
});

checkExField = function(form) {
  if (!checkForm(form)) return false;
	
	var prop = $V(form.elements.prop);
	
	if (prop.indexOf("enum") == 0 && prop.indexOf("list|") == -1) {
	  alert("Un champ de type Liste de choix nécessite une liste d'options");
		$(getForm("editFieldSpec").elements["list[]"][0]).tryFocus();
	  return false;
	}
	
	return true;
}

updateInternalName = function(e){
  var form = e.form;
	var str = ExField.slug($V(e));
	$V(form.elements.name, str);
}
</script>

<form name="editField" method="post" action="?" data-object_guid="{{$ex_field->_guid}}" onsubmit="return onSubmitFormAjax(this, {check: checkExField,onComplete: ExClass.edit.curry({{$ex_class->_id}})})">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_ex_class_field_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="_enum_translation" value="" />
	
  {{mb_key object=$ex_field}}
  {{mb_field object=$ex_field field=ex_group_id hidden=true}}
	
	{{mb_field object=$ex_field field=name hidden=true}}
  
  <table class="form">
  	
    {{assign var=object value=$ex_field}}
		<tr>
		  {{if $object->_id}}
		  <th class="title modify" colspan="4">
		    {{mb_include module=system template=inc_object_notes}}
		    {{mb_include module=system template=inc_object_idsante400}}
		    {{mb_include module=system template=inc_object_history}}
		    {{tr}}{{$object->_class_name}}-title-modify{{/tr}} 
		    '{{$object}}'
		  </th>
		  {{else}}
		  <th class="title" colspan="4">
		    {{tr}}{{$object->_class_name}}-title-create{{/tr}} 
		  </th>
		  {{/if}}
		</tr>
    
    <tr>
      <th style="width: 8em;">{{mb_label object=$ex_field field=_locale}}</th>
      <td>
      	{{if $ex_field->_id}}
          {{mb_field object=$ex_field field=_locale size=40}}
				{{else}}
      	  {{mb_field object=$ex_field field=_locale onkeyup="updateInternalName(this)" size=40}}
				{{/if}}
			</td>
			
      <th>{{mb_label object=$ex_field field=_locale_desc}}</th>
      <td>{{mb_field object=$ex_field field=_locale_desc tabIndex="2" size=40}}</td>
    </tr>
		
    <tr>
      <th><label for="ex_group_id">Groupe</label></th>
      <td>
        <select name="ex_group_id">
          {{foreach from=$ex_class->_ref_groups item=_group}}
            <option value="{{$_group->_id}}" {{if $_group->_id == $ex_field->ex_group_id}}selected="selected"{{/if}}>{{$_group}}</option>
          {{/foreach}}
        </select>
      </td>
      
      <th>{{mb_label object=$ex_field field=_locale_court}}</th>
      <td>{{mb_field object=$ex_field field=_locale_court tabIndex="3" size=30}}</td>
		</tr>
		<tr>
			
      <th>
        <label>
          {{if !$ex_field->concept_id}}Type {{/if}}
          <input type="radio" name="_concept_type" value="custom" {{if $ex_field->_id}}style="display: none;"{{/if}}
                 {{if !$ex_field->concept_id}}checked="checked"{{/if}} onclick="toggleListCustom(this)" />
        </label>
			</th>
      <td>
      	{{if !$ex_field->_id}}
	        <select {{if $ex_field->_id}}disabled="disabled"{{/if}} name="_spec_type" onchange="ExFieldSpec.edit(this.form)">
	          {{foreach from="CMbFieldSpecFact"|static:classes item=_class key=_key}}
	            <option value="{{$_key}}" {{if $_key == $spec_type && !$ex_field->concept_id}}selected="selected"{{/if}}>{{tr}}CMbFieldSpec.type.{{$_key}}{{/tr}}</option>
	          {{/foreach}}
	        </select>
				{{else}}
				  <input type="hidden" name="_spec_type" value="{{$spec_type}}" />
					{{if !$ex_field->concept_id}}
					  {{tr}}CMbFieldSpec.type.{{$spec_type}}{{/tr}}
					{{/if}}
				{{/if}}
      </td>
			
      <th>
        <label>
          {{if !$ex_field->_id || $ex_field->concept_id}}{{tr}}CExClassField-concept_id{{/tr}}{{/if}}
					
          <input type="radio" name="_concept_type" value="concept" {{if $ex_field->_id}}style="display: none;"{{/if}}
                 {{if $ex_field->concept_id}}checked="checked"{{/if}} onclick="toggleListCustom(this)" />
        </label>
			</th>
			<td>
        {{if !$ex_field->_id}}
          {{mb_field object=$ex_field field=concept_id form="editField" autocomplete="true,1,50,false,true" 
					           onchange="ExFieldSpec.edit(this.form)"}}
        {{else}}
          {{mb_value object=$ex_field field=concept_id}}
          {{mb_field object=$ex_field field=concept_id hidden=true}}
        {{/if}}
      </td>
    </tr>
		
		<tr>
			<th></th>
      <td colspan="3">
        {{if $ex_field->_id}}
          <button type="submit" class="modify">{{tr}}Save{{/tr}}</button>
          <button type="button" class="trash" onclick="confirmDeletion(this.form,{ajax:true,typeName:'le champ ',objName:'{{$ex_field->_view|smarty:nodefaults|JSAttribute}}'})">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
          <button type="submit" class="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
		
		<tr {{if $app->user_prefs.INFOSYSTEM == 0}}style="display: none"{{/if}}>
			<th></th>
			<td colspan="3">
				{{mb_field object=$ex_field field=prop readonly="readonly" size=50}}
			</td>
    </tr>
		
  </table>
</form>

<div id="fieldSpecEditor" style="white-space: normal;"></div>
