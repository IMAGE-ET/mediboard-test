<script type="text/javascript">
Main.add(function(){
  Control.Tabs.create("tab-classes");
});
</script>

<button type="button" class="new" onclick="ExClass.edit('0')">
  {{tr}}CExClass-title-create{{/tr}}
</button>

<ul class="control_tabs small wide" id="tab-classes">
{{foreach from=$class_tree item=_by_class key=_class}}
  <li style="text-align: left;">
    <a href="#tab-{{$_class}}">
      <small style="float: right;">({{$counts.$_class}})</small>
      
      {{if $_class != "CMbObject"}}
        {{tr}}{{$_class}}{{/tr}}
      {{else}}
        Non classé
      {{/if}}
    </a>
  </li>
{{/foreach}}
</ul>
<hr class="control_tabs" />

{{foreach from=$class_tree item=_by_class key=_class}}
  <table class="main tbl" id="tab-{{$_class}}" style="display: none;">
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
  {{tr}}CExClass.none{{/tr}}
{{/foreach}}