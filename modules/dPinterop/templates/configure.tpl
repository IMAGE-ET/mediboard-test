<h2>Paramètres de messages HPRIMServeurActes</h2>

{{assign var="class" value="hprim_export"}}


<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">

  <tr>
    <th class="category" colspan="100">Connexion au serveur FTP</th>
  </tr> 
  
  {{assign var="var" value="hostname"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" class="str" name="{{$m}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$m.$class.$var}}" />
    </td>
  </tr>  
    
  {{assign var="var" value="username"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" class="str" name="{{$m}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$m.$class.$var}}" />
    </td>
  </tr>  
    
  {{assign var="var" value="userpass"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="password" class="str" name="{{$m}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$m.$class.$var}}" />
    </td>
  </tr>  

  <tr>
    <th class="category" colspan="100">Options de dépots de fichier</th>
  </tr>
  
  {{assign var="var" value="fileprefix"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" class="str" name="{{$m}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$m.$class.$var}}" />
    </td>
  </tr>
  
  {{assign var="var" value="fileextension"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" class="str" name="{{$m}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$m.$class.$var}}" />
    </td>
  </tr>
  
  {{assign var="var" value="filenbroll"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <select name="{{$m}}[{{$class}}][{{$var}}]">
        <option value="1" {{if $dPconfig.$m.$class.$var == 1}}selected="selected"{{/if}}>1</option>
        <option value="2" {{if $dPconfig.$m.$class.$var == 2}}selected="selected"{{/if}}>2</option>
        <option value="3" {{if $dPconfig.$m.$class.$var == 3}}selected="selected"{{/if}}>3</option>
        <option value="4" {{if $dPconfig.$m.$class.$var == 4}}selected="selected"{{/if}}>4</option>
      </select>
    </td>
  </tr>  

  <tr>
    <td class="button" colspan="100">
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
  </tr>
</table>

</form>

<h2>Base de données de transit</h2>

{{include file="../../system/templates/configure_dsn.tpl" dsn=Transit}}