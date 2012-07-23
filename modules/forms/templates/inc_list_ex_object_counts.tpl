<table class="main tbl">
  {{foreach from=$ex_objects_counts_by_event key=_class item=_ex_classes_by_event}}
    <tr>
      <th class="title" colspan="2">{{tr}}{{$_class}}{{/tr}}</th>
    </tr>
    {{foreach from=$_ex_classes_by_event key=key item=_ex_classes}}
      <tr>
        <th class="category" colspan="2">{{tr}}{{$key}}{{/tr}}</th>
      </tr>
      {{foreach from=$_ex_classes key=_ex_class_id item=_ex_class_count}}
        <tr id="row-ex_class-{{$_ex_class_id}}">
          <td>
            {{assign var=_ex_class value=$ex_classes.$_ex_class_id}}
            
            <div style="float: right;">
              <span {{if $_ex_class->conditional}}style="background: #7e7;" title="{{tr}}CExClass-conditional{{/tr}}"{{/if}}>&nbsp;
              </span><span {{if $_ex_class->disabled}}style="background: #aaa;" title="{{tr}}CExClass-disabled{{/tr}}"{{/if}}>&nbsp;&nbsp;
              </span>
            </div>
            
            {{$_ex_class->name}}
          </td>
          <td class="narrow" style="text-align: right;">
            {{$_ex_class_count}} 
            <button class="right notext compact"
                    onclick="ExObject.loadExObjects(null, null, $('list-ex_object'), 2, {{$_ex_class_id}}, Object.extend(getForm('filter-ex_object').serialize(true), {a: 'ajax_list_ex_object'})); $('row-ex_class-{{$_ex_class_id}}').addUniqueClassName('selected')"></button>
          </td>
        </tr>
      {{/foreach}}
    {{/foreach}}
  {{foreachelse}}
    <tr>
      <td class="empty">{{tr}}CExObject.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>
