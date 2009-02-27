<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_configure" />
<input type="hidden" name="m" value="system" />

<table class="form">

  <!-- CCatalogueLabo -->  
  {{assign var="class" value="CCatalogueLabo"}}
  <tr>
    <th class="category" colspan="6">{{tr}}{{$class}}{{/tr}}</th>
  </tr>
  
  <tr>
    {{assign var="var" value="nb_upload_files"}}
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
    {{assign var="var" value="upload_max_filesize"}}
    <th>
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td><input type="text" class="str maxLength|4" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}"/></td>
  </tr>  
 <tr>
    <th class="category" colspan="2">Choix du service d'envoi des fichiers</th>
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
        <option value="CMedicapSender" {{if "CMedicapSender" == $dPconfig.$m.$var}} selected="selected" {{/if}}>e-Cap</option>
        <option value="CMedinetSender" {{if "CMedinetSender" == $dPconfig.$m.$var}} selected="selected" {{/if}}>Medinet</option>
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