<button type="button" class="new" onclick="ExClass.edit('0')">
  {{tr}}CExClass-title-create{{/tr}}
</button>

<table class="main tbl">
  <tr>
    <th class="title">Classes étendues</th>
  </tr>
  {{foreach from=$class_tree item=_by_class key=_class}}
	  <tr>
	  	<th><strong>{{tr}}{{$_class}}{{/tr}}</strong></th>
		</tr>
    {{foreach from=$_by_class item=_by_event key=_event}}
	    <tr>
	      <td style="padding-left: 1em;"><strong>{{$_event}}</strong></td>
	    </tr>
      {{foreach from=$_by_event item=_ex_class}}
		    <tr>
		      <td style="padding-left: 2em;">
		        <a href="#1" onclick="ExClass.edit({{$_ex_class->_id}})">
		          {{mb_value object=$_ex_class field=name}}
		        </a>
		      </td>
		    </tr>
      {{/foreach}}
    {{/foreach}}
  {{foreachelse}}
    <tr>
      <td colspan="4">{{tr}}CExClass.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>
<hr />