<script type="text/javascript">
/*launch = function(state) {
  var url = new Url("dPfiles", "launch_openoffice");
  url.addParam("state", state);
  url.requestUpdate("openoffice");
}*/
Main.add(function() {
  {{*if $dPconfig.dPfiles.CFile.openoffice_active*}}
    //launch(-1);
  {{*/if*}}
  Control.Tabs.create('tabs-configure', true);
});
</script>
<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_configure" />
<input type="hidden" name="m" value="system" />

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#files">{{tr}}CFile{{/tr}}</a></li>
  <!-- <li><a href="#ooo">Openoffice</a></li>-->
  <li><a href="#cdoc">{{tr}}CDocumentSender{{/tr}}</a></li>
  <li><a href="#test">{{tr}}Cfile-test_operations{{/tr}}</a></li>
</ul>
<hr class="control_tabs" />
<div id="files">
  <table class="form">
  
    <!-- CFile -->  
    {{assign var=class value=CFile}}
    {{assign var="var" value="upload_directory"}}
    <tr>
      <th style="width: 50%">
        <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-desc{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
        </label>  
      </th>
      <td style="width: 50%">
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

    <tr>
      {{assign var=var value=extensions_yoplet}}
      <th>
        <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$var}}{{/tr}}
        </label>  
      </th>
      <td><input type="text" class="str maxLength|100" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}"/></td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</div>
<!--
<div id="ooo">
  <table class="form">
    {{*assign var=class value=CFile*}}
    {{*mb_include module=system template=inc_config_bool var=openoffice_active*}}
  
    {{*mb_include module=system template=inc_config_str var=openoffice_path*}}
    {{*if $dPconfig.dPfiles.CFile.openoffice_active*}}
    <tr>
      <td style="text-align: right;">
        <button class="new" type="button" onclick="launch(1)">Lancer Openoffice</button>
        <button class="cancel" type="button" onclick="launch(0)">Arreter Openoffice</button>
      </td>
      <td>
        <div id="openoffice"></div>
      </td>
    </tr>
    {{*/if*}}
    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{*tr*}}Save{{*/tr*}}</button>
      </td>
    </tr>
  </table>
</div>
-->
<div id="cdoc">
  <table class="form">
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
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  
  </table>
</div>

<div id="test">
  {{mb_include template="inc_test_files"}}
</div>
</form>