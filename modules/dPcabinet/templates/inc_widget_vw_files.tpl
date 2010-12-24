<table class="form">
	<tr>
		<td class="button" colspan="10">
      {{if $can->admin}}
      <form name="DeleteAll-{{$object->_guid}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

      <input type="hidden" name="m" value="dPfiles" />
      <input type="hidden" name="dosql" value="do_file_multi_delete" />
      <input type="hidden" name="del" value="1" />
      <input type="hidden" name="object_guid" value="{{$object->_guid}}">
     
		  <button class="trash" type="button" style="float: right;" onclick="File.removeAll(this, '{{$object->_guid}}')">
        {{tr}}Delete-all{{/tr}}
      </button>
      
      </form>
      {{/if}}

			<button class="new" type="button" onclick="File.upload('{{$object->_class_name}}','{{$object->_id}}', '')" >
			  {{tr}}Add{{/tr}}
			</button>

			{{if $app->user_prefs.directory_to_watch}} 
      <button class="new yopletbutton" type="button" disabled="disabled" onclick="File.applet.modalOpen('{{$object->_guid}}')">
        {{tr}}Upload{{/tr}}
      </button>
      {{/if}}
		
		</td>
	</tr>
</table>

<table class="tbl">
  {{foreach from=$object->_ref_files item=_file}}
    {{assign var=object_class value=$object->_class_name}}
    {{assign var=object_id    value=$object->_id        }}
    <tr id="tr_{{$_file->_guid}}">
      <td id="td_{{$_file->_guid}}">
        {{mb_include template="inc_widget_line_file"}}
  	  </td>

      {{if $conf.dPfiles.system_sender}}
  	  <td class="button" style="width: 1px">
        <form name="Edit-{{$_file->_guid}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
       
        <input type="hidden" name="m" value="dPfiles" />
  	    <input type="hidden" name="dosql" value="do_file_aed" />
  	    <input type="hidden" name="del" value="0" />
        {{mb_key object=$_file}}
  	   
  	    <!-- Send File -->
  		  {{mb_include module=dPfiles template=inc_file_send_button 
  		     notext=notext
  		  		_doc_item=$_file
  		  		onComplete="File.refresh('$object_id','$object_class')"
  		  }}
        </form>
    	</td>
  		{{/if}}
  		
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
  