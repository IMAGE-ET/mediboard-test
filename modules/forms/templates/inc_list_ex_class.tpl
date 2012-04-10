<script type="text/javascript">
toggleExClassCat = function(className){
  $$(".ex-class-cat").invoke("hide");
  $("ex-class-"+className).show();
}

Main.add(function(){
  toggleExClassCat($V($("ex-class-select")));
});
</script>

<button type="button" class="new" onclick="ExClass.edit('0')">
  {{tr}}CExClass-title-create{{/tr}}
</button>
<br />

<div style="{{if $class_tree|@count == 0}} display: none; {{/if}}">
  <select onclick="toggleExClassCat($V(this))" id="ex-class-select" style="font-size: 1.2em;">
  {{foreach from=$class_tree item=_by_class key=_class}}
    <option value="{{$_class}}" {{if $_class == $ex_class->host_class}} selected="selected" {{/if}}>
      ({{$counts.$_class}})
      {{if $_class != "CMbObject"}}
        {{tr}}{{$_class}}{{/tr}}
      {{else}}
        Non classé
      {{/if}}
    </option>
  {{/foreach}}
  </select>
  <br />
</div>

{{foreach from=$class_tree item=_by_class key=_class}}
  <table class="main tbl ex-class-cat" id="ex-class-{{$_class}}" style="display: none;">
    {{foreach from=$_by_class item=_by_event key=_event}}
      {{if $_event != "void"}}
      <tr>
        <th>{{tr}}{{$_class}}-event-{{$_event}}{{/tr}}</th>
      </tr>
      {{/if}}

      {{foreach from=$_by_event item=_ex_class}}
        <tr data-ex_class_id="{{$_ex_class->_id}}">
          <td class="text" style="min-width: 16em;">
            <div style="float: right;">
              <span {{if $_ex_class->conditional}}style="background: #7e7;" title="{{tr}}CExClass-conditional{{/tr}}"{{/if}}>&nbsp;
              </span><span {{if $_ex_class->disabled}}style="background: #aaa;" title="{{tr}}CExClass-disabled{{/tr}}"{{/if}}>&nbsp;&nbsp;
              </span>
            </div>

            <a href="#1" onclick="ExClass.edit({{$_ex_class->_id}})">
              {{mb_value object=$_ex_class field=name}}
            </a>
          </td>
        </tr>
      {{/foreach}}
    {{/foreach}}
  </table>
{{foreachelse}}
  <table class="main tbl">
    <tr>
      <td class="empty">{{tr}}CExClass.none{{/tr}}</td>
    </tr>
  </table>
{{/foreach}}
