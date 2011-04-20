
<table class="main form">
{{foreach from=$all_ex_objects item=_ex_object key=key}}
  <tr>
  	{{foreach from=$_ex_object->_ref_ex_class->_ref_groups item=_ex_group}}
		  {{foreach from=$_ex_group->_ref_fields item=_ex_field}}
			  {{assign var=_field_name value=$_ex_field->name}}
				
		    <th>{{$_ex_field}}</th>
        {{* <td>{{mb_value object=$_ex_object field=$_field_name}}</td> *}}
				
			{{/foreach}}
		{{/foreach}}
	</tr>
{{/foreach}}