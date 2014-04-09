
<script type="text/javascript">
Main.add(function(){
  Control.Tabs.create("field_groups_layout");
});

toggleList = function(select, ex_group_id) {
  $$(".hostfield-list-"+ex_group_id).invoke("hide");
  $$(".hostfield-"+ex_group_id+"-"+$V(select))[0].show();
}
</script>

<ul class="control_tabs" id="field_groups_layout" style="font-size: 0.9em;">
  {{foreach from=$ex_class->_ref_groups item=_group}}
    <li>
      <a href="#group-layout-{{$_group->_guid}}" style="padding: 2px 4px;">
        {{$_group->name}} <small>({{$_group->_ref_fields|@count}})</small>
      </a>
    </li>
  {{/foreach}}
</ul>

{{assign var=groups value=$ex_class->_ref_groups}}

<form name="form-grid-layout" method="post" onsubmit="return false" class="prepared pixel-positionning">
  
{{foreach from=$groups key=_group_id item=_group}}
<div id="group-layout-{{$_group->_guid}}" style="display: none;" class="group-layout" data-group_id="{{$_group->_id}}">
  <table class="main layout">
    <tr>
      <td class="narrow">
        <div style="height: 600px; /*overflow: auto;*/" class="scrollable">
          <div class="pixel-grid" unselectable>
            {{* SUBGROUPS *}}
            {{foreach from=$_group->_ref_subgroups item=_subgroup}}
              {{mb_include
                module=forms
                template=inc_ex_subgroup_draggable
                _subgroup=$_subgroup
              }}
            {{/foreach}}

            {{* FIELDS *}}
            {{foreach from=$_group->_ref_root_fields item=_field}}
              {{if !$_field->disabled}}
                {{mb_include
                  module=forms
                  template=inc_ex_field_draggable_children
                  _field=$_field
                }}
              {{/if}}
            {{/foreach}}

            {{* MESSAGES *}}
            {{foreach from=$_group->_ref_root_messages item=_message}}
              <div class="resizable {{if $_message->_no_size}} no-size {{/if}}" tabIndex="0" data-message_id="{{$_message->_id}}"
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

            {{* HOST FIELDS *}}
            {{foreach from=$_group->_ref_host_fields item=_host_field}}
              {{if $_host_field->type}}
                <div class="resizable {{if $_host_field->_no_size}} no-size {{/if}}" tabIndex="0" data-host_field_id="{{$_host_field->_id}}"
                     style="left:{{$_host_field->coord_left}}px; top:{{$_host_field->coord_top}}px; width:{{$_host_field->coord_width}}px; height:{{$_host_field->coord_height}}px;">
                  {{mb_include module=forms template=inc_resizable_handles}}
                  {{assign var=_host_class value=$_host_field->host_class}}

                  {{mb_include module=forms template=inc_ex_host_field_draggable
                  _host_field=$_host_field
                  ex_group_id=$_group_id
                  _field=$_host_field->field
                  _type=$_host_field->type
                  host_object=$ex_class->_host_objects.$_host_class}}
                </div>
              {{/if}}
            {{/foreach}}
          </div>
        </div>
      </td>
      <td>
        {{mb_include module=forms template=inc_outofgrid_hostfields layout_editor=true}}
      </td>
    </tr>
  </table>
</div>
{{/foreach}}

</form>
