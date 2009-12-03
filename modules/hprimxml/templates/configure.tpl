{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<h1>Configuration du module {{tr}}{{$m}}{{/tr}}</h1>
<hr />
<script type="text/javascript">
  function doAction(evenement) {
    var url = new Url;
    url.setModuleAction("hprimxml", "ajax_do_cfg_action");
    url.addParam("evenement", evenement);
    url.requestUpdate('install_'+evenement, {waitingText: null, onComplete: function(){checkSchemaFile(evenement);}});
  }
  
  function checkSchemaFile(evt) {
    var url = new Url;
    url.setModuleAction("hprimxml", "ajax_check_schema_file");
    url.addParam("evenement", evt);
    url.requestUpdate('status_'+evt);
  }
  
  Main.add( function(){
    {{foreach from=$evenements key=_evt item=_versions}}
      checkSchemaFile('{{$_evt}}');
    {{/foreach}}
  });
</script>
<table class="tbl">
  <tr>
    <th class="category" colspan="10">Schémas HPRIM XML</th>
  </tr>
  <tr>
    <th class="category">Evénement</th>
    <th class="category">Version</th>
    <th class="category">Status</th>
    <th class="category">Action</th>
    <th class="category">Validation</th>
  </tr>
    
  {{foreach from=$evenements key=_evt item=_versions}}  
  <tr>
    <td>{{tr}}config-hprimxml-{{$_evt}}{{/tr}}</td>
    <td>
      <form name="editConfig_schema" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post"
       onsubmit="return onSubmitFormAjax(this, { onComplete: function() { checkSchemaFile('{{$_evt}}')}});">
        <input type="hidden" name="m" value="system" />
        <input type="hidden" name="dosql" value="do_configure" />
  
        <select name="hprimxml[{{$_evt}}][version]" onchange="this.form.onsubmit();">
        {{foreach from=$_versions item=_version}}
          <option value="{{$_version}}" {{if ($_version == $dPconfig.hprimxml.$_evt.version)}}selected="selected"{{/if}}>v. {{$_version}}</option>
        {{/foreach}}
        </select>
      </form>
    </td>
    <td id="status_{{$_evt}}">
      
    </td>
    <td onclick="doAction('{{$_evt}}');">
      <button class="tick">Installation</button>
      <div class="text" id="install_{{$_evt}}"></div>
    </td>
    <td>
      <form name="editConfig_validation" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this);">
        <input type="hidden" name="m" value="system" />
        <input type="hidden" name="dosql" value="do_configure" />
        
        <label for="validation_{{$_evt}}_1">{{tr}}bool.1{{/tr}}</label>
        <input type="radio" name="hprimxml[{{$_evt}}][validation]" value="1" onchange="this.form.onsubmit();" {{if $dPconfig.hprimxml.$_evt.validation == "1"}}checked="checked"{{/if}}/>
        <label for="validation_{{$_evt}}_0">{{tr}}bool.0{{/tr}}</label>
        <input type="radio" name="hprimxml[{{$_evt}}][validation]" value="0" onchange="this.form.onsubmit();" {{if $dPconfig.hprimxml.$_evt.validation == "0"}}checked="checked"{{/if}}/>
      </form>
    </td>
  </tr>
  {{/foreach}}
</table>

<hr />

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  <table class="form">
    {{assign var="var" value="functionPratImport"}}
    <tr>
      <th>
        <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$var}}{{/tr}}
        </label>  
      </th>
      <td>
        <input class="text" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}" />
      </td>
    </tr>
    
    {{assign var="var" value="medecinIndetermine"}}
    <tr>
      <th>
        <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$var}}{{/tr}}
        </label>  
      </th>
      <td>
        <input class="text" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}" />
      </td>
    </tr>
    
    {{mb_include module=system template=inc_config_bool var=medecinActif}}
    
    {{mb_include module=system template=inc_config_bool var=mvtComplet}}
    
    {{mb_include module=system template=inc_config_bool var=strictSejourMatch}}
   
    <tr>
      <td class="button" colspan="10">
        <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>