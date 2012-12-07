{{assign var=_properties value=$_subgroup->_default_properties}}

{{assign var=_style value=""}}
{{foreach from=$_properties key=_type item=_value}}
  {{if $_value != ""}}
    {{assign var=_style value="$_style $_type:$_value;"}}
  {{/if}}
{{/foreach}}

<div class="resizable subgroup" id="subgroup-{{$_subgroup->_guid}}"
     style="left:{{$_subgroup->coord_left}}px; top:{{$_subgroup->coord_top}}px; width:{{$_subgroup->coord_width}}px; height:{{$_subgroup->coord_height}}px;">
  <fieldset {{if !$_subgroup->title}} class="no-label" {{/if}} style="{{$_style}}">
    {{if $_subgroup->title}}
      <legend>{{$_subgroup->title}}</legend>
    {{/if}}

    {{* SUBGROUPS *}}
    {{foreach from=$_subgroup->_ref_children_groups item=_sub_subgroup}}
      {{mb_include
        module=forms
        template=inc_ex_subgroup
        _subgroup=$_sub_subgroup
      }}
    {{/foreach}}

    {{* FIELDS *}}
    {{foreach from=$_subgroup->_ref_children_fields item=_field}}
      {{assign var=_field_name value=$_field->name}}

      <div class="field-{{$_field_name}} resizable field-input {{if $_field->_no_size}} no-size {{/if}}"
           style="left:{{$_field->coord_left}}px; top:{{$_field->coord_top}}px; width:{{$_field->coord_width}}px; height:{{$_field->coord_height}}px; ">
        {{mb_include module=forms template=inc_reported_value ex_object=$ex_object ex_field=$_field}}
        {{mb_include module=forms template=inc_ex_object_field ex_object=$ex_object ex_field=$_field form="editExObject_$ex_form_hash"}}
      </div>
    {{/foreach}}

    {{* MESSAGES *}}
    {{foreach from=$_subgroup->_ref_children_messages item=_message}}
      <div class="resizable {{if $_message->_no_size}} no-size {{/if}}" id="message-{{$_message->_guid}}"
           style="left:{{$_message->coord_left}}px; top:{{$_message->coord_top}}px; width:{{$_message->coord_width}}px; height:{{$_message->coord_height}}px; pointer-events: none;">
        {{mb_include module=forms template=inc_ex_message}}
      </div>
    {{/foreach}}
  </fieldset>
</div>
