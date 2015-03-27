{{if !$readonly}}

  {{foreach from=$groups item=_group}}
    <div id="tab-{{$_group->_guid}}" style="display: none; position: relative;" class="pixel-positionning">
      {{* ----- PICTURES ----- *}}
      {{foreach from=$_group->_ref_root_pictures item=_picture}}
        {{if !$_picture->disabled && $_picture->_ref_file && $_picture->_ref_file->_id}}
          <div class="resizable draggable-picture" id="picture-{{$_picture->_guid}}"
               style="left:{{$_picture->coord_left}}px; top:{{$_picture->coord_top}}px; width:{{$_picture->coord_width}}px; height:{{$_picture->coord_height}}px; pointer-events: none; text-align: center;">
            {{mb_include module=forms template=inc_ex_picture}}
          </div>
        {{/if}}
      {{/foreach}}

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

          {{if $_field->hidden}}
            {{mb_field object=$ex_object field=$_field_name hidden=true}}
          {{else}}
            <div class="resizable field-{{$_field_name}} field-input {{if $_field->_no_size}} no-size {{/if}}"
                 style="left:{{$_field->coord_left}}px; top:{{$_field->coord_top}}px; width:{{$_field->coord_width}}px; height:{{$_field->coord_height}}px; ">
              {{mb_include module=forms template=inc_ex_object_field ex_object=$ex_object ex_field=$_field form="editExObject_$ex_form_hash"}}
            </div>
          {{/if}}
        {{/if}}
      {{/foreach}}

      {{* ----- MESSAGES ----- *}}
      {{foreach from=$_group->_ref_root_messages item=_message}}
        <div class="resizable {{if $_message->_no_size}} no-size {{/if}} draggable-message" id="message-{{$_message->_guid}}"
             style="left:{{$_message->coord_left}}px; top:{{$_message->coord_top}}px; width:{{$_message->coord_width}}px; height:{{$_message->coord_height}}px; pointer-events: none;">
          {{mb_include module=forms template=inc_ex_message}}
        </div>
      {{/foreach}}

      {{* HOST FIELDS *}}
      {{foreach from=$_group->_ref_host_fields item=_host_field}}
        {{if $_host_field->type}}
          <div class="resizable {{if $_host_field->_no_size}} no-size {{/if}}" data-host_field_id="{{$_host_field->_id}}"
               style="left:{{$_host_field->coord_left}}px; top:{{$_host_field->coord_top}}px; width:{{$_host_field->coord_width}}px; height:{{$_host_field->coord_height}}px;">
            {{assign var=_host_class value=$_host_field->host_class}}

            {{if $_host_field->type == "label"}}
              {{mb_label object=$_host_field->_ref_host_object field=$_host_field->field}}
            {{else}}
              {{if $_host_field->_ref_host_object->_id}}
                {{mb_value object=$_host_field->_ref_host_object field=$_host_field->field}}
              {{else}}
                <div class="info empty opacity-30">Information non disponible</div>
              {{/if}}
            {{/if}}
          </div>
        {{/if}}
      {{/foreach}}
    </div>
  {{/foreach}}

{{else}}

  {{foreach from=$groups item=_group}}
    <h3 style="border-bottom: 1px solid #999;">{{$_group->name}}</h3>

    <div id="tab-{{$_group->_guid}}" style="position: relative;" class="pixel-positionning pixel-grid-print">
      {{* ----- PICTURES ----- *}}
      {{foreach from=$_group->_ref_root_pictures item=_picture}}
        {{if !$_picture->disabled && $_picture->_ref_file && $_picture->_ref_file->_id}}
          <div class="resizable draggable-picture" id="picture-{{$_picture->_guid}}"
               style="left:{{$_picture->coord_left}}px; top:{{$_picture->coord_top}}px; width:{{$_picture->coord_width}}px; height:{{$_picture->coord_height}}px; pointer-events: none; text-align: center;">
            {{mb_include module=forms template=inc_ex_picture}}
          </div>
        {{/if}}
      {{/foreach}}

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
        {{if !$_field->disabled && !$_field->hidden}}
          {{assign var=_field_name value=$_field->name}}

          <div class="resizable field-{{$_field_name}} field-input {{if $_field->_no_size}} no-size {{/if}}"
               style="left:{{$_field->coord_left}}px; top:{{$_field->coord_top}}px; width:{{$_field->coord_width}}px; height:{{$_field->coord_height}}px; ">
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

      {{* HOST FIELDS *}}
      {{foreach from=$_group->_ref_host_fields item=_host_field}}
        {{if $_host_field->type}}
          <div class="resizable {{if $_host_field->_no_size}} no-size {{/if}}" data-host_field_id="{{$_host_field->_id}}"
               style="left:{{$_host_field->coord_left}}px; top:{{$_host_field->coord_top}}px; width:{{$_host_field->coord_width}}px; height:{{$_host_field->coord_height}}px;">
            {{assign var=_host_class value=$_host_field->host_class}}

            {{if $_host_field->type == "label"}}
              {{mb_label object=$_host_field->_ref_host_object field=$_host_field->field}}
            {{else}}
              {{if $_host_field->_ref_host_object->_id}}
                {{mb_value object=$_host_field->_ref_host_object field=$_host_field->field}}
              {{else}}
                <div class="info empty opacity-30">Information non disponible</div>
              {{/if}}
            {{/if}}
          </div>
        {{/if}}
      {{/foreach}}
    </div>
  {{/foreach}}

{{/if}}