{{unique_id var=uid_exobject}}

<table class="main layout">
  <tr>
    <td class="narrow" style="min-width: 20em; vertical-align: top;">

      {{if "digitalpen"|module_active}}
        {{mb_include module=digitalpen template=inc_widget_forms_to_validate object_guid="$reference_class-$reference_id" narrow=true}}
      {{/if}}

      {{if !$readonly && $ex_classes_creation|@count}}
        <select onchange="ExObject.showExClassFormSelect.defer(this, '{{$self_guid}}')" style="width: 22em;">
          <option value=""> &ndash; Remplir nouveau formulaire </option>
          {{foreach from=$ex_classes_creation item=_ex_class_events key=_ex_class_id}}
            {{if $_ex_class_events|@count > 1}}
              <optgroup label="{{$ex_classes.$_ex_class_id}}">
                {{foreach from=$_ex_class_events item=_ex_class_event}}
                  <option value="{{$_ex_class_event->ex_class_id}}"
                          data-reference_class="{{$reference_class}}"
                          data-reference_id="{{$reference_id}}"
                          data-host_class="{{$_ex_class_event->host_class}}"
                          data-event_name="{{$_ex_class_event->event_name}}">
                    {{$_ex_class_event}}
                  </option>
                {{/foreach}}

              </optgroup>
              {{else}}
              {{foreach from=$_ex_class_events item=_ex_class_event}}
                <option value="{{$_ex_class_event->ex_class_id}}"
                        data-reference_class="{{$reference_class}}"
                        data-reference_id="{{$reference_id}}"
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
        {{foreach from=$ex_objects_counts item=_ex_objects_count key=_ex_class_id}}
          {{if $_ex_objects_count}}
          <tr>
            <td class="text">
              <strong style="float: right;" class="ex-object-result">
                {{if $ex_objects_results.$_ex_class_id !== null}}
                   = {{$ex_objects_results.$_ex_class_id}}
                {{/if}}
              </strong>

              <a href="#1" onclick="$(this).up('tr').addUniqueClassName('selected'); ExObject.loadExObjects('{{$reference_class}}', '{{$reference_id}}', 'ex_class-list-{{$uid_exobject}}', 2, '{{$_ex_class_id}}', {other_container: this.up('tr'), readonly: {{$readonly|ternary:1:0}}}); return false;">
                {{$ex_classes.$_ex_class_id->name}}
              </a>
            </td>
            <td class="narrow">
              {{if !$readonly && isset($ex_classes_creation.$_ex_class_id|smarty:nodefaults)}}
                {{assign var=_ex_class_event value=$ex_classes_creation.$_ex_class_id|@reset}}
                <button class="add notext compact"
                        onclick="showExClassForm('{{$_ex_class_id}}', '{{$reference_class}}-{{$reference_id}}', '{{$_ex_class_event->host_class}}-{{$_ex_class_event->event_name}}', null, '{{$_ex_class_event->event_name}}', '@ExObject.refreshSelf.{{$self_guid}}');">
                  {{tr}}New{{/tr}}
                </button>
              {{/if}}
            </td>
            <td class="narrow" style="text-align: right;">
              <span class="compact ex-object-count">{{$_ex_objects_count}}</span>
              <button class="right notext compact"
                      onclick="$(this).up('tr').addUniqueClassName('selected'); ExObject.loadExObjects('{{$reference_class}}', '{{$reference_id}}', 'ex_class-list-{{$uid_exobject}}', 2, '{{$_ex_class_id}}', {other_container: this.up('tr'), readonly: {{$readonly|ternary:1:0}}})">
              </button>
            </td>
          </tr>
          {{/if}}
        {{foreachelse}}
          <tr>
            <td colspan="3" class="empty">Aucun formulaire saisi</td>
          </tr>
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