<script type="text/javascript">
loadExObjectsList = function(element, reference_class, reference_id, ex_class_id){
  element = $(element);
  
  var row  = element.up('tr');
  var body = element.up('tbody');
  var otherContainer = body.down("tr:first");
  
  var listContainer = body.down('.list-container');
  if (listContainer.visible()) {
    listContainer.hide();
    row.removeClassName('selected');
    body.removeClassName('opened');
    return;
  }
  
  element.up('table').select('.list-container').each(function(c){
    c.previous('tr').removeClassName('selected');
  });
  
  listContainer.show();
  
  row.addClassName('selected');
  body.addClassName('opened');
  ExObject.loadExObjects(reference_class, reference_id, listContainer.down('td'), 1, ex_class_id, {
    other_container: otherContainer
  });
};

filterExClasses = function(input){
  var keyword = input.value.toLowerCase();
  var lines = $(input).up('table').select("tbody[data-name]");
  
  if (keyword == "") {
    lines.invoke("show");
    return;
  }
  
  lines.invoke("hide");
  
  lines.filter(function(line){
    return line.get('name').toLowerCase().indexOf(keyword) > -1;
  }).invoke("show");
};
</script>

{{if "digitalpen"|module_active}}
  {{mb_include module=digitalpen template=inc_widget_forms_to_validate object_guid="$reference_class-$reference_id"}}
{{/if}}

<table class="main tbl treegrid">
  <tr>
    <td style="width: 1px;"></td>
    <td colspan="3">
      {{if $ex_classes_creation|@count}}
        <select onchange="ExObject.showExClassFormSelect.defer(this, '{{$self_guid}}')" style="width: 20em; max-width: 35em; float: left;">
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

      <label style="float: right; display: block;">
        Recherche
        <input type="text"{{* type="search" *}} onkeyup="filterExClasses(this)" size="15" />
        <button class="cancel notext" onclick="var input = $(this).previous(); $V(input,''); filterExClasses(input)"></button>
      </label>
    </td>
  </tr>

  {{foreach from=$ex_class_categories item=_category}}
    {{if $_category->ex_class_category_id}}
      <tr>
        <td style="background: #{{$_category->color}}"></td>
        <th colspan="3" style="text-align: left;" title="{{$_category->description}}">
          {{$_category}}
        </th>
      </tr>
    {{/if}}

    {{foreach from=$_category->_ref_ex_classes item=_ex_class}}
      {{assign var=_ex_class_id value=$_ex_class->_id}}

      {{if array_key_exists($_ex_class_id,$ex_objects_counts)}}
        {{assign var=_ex_objects_count value=$ex_objects_counts.$_ex_class_id}}
        {{if $_ex_objects_count}}
          <tbody data-name="{{$ex_classes.$_ex_class_id->name}}">
            <tr>
              <td style="background: #{{$_category->color}}"></td>
              <td class="text">
                <strong style="float: right;" class="ex-object-result">
                  {{if $ex_objects_results.$_ex_class_id !== null}}
                    = {{$ex_objects_results.$_ex_class_id}}
                  {{/if}}
                </strong>

                <a href="#1" class="tree-folding" onclick="loadExObjectsList(this, '{{$reference_class}}', '{{$reference_id}}', '{{$_ex_class_id}}'); return false;">
                  {{$ex_classes.$_ex_class_id->name}}
                </a>
              </td>

              <td class="narrow">
                {{if isset($ex_classes_creation.$_ex_class_id|smarty:nodefaults)}}
                  {{assign var=_ex_class_event value=$ex_classes_creation.$_ex_class_id|@reset}}
                  <button class="add notext compact"
                          onclick="showExClassForm('{{$_ex_class_id}}', '{{$reference_class}}-{{$reference_id}}', '{{$_ex_class_event->host_class}}-{{$_ex_class_event->event_name}}', null, '{{$_ex_class_event->event_name}}', '@ExObject.refreshSelf.{{$self_guid}}');">
                    {{tr}}New{{/tr}}
                  </button>
                {{/if}}
              </td>

              <td class="narrow ex-object-count" style="text-align: right;">
                {{$_ex_objects_count}}
              </td>
            </tr>
            <tr style="display: none;" class="list-container">
              <td colspan="3"></td>
            </tr>
          </tbody>
        {{/if}}
      {{/if}}
    {{/foreach}}
  {{foreachelse}}
    <tr>
      <td colspan="3" class="empty">Aucun formulaire saisi</td>
    </tr>
  {{/foreach}}
</table>