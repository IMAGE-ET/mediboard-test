<script type="text/javascript">
  showExClassFormSelect = function(select){
    var selected = select.options[select.selectedIndex];
    var reference_class = selected.get("reference_class");
    var reference_id    = selected.get("reference_id");
    var host_class      = selected.get("host_class");
    var event_name      = selected.get("event_name");
    
    showExClassForm(selected.value, reference_class+"-"+reference_id, host_class+"-"+event_name, null, event_name, '@ExObject.refreshSelf.{{$self_guid}}');
    
    select.selectedIndex = 0;
  }
</script>

<table class="main layout">
  <tr>
    <td class="narrow" style="min-width: 20em; vertical-align: top;">
      <table class="main tbl">
        
        {{if $ex_classes_creation|@count}}
        <select onchange="showExClassFormSelect(this)" style="width: 20em;">
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
        
        {{foreach from=$ex_objects_counts item=_ex_objects_count key=_ex_class_id}}
          {{if $_ex_objects_count}}
          <tr>
            <td class="text">
              <a href="#1" onclick="$(this).up('tr').addUniqueClassName('selected'); ExObject.loadExObjects('{{$reference_class}}', '{{$reference_id}}', 'ex_class-list', 2, '{{$_ex_class_id}}'); return false;">
                {{$ex_classes.$_ex_class_id->name}}
              </a>
            </td>
            <td class="narrow" style="text-align: right;">
              {{$_ex_objects_count}}
              <button class="right notext compact"
                      onclick="$(this).up('tr').addUniqueClassName('selected'); ExObject.loadExObjects('{{$reference_class}}', '{{$reference_id}}', 'ex_class-list', 2, '{{$_ex_class_id}}')">
              </button>
            </td>
          </tr>
          {{/if}}
        {{foreachelse}}
          <tr>
            <td colspan="2" class="empty">Aucun formulaire saisi</td>
          </tr>
        {{/foreach}}
      </table>
    </td>
    <td id="ex_class-list" style="vertical-align: top;">
      <div class="small-info">
        Cliquez sur le bouton correspondant au formulaire dont vous voulez voir le détail
      </div>
    </td>
  </tr>
</table>