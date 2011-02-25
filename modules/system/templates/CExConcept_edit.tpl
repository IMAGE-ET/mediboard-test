<button type="button" class="new" onclick="MbObject.edit('{{$object->_class_name}}-0')">
  {{tr}}{{$object->_class_name}}-title-create{{/tr}}
</button>

{{mb_include_script module=forms script=ex_class_editor ajax=true}}

<script type="text/javascript">
toggleListCustom = function(radio) {
  var enableList = (radio.value == "list" && radio.checked);
  var form = radio.form;
  
  var input = form.ex_list_id_autocomplete_view;
  var select = form._spec_type;
  
  input.up(".dropdown").down(".dropdown-trigger").setVisibility(enableList);
  
  if (enableList) {
    //$V(select, "none");
  }
  else {
    $V(input, "");
    $V(form.ex_list_id, "");
  }
  
  input.disabled = input.readOnly = !enableList;
  select.disabled = select.readOnly = enableList;
  
  ExConceptSpec.edit(form);
}

Main.add(function(){
  var radio = getForm("edit-{{$object->_guid}}")._concept_type[0];
  toggleListCustom.defer(radio);
});
</script>

<form name="edit-{{$object->_guid}}" method="post" action="?" onsubmit="return onSubmitFormAjax(this)">
  {{mb_class object=$object}}
  {{mb_key object=$object}}
  
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="callback" value="MbObject.editCallback" />
  
  <table class="main form">
    
    {{mb_include module=system template=inc_form_table_header css_class="text" colspan=4}}
    
    {{if $object->_id}}
      {{mb_include module=system template=inc_tag_binder colspan=4}}
      
      <tr>
        <td colspan="4"><hr /></td>
      </tr>
    {{/if}}
    
    <tr>
      <th>{{mb_label object=$object field=name}}</th>
      <td>{{mb_field object=$object field=name size=40}}</td>
      
      <th>
        <label>
          {{tr}}CExConcept-ex_list_id{{/tr}}
          <input type="radio" name="_concept_type" value="list" {{if $object->ex_list_id}}checked="checked"{{/if}}
                 onclick="toggleListCustom(this)" />
        </label>
      </th>
      <td>
        {{mb_field object=$object field=ex_list_id form="edit-`$object->_guid`" autocomplete="true,1,50,false,true" onchange="ExConceptSpec.edit(this.form)"}}
      </td>
    </tr>
    <tr>
      <th></th>
      <td>
        {{if $object->_id}}
          <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
          
          <button type="button" class="trash" onclick="confirmDeletion(this.form,{ajax: true, typeName:'', objName:'{{$object->_view|smarty:nodefaults|JSAttribute}}'})">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
          <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
      <th>
        <label>
          Type 
          <input type="radio" name="_concept_type" value="custom" {{if !$object->ex_list_id}}checked="checked"{{/if}}
                 onclick="toggleListCustom(this)" />
        </label>
      </th>
      <td>        
        <select name="_spec_type" onchange="ExConceptSpec.edit(this.form)">
           <!--<option value="none"> &ndash; Aucun</option>-->

          {{assign var=spec_type value=$object->_concept_spec->getSpecType()}}
          {{foreach from="CMbFieldSpecFact"|static:classes item=_class key=_key}}
            <option value="{{$_key}}" {{if $_key == $spec_type && !$object->ex_list_id}}selected="selected"{{/if}}>
              {{tr}}CMbFieldSpec.type.{{$_key}}{{/tr}}
            </option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    <tr {{if $app->user_prefs.INFOSYSTEM == 0}}style="display: none;"{{/if}}>
      <th></th>
      <td colspan="3">
        {{mb_field object=$object field=prop readonly=true size=80}}
      </td>
    </tr>
  </table>
</form>

<div id="ExConcept-spec-editor"></div>
