{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function(){
  ExClass.id = "{{$ex_class->_id}}";
  ExClass.layourEditorReady = false;
  $("exClassList").select("tr[data-ex_class_id]").invoke("removeClassName", "selected");
  
  var form = getForm("editExClass");
  var auto = !!form._event.options[form._event.selectedIndex].get("auto");
  ExClass.toggleConditional(auto);
});
</script>

<table class="main form">
  {{mb_include module=system template=inc_form_table_header object=$ex_class colspan="2" css_class="text"}}

  <tr>
    <td colspan="2">
      <form name="editExClass" method="post" action="?" onsubmit="return onSubmitFormAjax(this)">
        <input type="hidden" name="m" value="system" />
        <input type="hidden" name="dosql" value="do_ex_class_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="_duplicate" value="" />
        <input type="hidden" name="callback" value="ExClass.editCallback" />
        {{mb_key object=$ex_class}}
        
        {{mb_field object=$ex_class field=host_class hidden=true}}
        {{mb_field object=$ex_class field=event hidden=true}}
        
        <table class="main form">
          <tr>
            <th>{{mb_label object=$ex_class field=event}}</th>
            <td>
              <select name="_event" class="notNull" onchange="ExClass.setEvent(this)" style="width: 20em !important;">
                <option value=""> &ndash; Choisir </option>
                {{foreach from=$classes item=_events key=_class}}
                  <optgroup label="{{tr}}{{$_class}}{{/tr}}">
                    {{foreach from=$_events item=_params key=_event_name}}
                      <option value="{{$_class}}.{{$_event_name}}" {{if @$_params.auto}} data-auto="true" {{/if}}
                              {{if $_class == $ex_class->host_class && $_event_name == $ex_class->event}} selected="selected" {{/if}}
                              >
                        {{tr}}{{$_class}}{{/tr}} - {{tr}}{{$_class}}-event-{{$_event_name}}{{/tr}}
                        {{if @$_params.auto}} (déclench. auto){{/if}}
                      </option>
                    {{/foreach}}
                  </optgroup>
                {{/foreach}}
              </select>
            </td>
            
            <th>{{mb_label object=$ex_class field=disabled}}</th>
            <td>{{mb_field object=$ex_class field=disabled typeEnum=checkbox}}</td>
            
            <td class="compact text" style="vertical-align: middle;">{{tr}}CExClass-disabled-desc{{/tr}}</td>
          </tr>
          
          <tr>
            <th>{{mb_label object=$ex_class field=name}}</th>
            <td>{{mb_field object=$ex_class field=name style="width: 95%;"}}</td>
            
            <th>{{mb_label object=$ex_class field=conditional}}</th>
            <td>{{mb_field object=$ex_class field=conditional typeEnum=checkbox}}</td>
            
            <td class="compact text" style="vertical-align: middle;">{{tr}}CExClass-conditional-desc{{/tr}}</td>
          </tr>
          
          <tr>
            <th>{{mb_label object=$ex_class field=group_id}}</th>
            <td>
              <select name="group_id" style="width: 20em;">
                <option value=""> &ndash; Tous </option>
                {{foreach from=$groups item=_group}}
                  <option value="{{$_group->_id}}" {{if $ex_class->group_id == $_group->_id}} selected="selected" {{/if}}>{{$_group}}</option>
                {{/foreach}}
              </select>
            </td>
            
            <th rowspan="2">{{mb_label object=$ex_class field=unicity}}</th>
            <td rowspan="2" colspan="2">
              {{if $ex_class->_id}}
                {{mb_field object=$ex_class field=unicity typeEnum=radio}}
              {{else}}
                <div class="small-info">
                  Enregistrez le formulaire pour choisir son type d'unicité
                </div>
              {{/if}}
            </td>
            
            <td class="compact text" style="vertical-align: middle;"></td>
          </tr>
          
          <tr>
            <th>{{mb_label object=$ex_class field=native_views}}</th>
            <td colspan="4">{{mb_field object=$ex_class field=native_views}}</td>
          </tr>
          
          <tr>
            <td colspan="3" style="vertical-align: bottom; text-align: right;">
              {{if $ex_class->_id}}
                {{if $ex_class->host_class != "CMbObject"}}
                  <button type="button" class="search" onclick="ExObject.edit(null, {{$ex_class->_id}}, 'preview')">{{tr}}Preview{{/tr}}</button>
                {{/if}}
                
                <button type="submit" class="modify">{{tr}}Save{{/tr}}</button>
                <button type="button" class="hslip" onclick="if (confirm('Confirmez-vous la duplication de ce formulaire ?')) { $V(this.form._duplicate, 1); this.form.onsubmit(); }">{{tr}}Duplicate{{/tr}}</button>
                
                <button type="button" class="trash" onclick="confirmDeletion(this.form,{ajax:true,typeName:'{{tr}}CExClass.one{{/tr}}',objName:'{{$ex_class->_view|smarty:nodefaults|JSAttribute}}'})">
                  {{tr}}Delete{{/tr}}
                </button>
                <!--<button type="button" class="change" onclick="MbObject.export('{{$ex_class->_guid}}')">
                  {{tr}}Export{{/tr}}
                </button>-->
              {{else}}
                <button type="submit" class="submit">{{tr}}Create{{/tr}}</button>
              {{/if}}
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
</table>

{{if $ex_class->_id}}

<script type="text/javascript">
Main.add(function(){
  exClassTabs = Control.Tabs.create("ExClass-back", true, {
    afterChange: function(newContainer){
      if (ExClass.layourEditorReady || newContainer.id != "fields-layout") return;
      
      var form = getForm("form-grid-layout").removeClassName("prepared");
      prepareForm(form);
      ExClass.initLayoutEditor();
    }
  });
  
  exFieldGroupsTabs = Control.Tabs.create("field_groups", true);
  
  var selected = $("exClassList").down("tr[data-ex_class_id={{$ex_class->_id}}]");
  if (selected) {
    selected.addClassName("selected");
  }
});

toggleGroupLabelEdit = function(link) {
  link = $(link);
  link.down('span.label').toggle();
  
  var form = link.next('form');
  var input = form.elements.name;
  
  form.toggle();
  input.select();
}
</script>

<ul class="control_tabs" id="ExClass-back">
  <li><a href="#fields-specs" class="active">{{tr}}CExClass-back-fields{{/tr}}</a></li>
  <li><a href="#fields-layout">{{tr}}CExClassField-layout{{/tr}}</a></li>
  <li><a href="#fields-constraints">{{tr}}CExClass-back-constraints{{/tr}}</a></li>
</ul>
<hr class="control_tabs" />

<div id="fields-specs">

<ul class="control_tabs" id="field_groups" style="font-size: 0.9em;">
  {{foreach from=$ex_class->_ref_groups item=_group name=_groups}}
    <li>
      <a href="#group-{{$_group->_guid}}" ondblclick="toggleGroupLabelEdit(this)" title="Double-cliquer pour modifier" style="padding: 2px 4px;">
        <span class="label" style="font-weight: normal;">
          {{$_group->name}} <small>({{$_group->_ref_fields|@count}})</small>
        </span>
      </a>
        
      <form name="edit-field-group-{{$_group->_guid}}" action="?" method="post" style="display: none;"
            onsubmit="return onSubmitFormAjax(this, {onComplete: ExClass.edit.curry({{$ex_class->_id}})})">
        <input type="hidden" name="m" value="system" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="_move" value="" />
        <input type="hidden" name="@class" value="CExClassFieldGroup" />
        {{mb_key object=$_group}}
        
        {{if !$smarty.foreach._groups.first}}
          <button type="button" class="left notext" onclick="$V(this.form._move, 'before'); this.form.onsubmit()" title="Déplacer sur la gauche"></button>
        {{/if}}
        
        {{mb_field object=$_group field=name size=18}}
        
        {{if !$smarty.foreach._groups.last}}
          <button type="button" class="right notext" onclick="$V(this.form._move, 'after'); this.form.onsubmit()" title="Déplacer sur la droite"></button>
        {{/if}}
        
        <button onclick="Event.stop(event); this.form.onsubmit();" 
                class="submit notext compact" type="submit">
          {{tr}}Save{{/tr}}
        </button>
        <button onclick="toggleGroupLabelEdit($(this).up('li').down('a'))" 
                class="cancel notext compact" type="button">
          {{tr}}Cancel{{/tr}}
        </button>
        
        {{if $_group->_ref_fields|@count == 0}}
        <button onclick="return confirmDeletion(this.form, {ajax: true})" 
                class="trash notext compact" type="button">
          {{tr}}Delete{{/tr}}
        </button>
        {{/if}}
      </form>
    </li>
  {{/foreach}}
  
  {{* create a new group *}}
  <li style="white-space: nowrap;">
    <form name="create-field-group" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: ExClass.edit.curry({{$ex_class->_id}})})">
      <input type="hidden" name="m" value="system" />
      <input type="hidden" name="@class" value="CExClassFieldGroup" />
      <input type="hidden" name="ex_class_id" value="{{$ex_class->_id}}" />
      
      <button class="add" type="button" style="margin: -2px;" 
              onclick="$(this).hide().next('span').show(); $(this.form.elements.name).tryFocus()">
        {{tr}}CExClassFieldGroup-title-create{{/tr}}
      </button>
      
      <span style="display: none;">
        <button class="submit notext" type="submit" style="margin: -2px"></button>
        {{mb_field class=CExClassFieldGroup field=name size=10 style="margin-right: 4px;"}}
      </span>
    </form>
  </li>
</ul>
<hr class="control_tabs" />

<table class="main layout">
  <tr>
    <td style="width: 16em; max-width: 300px;">
      <table class="main tbl">
        {{*<tr>
          <th>{{mb_title class=CExClassField field=name}}</th>
          <th>{{mb_title class=CExClassField field=prop}}</th>
        </tr>*}}
        {{foreach from=$ex_class->_ref_groups item=_group}}
          <tbody id="group-{{$_group->_guid}}" style="display: none;">
            <tr>
              <td style="text-align: right; min-width: 14em;">
                <button type="button" class="new" onclick="ExField.create({{$ex_class->_id}}, '{{$_group->_id}}')">
                  {{tr}}CExClassField-title-create{{/tr}}
                </button>
              </td>
            </tr>
            {{foreach from=$_group->_ref_fields item=_field}}
              <tr class="ex-class-field" data-ex_class_field_id="{{$_field->_id}}">
                <td class="text">
                  <span style="float: right;">
                    {{if $_field->report_level}}
                      <img src="./images/icons/reported.png" title="{{tr}}CExClassField-reported{{/tr}}"/>
                    {{/if}}
                    
                    {{if $_field->formula}}
                      <img src="style/mediboard/images/buttons/formula.png" />
                    {{/if}}
                    
                    {{assign var=_spec_type value=$_field->_spec_object->getSpecType()}}
                    {{assign var=_can_formula_arithmetic value="CExClassField::formulaCanArithmetic"|static_call:$_spec_type}}
                    {{assign var=_can_formula_concat value="CExClassField::formulaCanConcat"|static_call:$_spec_type}}
                    
                    {{if $_can_formula_arithmetic || $_can_formula_concat}}
                    <button class="right notext insert-formula {{if $_can_formula_arithmetic}}arithmetic{{/if}} {{if $_can_formula_concat}}concat{{/if}}" style="margin: -3px; margin-left: -1px; display: none;"
                            onclick="ExFormula.insertText('[{{$_field->_locale|smarty:nodefaults|JSAttribute}}]')">
                      {{tr}}CExClassField.add_to_formula{{/tr}}
                    </button>
                    {{/if}}
                  </span>
                  
                  <a href="#1" onclick="ExField.edit('{{$_field->_id}}', null, null, '{{$_group->_id}}')">
                    {{if $_field->_locale}}
                      {{$_field->_locale}}
                    {{else}}
                      [{{$_field->name}}]
                    {{/if}}
                  </a>
                </td>
              </tr>
            {{foreachelse}}
              <tr>
                <td colspan="2" class="empty">{{tr}}CExClassField.none{{/tr}}</td>
              </tr>
            {{/foreach}}
            
            <tr>
              <th class="category">{{tr}}CExClassFieldGroup-back-class_messages{{/tr}}</th>
            </tr>
            <tr>
              <td style="text-align: right;">
                <button type="button" class="new" onclick="ExMessage.create('{{$_group->_id}}')">
                  {{tr}}CExClassMessage-title-create{{/tr}}
                </button>
              </td>
            </tr>
            {{foreach from=$_group->_ref_messages item=_message}}
              <tr class="ex-class-message" data-ex_class_message_id="{{$_message->_id}}">
                <td class="text">
                  <a href="#1" onclick="ExMessage.edit('{{$_message->_id}}', '{{$_group->_id}}')">
                    {{$_message}}
                  </a>
                </td>
              </tr>
            {{foreachelse}}
              <tr>
                <td colspan="2" class="empty">{{tr}}CExClassMessage.none{{/tr}}</td>
              </tr>
            {{/foreach}}
            
          </tbody>
        {{/foreach}}
      </table>
    </td>
    <td id="exFieldEditor" style="width: auto;">
      <!-- exFieldEditor -->&nbsp;
    </td>
  </tr>
</table>

</div>

<div id="fields-layout" style="display: none;">
  {{mb_include module=forms template=inc_edit_fields_layout}}
</div>

<div id="fields-constraints" style="display: none;">
  {{mb_include module=forms template=inc_edit_class_constraints}}
</div>

{{/if}}