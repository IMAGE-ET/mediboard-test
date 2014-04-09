<table class="main layout">
  <tr>
    <td {{if !$ex_class->pixel_positionning}} style="width: 30%;" {{/if}}>
      <select onchange="toggleList(this, '{{$_group_id}}')" class="dont-lock">
        {{foreach from=$ex_class->_host_objects item=_object key=_class}}
          <option value="{{$_class}}">{{tr}}{{$_class}}{{/tr}}</option>
        {{/foreach}}
      </select>
      {{if !$ex_class->pixel_positionning}}
      </td>
      <td>
    {{/if}}
      {{foreach from=$ex_class->_host_objects item=_object key=_class name=_host_objects}}
        <div style="overflow-y: scroll; min-height: 140px; {{if !$ex_class->pixel_positionning}} max-height: 140px; {{else}} max-height: 600px; {{/if}} {{if $smarty.foreach._host_objects.first}} display: inline-block; {{else}} display: none; {{/if}}"
             class="hostfield-{{$_group_id}}-{{$_class}} hostfield-list-{{$_group_id}}" data-x="" data-y="">
          <ul>
            {{foreach from=$_object->_specs item=_spec key=_field}}
              {{if $_spec->show == 1 || $_field == "_view" || ($_spec->show == "" && $_field.0 !== "_")}}
                <li>
                  {{mb_include module=forms template=inc_ex_host_field_draggable ex_group_id=$_group_id host_object=$_object}}
                </li>
              {{/if}}
            {{/foreach}}
          </ul>
        </div>
      {{/foreach}}
    </td>
  </tr>
</table>