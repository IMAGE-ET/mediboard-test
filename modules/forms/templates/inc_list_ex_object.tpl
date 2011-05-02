
{{*
<table class="main tbl vertical">
{{foreach from=$all_ex_objects item=_ex_object key=key}}
  <tr>
    {{foreach from=$_ex_object->_ref_ex_class->_ref_groups item=_ex_group}}
      {{foreach from=$_ex_group->_ref_fields item=_ex_field}}
        <th><span>{{$_ex_field}}</span></th>
      {{/foreach}}
    {{/foreach}}
  </tr>
  <tr>
  	{{foreach from=$_ex_object->_ref_ex_class->_ref_groups item=_ex_group}}
		  {{foreach from=$_ex_group->_ref_fields item=_ex_field}}
        <td>{{mb_value object=$_ex_object field=$_ex_field->name}}</td>
			{{/foreach}}
		{{/foreach}}
	</tr>
{{/foreach}}
*}}

<script type="text/javascript">
printExObject = function(ex_object_id, ex_class_id, object_guid) {
  var printIframe = $("printIframe");
  printIframe.src = "about:blank";
  printIframe.src = "?m=forms&a=view_ex_object_form&ex_object_id="+ex_object_id+"&ex_class_id="+ex_class_id+"&object_guid="+object_guid+"&dialog=1&readonly=1&print=1";
}
</script>

{{foreach from=$ex_objects_by_class item=_ex_objects key=_ex_class_id}}
  {{assign var=_ex_obj value=$_ex_objects|@reset}}

	<h2>{{$ex_classes.$_ex_class_id}}</h2>
	
  <table class="main tbl vertical" style="width: 1%;">
	  <!-- First line -->
	  <tr>
      <th class="narrow"></th>
      <th class="narrow"></th>
		  {{foreach from=$_ex_objects item=_ex_object name=_ex_object}}
			  <th class="narrow">
			  	{{mb_value object=$_ex_object->_ref_first_log field=date}}
					<button class="print notext" onclick="printExObject('{{$_ex_object->_id}}', '{{$_ex_object->_ex_class_id}}', '{{$_ex_object->_ref_object->_guid}}')">{{tr}}Print{{/tr}}</button>
				</th>
			{{/foreach}}
    </tr>
		
	  {{foreach from=$_ex_obj->_ref_ex_class->_ref_groups item=_ex_group name=_ex_group}}
			<tr>
				<th rowspan="{{$_ex_group->_ref_fields|@count}}" style="word-wrap: break-word; white-space: normal;">
					<span>{{$_ex_group}}</span>
				</th>
				
        {{foreach from=$_ex_group->_ref_fields item=_ex_field}}
				  <td style="font-weight: bold;">
					  {{mb_label object=$_ex_obj field=$_ex_field->name}}</span>
					</td>
					
				  {{foreach from=$_ex_objects item=_ex_object2 name=_ex_object2}}
            <td class="text">
            	{{mb_value object=$_ex_object2 field=$_ex_field->name}}
						</td>
						
						{{if $smarty.foreach._ex_object2.last}}
						  </tr>
						{{/if}}
				  {{/foreach}}
        {{/foreach}}
		{{/foreach}}
  </table>

{{/foreach}}