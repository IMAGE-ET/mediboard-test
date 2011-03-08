<script type="text/javascript">
Main.add(function(){
  Control.Tabs.create("tab-classes");
});
</script>

<button type="button" class="new" onclick="ExClass.edit('0')">
  {{tr}}CExClass-title-create{{/tr}}
</button>

<ul class="control_tabs" id="tab-classes">
{{foreach from=$class_tree item=_by_class key=_class}}
  <li>
  	<a href="#tab-{{$_class}}">{{tr}}{{$_class}}{{/tr}}</a>
  </li>
{{/foreach}}
</ul>
<hr class="control_tabs" />

{{foreach from=$class_tree item=_by_class key=_class}}
	<table class="main tbl" id="tab-{{$_class}}">
    {{foreach from=$_by_class item=_by_event key=_event}}
	    <tr>
	      <td><strong>{{$_event}}</strong></td>
	    </tr>
      {{foreach from=$_by_event item=_ex_class}}
		    <tr>
		      <td style="padding-left: 1em;">
            {{if $_ex_class->disabled}}
              <small style="float: right; color: #666;">(inactif)</small>
            {{/if}}
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