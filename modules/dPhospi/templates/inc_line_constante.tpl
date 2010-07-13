<tr>
  <td>{{tr}}{{$_constante->_class_name}}{{/tr}}</td>
  <td>
    <div style="float: right">
    {{mb_include module=system template=inc_object_history object=$_constante}}
    </div>
  	{{$_constante->_ref_user}}
  </td>
  <td  style="text-align: center">
     {{mb_ditto name=datetime value=$_constante->datetime|date_format:$dPconfig.date}}
  </td>
  <td>{{$_constante->datetime|date_format:$dPconfig.time}}</td>
	<td colspan="2">
		{{foreach from=$params key=_key item=_field}}
		  {{if $_constante->$_key != null && $_key|substr:0:1 != "_"}}
			  {{$_key}}: {{$_constante->$_key}} ({{$_field.unit}}), &nbsp;
			{{/if}}
		{{/foreach}}
	</td>
	<td></td>
</tr> 