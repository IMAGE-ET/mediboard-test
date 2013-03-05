{{if $object->_id}}
  <button type="button" class="new" onclick="MbObject.edit('{{$object->_class}}-0')">
    {{tr}}{{$object->_class}}-title-create{{/tr}}
  </button>
{{/if}}

{{mb_script module=forms script=ex_class_editor ajax=true}}

<script type="text/javascript">
toggleListCustom = function(radio) {
  var enableList = (radio.value == "list" && radio.checked);
  var form = radio.form;
  
  var input = form.ex_list_id_autocomplete_view;
  var select = form._spec_type;
  
  if (input) {
    input.up(".dropdown").down(".dropdown-trigger").setVisibility(enableList);
    input.disabled = input.readOnly = !enableList;
    
    var multiple = form._multiple;
    multiple.disabled = multiple.readOnly = !enableList;
  }
  
  if (enableList) {
    //$V(select, "none");
  }
  else {
    $V(input, "");
    $V(form.ex_list_id, "");
  }
  
  select.disabled = select.readOnly = enableList;
  
  ExConceptSpec.edit(form);
}

selectList = function(input) {
  ExConceptSpec.edit(input.form);
  
  if (!$V(input.form.elements.name)) {
    $V(input.form.elements.name, input.form.ex_list_id_autocomplete_view.value);
  }
}

Main.add(function(){
  var radio = getForm("edit-{{$object->_guid}}")._concept_type[0];
  toggleListCustom.defer(radio);
  
  Control.Tabs.create("ex-concept-tabs", true);
});
</script>

<form name="edit-{{$object->_guid}}" data-object_guid="{{$object->_guid}}" method="post" action="?" onsubmit="return onSubmitFormAjax(this)">
  {{mb_class object=$object}}
  {{mb_key object=$object}}
  
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="callback" value="MbObject.editCallback" />
  
  <table class="main form">
    
    {{mb_include module=system template=inc_form_table_header css_class="text" colspan=4}}
    
    {{if $object->_id}}
      {{mb_include module=system template=inc_tag_binder colspan=4}}
    {{/if}}
    
    <tr>
      <th>{{mb_label object=$object field=name}}</th>
      <td>{{mb_field object=$object field=name size=40}}</td>
      
      <th>
        <label>
          {{if !$object->_id || $object->ex_list_id}}{{tr}}CExConcept-ex_list_id{{/tr}}{{/if}}
          
          <input type="radio" name="_concept_type" value="list" {{if $object->_id}}style="display: none;"{{/if}}
                 {{if $object->ex_list_id}}checked="checked"{{/if}} onclick="toggleListCustom(this)" />
        </label>
      </th>
      <td>
        {{if !$object->_id}}
          {{mb_field object=$object field=ex_list_id form="edit-`$object->_guid`" autocomplete="true,1,50,true,true" onchange="selectList(this)"}}
          <button class="new" onclick="ExList.createInModal()" type="button">{{tr}}CExList-title-create{{/tr}}</button>
          <label>
            <input type="checkbox" name="_multiple" value="1" onclick="ExConceptSpec.edit(this.form)" /> Choix multiple 
          </label>
        {{else}}
          {{mb_value object=$object field=ex_list_id}}
          {{mb_field object=$object field=ex_list_id hidden=true}}
        {{/if}}
      </td>
    </tr>
    <tr>
      <th></th>
      <td>
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>

        {{if $object->_id}}
          <button type="button" class="trash" onclick="confirmDeletion(this.form,{ajax: true, typeName:'', objName:'{{$object->_view|smarty:nodefaults|JSAttribute}}'})">
            {{tr}}Delete{{/tr}}
          </button>
        {{/if}}
      </td>
      <th>
        <label>
          {{if !$object->ex_list_id}}Type {{/if}}
          <input type="radio" name="_concept_type" value="custom" {{if $object->_id}}style="display: none;"{{/if}}
                 {{if !$object->ex_list_id}}checked="checked"{{/if}} onclick="toggleListCustom(this)" />
        </label>
      </th>
      <td>
        {{assign var=spec_type value=$object->_concept_spec->getSpecType()}}
        
        {{if !$object->_id}}
          <select name="_spec_type" onchange="ExConceptSpec.edit(this.form)">
            {{foreach from="CExClassField::getTypes"|static_call:null key=_key item=_class}}
              {{if !$conf.forms.CExConcept.force_list || ($_key != "enum" && $_key != "set")}}
                <option value="{{$_key}}" {{if $_key == $spec_type && !$object->ex_list_id}}selected="selected"{{/if}}>
                  {{tr}}CMbFieldSpec.type.{{$_key}}{{/tr}}
                </option>
              {{/if}}
            {{/foreach}}
          </select>
        {{else}}
          <input type="hidden" name="_spec_type" value="{{$spec_type}}" />
          
          {{if !$object->ex_list_id}}
            {{tr}}CMbFieldSpec.type.{{$spec_type}}{{/tr}}
          {{/if}}
        {{/if}}
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

<ul id="ex-concept-tabs" class="control_tabs">
  <li>
    <a href="#ExConcept-spec-editor">Paramètres</a>
  </li>
  {{if $object->_id}}
  <li>
    <a href="#ex-back-class_fields" {{if $object->_back.class_fields|@count == 0}} class="empty" {{/if}}>
      {{tr}}CExConcept-back-class_fields{{/tr}} <small>({{$object->_back.class_fields|@count}})</small>
    </a>
  </li>
  {{/if}}
</ul>
<hr class="control_tabs" />

<div id="ExConcept-spec-editor" style="display: none;"></div>

{{if $object->_id}}
<div id="ex-back-class_fields" style="display: none;">
  <table class="main tbl">
    <tr>
      <th>{{tr}}CExClass{{/tr}}</th>
      <th>{{tr}}CExClassFieldGroup{{/tr}}</th>
      <th>{{tr}}CExClassField{{/tr}}</th>
    </tr>
    
    {{foreach from=$object->_back.class_fields item=_field}}
      <tr>
        <td>
          {{mb_value object=$_field->_ref_ex_group->_ref_ex_class field=name}}
        </td>
        <td>
          {{mb_value object=$_field->_ref_ex_group field=name}}
        </td>
        <td>
          {{mb_value object=$_field field=_locale}}
        </td>
      </tr>
    {{foreachelse}}
      <tr>
        <td class="empty" colspan="3">{{tr}}CExClassField.none{{/tr}}</td>
      </tr>
    {{/foreach}}
  </table>
</div>
{{/if}}
