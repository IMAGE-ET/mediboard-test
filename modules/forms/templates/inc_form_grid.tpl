
  {{assign var=_first_row value=$grid|@reset}}
  {{assign var=_cols value=$_first_row|@reset}}
  {{math equation="100/x" x=$_cols|@count assign=_pct}}

<table class="main form ex-form">
    {{foreach from=$_cols item=_row}}
      <col style="width: {{$_pct}}%;" />
    {{/foreach}}

  {{foreach from=$grid key=_group_id item=_grid}}
  {{if $groups.$_group_id->_ref_fields|@count}}
  <tbody id="tab-{{$groups.$_group_id->_guid}}" style="display: none;">
  {{foreach from=$_grid key=_y item=_line}}
  <tr>
      {{foreach from=$_line key=_x item=_group name=_x}}
      {{if $_group.object}}
        {{if $_group.object instanceof CExClassField}}
          {{assign var=_field value=$_group.object}}
          {{assign var=_field_name value=$_field->name}}
          
          {{if $_group.type == "label"}}
            {{if $_field->coord_field_x == $_field->coord_label_x+1}}
              <th style="font-weight: bold; vertical-align: middle;">
                <div class="field-{{$_field->name}} field-label">
                  {{mb_label object=$ex_object field=$_field_name}}
                  {{mb_include module=forms template=inc_reported_value ex_object=$ex_object ex_field=$_field}}
                </div>
              </th>
            {{else}}
              <td style="font-weight: bold; text-align: left;" class="field-{{$_field->name}} field-label">
                <div class="field-{{$_field->name}} field-label">
                  {{mb_label object=$ex_object field=$_field_name}}
                  {{mb_include module=forms template=inc_reported_value ex_object=$ex_object ex_field=$_field}}
                </div>
              </td>
            {{/if}}
          {{elseif $_group.type == "field"}}
            <td {{if $_field->coord_field_x == $_field->coord_label_x+1}} style="vertical-align: middle;" {{/if}}>
              <div class="field-{{$_field->name}} field-input">
                {{mb_include module=forms template=inc_ex_object_field ex_object=$ex_object ex_field=$_field form="editExObject_$ex_form_hash"}}
              </div>
            </td>
          {{/if}}
        {{elseif $_group.object instanceof CExClassHostField}}
          {{assign var=_host_field value=$_group.object}}
          
          {{if $_group.type == "label"}}
              {{assign var=_next_col value=$smarty.foreach._x.iteration}}
              {{assign var=_next value=null}}

              {{if array_key_exists($_next_col,$_line)}}
                {{assign var=_tmp_next value=$_line.$_next_col}}

                {{if $_tmp_next.object instanceof CExClassHostField}}
                  {{assign var=_next value=$_line.$_next_col.object}}
                {{/if}}
              {{/if}}

              {{if $_next && $_next->host_class == $_host_field->host_class && $_next->field == $_host_field->field}}
                <th style="font-weight: bold; vertical-align: top;">
              {{mb_label object=$_host_field->_ref_host_object field=$_host_field->field}}
            </th>
          {{else}}
                <td style="font-weight: bold; text-align: left;">
                  {{mb_label object=$_host_field->_ref_host_object field=$_host_field->field}}
                </td>
              {{/if}}
            {{else}}
            <td>
              {{if $_host_field->_ref_host_object->_id}}
                {{mb_value object=$_host_field->_ref_host_object field=$_host_field->field}}
              {{elseif $preview_mode}}
                [{{mb_label object=$_host_field->_ref_host_object field=$_host_field->field}}]
              {{else}}
                <div class="info empty opacity-30">Information non disponible</div>
              {{/if}}
            </td>
          {{/if}}
        {{else}}
          {{assign var=_message value=$_group.object}}
          
          {{if $_group.type == "message_title"}}
            {{if $_message->coord_text_x == $_message->coord_title_x+1}}
              <th style="font-weight: bold; vertical-align: middle;">
                {{$_message->title}}
              </th>
            {{else}}
              <td style="font-weight: bold; text-align: left;">
                {{$_message->title}}
              </td>
            {{/if}}
          {{else}}
            <td>
              <div id="message-{{$_message->_guid}}">
                {{mb_include module=forms template=inc_ex_message}}
              </div>
            </td>
          {{/if}}
        {{/if}}
      {{else}}
        <td></td>
      {{/if}}
    {{/foreach}}
  </tr>
  {{/foreach}}
  
  {{* Out of grid *}}
  {{foreach from=$groups.$_group_id->_ref_fields item=_field}}
    {{assign var=_field_name value=$_field->name}}
    
    {{if isset($out_of_grid.$_group_id.field.$_field_name|smarty:nodefaults)}}
      <tr>
        <th colspan="2" style="vertical-align: middle; font-weight: bold; width: 50%;">
          <div class="field-{{$_field->name}} field-label">
            {{mb_label object=$ex_object field=$_field->name}}
            {{mb_include module=forms template=inc_reported_value ex_object=$ex_object ex_field=$_field}}
          </div>
        </th>
        <td colspan="2" style="vertical-align: middle;">
          <div class="field-{{$_field->name}} field-input">
            {{mb_include module=forms template=inc_ex_object_field ex_object=$ex_object ex_field=$_field form="editExObject_$ex_form_hash"}}
          </div>
        </td>
      </tr>
    {{/if}}
  {{/foreach}}

  <tr>
    <td colspan="4" class="button">
      <button class="submit singleclick" type="submit" {{if $preview_mode}}disabled{{/if}}>{{tr}}Save{{/tr}}</button>

      {{if $ex_object->_id && $can_delete && !$preview_mode}}
        <button type="button" class="trash" onclick="confirmDeletion(this.form,{callback: (function(){ FormObserver.changes = 0; onSubmitFormAjax(this.form); }).bind(this), typeName:'', objName:'{{$ex_object->_ref_ex_class->name|smarty:nodefaults|JSAttribute}}'})">
          {{tr}}Delete{{/tr}}
        </button>
      {{/if}}
    </td>
  </tr>
    
  </tbody>
  {{/if}}
  {{/foreach}}
  
</table>