<table class="tbl">
	<tr>
		<td class="button" colspan="10">
			<button class="new" type="button" onclick="File.upload('{{$object->_class_name}}','{{$object->_id}}', '')" >
			  {{tr}}Add{{/tr}}
			</button>
		</td>
	</tr>

  {{foreach from=$object->_ref_files item=_file}}
  <tr>
  	<td>
	    <a href="#" class="action" 
	    	 onclick="File.popup('{{$object->_class_name}}','{{$object->_id}}','{{$_file->_class_name}}','{{$_file->_id}}');"
	    	 onmouseover="ObjectTooltip.createEx(this, '{{$_file->_guid}}', 'objectViewHistory')">
	      {{$_file->file_name}}
	    </a>
	    <small>({{$_file->_file_size}})</small>
  	</td>
  	
	  <td class="button" style="width: 1px">
	    <form name="Delete-{{$_file->_guid}}" action="?m={{$m}}" enctype="multipart/form-data" method="post" onsubmit="return checkForm(this)">

      <input type="hidden" name="m" value="dPfiles" />
      <input type="hidden" name="dosql" value="do_file_aed" />
      <input type="hidden" name="del" value="1" />
      {{mb_key object=$_file}}
      {{mb_field object=$_file field="_view" hidden=1 prop=""}}
      <button class="trash notext" type="button" onclick="File.remove(this, '{{$object->_id}}', '{{$object->_class_name}}')">
        {{tr}}Delete{{/tr}}
      </button>

	    </form>

	  </td>
	  
	  <td class="button" style="width: 1px">

      <form name="Edit-{{$_file->_guid}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
     
      <input type="hidden" name="m" value="dPfiles" />
	    <input type="hidden" name="dosql" value="do_file_aed" />
	    <input type="hidden" name="del" value="0" />
      {{mb_key object=$_file}}
	   
	    <!-- Send File -->
	    {{assign var=object_class value=$object->_class_name}}
	    {{assign var=object_id    value=$object->_id        }}
		  {{mb_include module=dPfiles template=inc_file_send_button 
		     notext=notext
		  		_doc_item=$_file
		  		onComplete="File.refresh('$object_id','$object_class')"
		  }}
	   
      </form>

  	</td>
  </tr>
  
  {{foreachelse}}
  <tr>
  	<td>
  		<em>
  	    {{tr}}{{$object->_class_name}}{{/tr}} :
  	    {{tr}}CFile.none{{/tr}}
  	  </em>
  	</td>
  </tr>
  {{/foreach}}

</table>
  