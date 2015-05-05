{{unique_id var=uid_exobject}}

<table class="main layout">
  <tr>
    <td class="narrow" style="min-width: 20em; vertical-align: top;">
      {{if $cross_context_class && $cross_context_id}}
        {{assign var=can_create value=true}}
      {{else}}
        {{assign var=can_create value=$readonly|ternary:false:true}}
      {{/if}}

      {{if $can_create && "digitalpen"|module_active}}
        {{mb_include module=digitalpen template=inc_widget_forms_to_validate object_guid="$reference_class-$reference_id" narrow=true}}
      {{/if}}

      {{if $can_create && $ex_classes_creation|@count}}
        <select onchange="ExObject.showExClassFormSelect.defer(this, '{{$self_guid}}')" style="width: 22em;">
          <option value=""> &ndash; Nv. formulaire dans {{$creation_context}} </option>
          {{foreach from=$ex_classes_creation item=_ex_class_events key=_ex_class_id}}
            {{if $_ex_class_events|@count > 1}}
              <optgroup label="{{$ex_classes.$_ex_class_id}}">
                {{foreach from=$_ex_class_events item=_ex_class_event}}
                  <option value="{{$_ex_class_event->ex_class_id}}"
                          data-reference_class="{{$creation_context->_class}}"
                          data-reference_id="{{$creation_context->_id}}"
                          data-host_class="{{$_ex_class_event->host_class}}"
                          data-event_name="{{$_ex_class_event->event_name}}">
                    {{$_ex_class_event}}
                  </option>
                {{/foreach}}

              </optgroup>
              {{else}}
              {{foreach from=$_ex_class_events item=_ex_class_event}}
                <option value="{{$_ex_class_event->ex_class_id}}"
                        data-reference_class="{{$creation_context->_class}}"
                        data-reference_id="{{$creation_context->_id}}"
                        data-host_class="{{$_ex_class_event->host_class}}"
                        data-event_name="{{$_ex_class_event->event_name}}">
                  {{$ex_classes.$_ex_class_id}}
                </option>
              {{/foreach}}
            {{/if}}
          {{/foreach}}
        </select>
      {{/if}}

      <table class="main tbl">
        {{foreach from=$ex_class_categories item=_category}}
          {{if $_category->ex_class_category_id}}
            {{assign var=_show_catgegory value=false}}

            {{foreach from=$_category->_ref_ex_classes item=_ex_class}}
              {{assign var=_ex_class_id value=$_ex_class->_id}}
              {{if array_key_exists($_ex_class_id,$ex_objects_counts) && $ex_objects_counts.$_ex_class_id > 0}}
                {{assign var=_show_catgegory value=true}}
              {{/if}}
            {{/foreach}}

            {{if $_show_catgegory}}
              <tr>
                <td style="width: 1px; background: #{{$_category->color}}"></td>
                <th colspan="3" style="text-align: left;" title="{{$_category->description}}">
                  {{$_category}}
                </th>
              </tr>
            {{/if}}
          {{/if}}

          {{foreach from=$_category->_ref_ex_classes item=_ex_class}}
            {{assign var=_ex_class_id value=$_ex_class->_id}}

            {{if array_key_exists($_ex_class_id,$ex_objects_counts)}}
              {{assign var=_ex_objects_count value=$ex_objects_counts.$_ex_class_id}}
              {{if $_ex_objects_count}}
                <tr>
                  <td style="width: 1px; background: #{{$_category->color}}"></td>
                  <td class="text">
                    <strong style="float: right;" class="ex-object-result">
                      {{if $ex_objects_results.$_ex_class_id !== null}}
                        = {{$ex_objects_results.$_ex_class_id}}
                      {{/if}}
                    </strong>

                    <a href="#1" onclick="$(this).up('tr').addUniqueClassName('selected'); ExObject.loadExObjects('{{$reference_class}}', '{{$reference_id}}', 'ex_class-list-{{$uid_exobject}}', 2, '{{$_ex_class_id}}', {other_container: this.up('tr'), readonly: {{$readonly|ternary:1:0}}, cross_context_class: '{{$cross_context_class}}', cross_context_id: '{{$cross_context_id}}'}); return false;">
                      {{$ex_classes.$_ex_class_id->name}}
                    </a>
                  </td>
                  <td class="narrow">
                    {{if $can_create && isset($ex_classes_creation.$_ex_class_id|smarty:nodefaults)}}
                      {{assign var=_ex_class_event value=$ex_classes_creation.$_ex_class_id|@reset}}
                      <button class="add notext compact"
                              onclick="showExClassForm('{{$_ex_class_id}}', '{{$creation_context->_guid}}', '{{$_ex_class_event->host_class}}-{{$_ex_class_event->event_name}}', null, '{{$_ex_class_event->event_name}}', '@ExObject.refreshSelf.{{$self_guid}}');">
                        {{tr}}New{{/tr}}
                      </button>
                    {{/if}}
                  </td>
                  <td class="narrow" style="text-align: right;">
                    <span class="compact ex-object-count">{{$_ex_objects_count}}</span>
                    <button class="right notext compact"
                            onclick="$(this).up('tr').addUniqueClassName('selected'); ExObject.loadExObjects('{{$reference_class}}', '{{$reference_id}}', 'ex_class-list-{{$uid_exobject}}', 2, '{{$_ex_class_id}}', {other_container: this.up('tr'), readonly: {{$readonly|ternary:1:0}}, cross_context_class: '{{$cross_context_class}}', cross_context_id: '{{$cross_context_id}}'})">
                    </button>
                  </td>
                </tr>
              {{/if}}
            {{/if}}
          {{/foreach}}
        {{/foreach}}
      </table>
    </td>
    <td id="ex_class-list-{{$uid_exobject}}" style="vertical-align: top;">
      <div class="small-info">
        Cliquez sur le bouton correspondant au formulaire dont vous voulez voir le détail
      </div>
    </td>
  </tr>
</table>