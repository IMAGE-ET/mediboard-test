{{foreach from=$groups item=_group}}
  <div id="tab-{{$_group->_guid}}" style="display: none; position: relative;" class="pixel-positionning">
    {{* ----- SUB GROUPS ----- *}}
    {{foreach from=$_group->_ref_subgroups item=_subgroup}}
      {{mb_include
        module=forms
        template=inc_ex_subgroup
        _subgroup=$_subgroup
      }}
    {{/foreach}}
    
    {{* ----- FIELDS ----- *}}
    {{foreach from=$_group->_ref_root_fields item=_field}}
      {{if !$_field->disabled}}
        {{assign var=_field_name value=$_field->name}}

        <div class="resizable field-{{$_field_name}} field-input {{if $_field->_no_size}} no-size {{/if}}"
             style="left:{{$_field->coord_left}}px; top:{{$_field->coord_top}}px; width:{{$_field->coord_width}}px; height:{{$_field->coord_height}}px; ">
          {{mb_include module=forms template=inc_reported_value ex_object=$ex_object ex_field=$_field}}
          {{mb_include module=forms template=inc_ex_object_field ex_object=$ex_object ex_field=$_field form="editExObject_$ex_form_hash"}}
        </div>
      {{/if}}
    {{/foreach}}

    {{* ----- MESSAGES ----- *}}
    {{foreach from=$_group->_ref_root_messages item=_message}}
      <div class="resizable {{if $_message->_no_size}} no-size {{/if}} draggable-message" id="message-{{$_message->_guid}}"
           style="left:{{$_message->coord_left}}px; top:{{$_message->coord_top}}px; width:{{$_message->coord_width}}px; height:{{$_message->coord_height}}px; pointer-events: none;">
        {{mb_include module=forms template=inc_ex_message}}
      </div>
    {{/foreach}}
  </div>
{{/foreach}}

