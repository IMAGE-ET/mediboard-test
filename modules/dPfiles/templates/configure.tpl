<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_configure" />
<input type="hidden" name="m" value="system" />

<table class="form">

  <!-- CFile -->  
  {{assign var=class value=CFile}}
  <tr>
    <th class="category" colspan="10">{{tr}}{{$class}}{{/tr}}</th>
  </tr>
  
  {{assign var="var" value="upload_directory"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input name="{{$m}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$m.$class.$var}}" />
    </td>
  </tr>  
		
  <tr>
    {{assign var=var value=nb_upload_files}}
    <th>
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <select class="num" name="{{$m}}[{{$var}}]">
      {{html_options values=$listNbFiles output=$listNbFiles selected=$dPconfig.$m.$var}}
      </select>
    </td>
  </tr>  
    
  <tr>
    {{assign var=var value=upload_max_filesize}}
    <th>
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td><input type="text" class="str maxLength|4" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}"/></td>
  </tr>  

  <!-- CFileCategory -->  
  {{assign var=class value=CFilesCategory}}
  <tr>
    <th class="category" colspan="10">{{tr}}{{$class}}{{/tr}}</th>
  </tr>
  
  {{mb_include module=system template=inc_config_bool var=show_empty}}

  <!-- CDocumentSender -->  
  {{assign var=class value=CDocumentSender}}

	<tr>
    <th class="category" colspan="10">{{tr}}{{$class}}{{/tr}}</th>
  </tr>
  
  <tr>
    {{assign var="var" value="system_sender"}}
    <th>
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <select class="str" name="{{$m}}[{{$var}}]">
        <option value="" {{if "" == $dPconfig.$m.$var}} selected="selected" {{/if}}>Aucun</option>
        <option value="CEcDocumentSender" {{if "CEcDocumentSender" == $dPconfig.$m.$var}} selected="selected" {{/if}}>{{tr}}CEcDocumentSender{{/tr}}</option>
        <option value="CMedinetSender"    {{if "CMedinetSender"    == $dPconfig.$m.$var}} selected="selected" {{/if}}>{{tr}}CMedinetSender{{/tr}}</option>
      </select>
    </td>
  </tr>  

  {{assign var="var" value="rooturl"}}
   <tr>
     <th>
       <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
         {{tr}}config-{{$m}}-{{$var}}{{/tr}}
       </label>  
     </th>
     <td>
       <input class="str" size="30" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}" />
     </td>
   </tr>
   
  <tr>
    <td class="button" colspan="2">
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
  </tr>

</table>
</form>