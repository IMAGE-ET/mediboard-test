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
  	<a href="#tab-{{$_class}}">
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
	<table class="main tbl" id="tab-{{$_class}}">
    {{foreach from=$_by_class item=_by_event key=_event}}
		  {{if $_event != "void"}}
	    <tr>
	      <td><strong>{{$_event}}</strong></td>
	    </tr>
			{{/if}}
			
      {{foreach from=$_by_event item=_ex_class}}
		    <tr>
		      <td class="text" style="min-width: 15em;">
            <div style="float: right;">
              <span {{if $_ex_class->conditional}}style="background: #7e7;" title="{{tr}}CExClass-conditional{{/tr}}"{{/if}}>&nbsp;&nbsp;</span>
              <span {{if $_ex_class->disabled}}   style="background: #999;" title="{{tr}}CExClass-disabled{{/tr}}"{{/if}}>&nbsp;&nbsp;</span>
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