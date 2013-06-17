
<div class="resizable subgroup" tabIndex="0" data-subgroup_id="{{$_subgroup->_id}}"
     style="left:{{$_subgroup->coord_left}}px; top:{{$_subgroup->coord_top}}px; width:{{$_subgroup->coord_width}}px; height:{{$_subgroup->coord_height}}px; "
     ondblclick="ExSubgroup.edit({{$_subgroup->_id}}); Event.stop(event);"
     onclick="this.up('.resizable').focus(); Event.stop(event);"
     unselectable="on"
     onselectstart="return false;"
  >
{{mb_include module=forms template=inc_resizable_handles}}
  <div class="overlayed">
    <fieldset {{if !$_subgroup->title}} class="no-label" {{/if}}>
      {{if $_subgroup->title}}
        <legend>{{$_subgroup->title}}</legend>
      {{/if}}

      {{* SUBGROUPS *}}
      {{foreach from=$_subgroup->_ref_children_groups item=_sub_subgroup}}
        {{mb_include
          module=forms
          template=inc_ex_subgroup_draggable
          _subgroup=$_sub_subgroup
        }}
      {{/foreach}}

      {{* FIELDS *}}
      {{foreach from=$_subgroup->_ref_children_fields item=_field}}
        {{if !$_field->disabled}}
          {{mb_include
            module=forms
            template=inc_ex_field_draggable_children
            _field=$_field
          }}
        {{/if}}
      {{/foreach}}

      {{* MESSAGES *}}
      {{foreach from=$_subgroup->_ref_children_messages item=_message}}
        <div class="resizable {{if $_message->_no_size}} no-size {{/if}} draggable-message" tabIndex="0" data-message_id="{{$_message->_id}}"
             style="left:{{$_message->coord_left}}px; top:{{$_message->coord_top}}px; width:{{$_message->coord_width}}px; height:{{$_message->coord_height}}px;">
          {{mb_include module=forms template=inc_resizable_handles}}
          {{mb_include
            module=forms
            template=inc_ex_message_draggable
            _field=$_message
            ex_group_id=$_group_id
            _type=""
          }}
        </div>
      {{/foreach}}
    </fieldset>
  </div>
</div>