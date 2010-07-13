{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  function doAction(evenement) {
    var url = new Url("hprimxml", "ajax_do_cfg_action");
    url.addParam("evenement", evenement);
    url.requestUpdate('install_'+evenement, { onComplete: function(){checkSchemaFile(evenement);}});
  }
  
  function checkSchemaFile(evt) {
    var url = new Url("hprimxml", "ajax_check_schema_file");
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
    <th class="category">Evénement</th>
    <th class="category">Version</th>
    <th class="category">{{tr}}Status{{/tr}}</th>
    <th class="category">{{tr}}Action{{/tr}}</th>
    <th class="category">Validation</th>
    <th class="category">Acquittement</th>
  </tr>
    
  {{foreach from=$evenements key=_evt item=_versions}}  
  <tr>
    <td>{{tr}}config-hprimxml-{{$_evt}}{{/tr}}</td>
    <td>
      <form name="editConfig_extract" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post"
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
    <td>
      <form name="editConfig_send_ack" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this);">
        <input type="hidden" name="m" value="system" />
        <input type="hidden" name="dosql" value="do_configure" />
        
        <label for="send_ack_{{$_evt}}_1">{{tr}}bool.1{{/tr}}</label>
        <input type="radio" name="hprimxml[{{$_evt}}][send_ack]" value="1" onchange="this.form.onsubmit();" {{if $dPconfig.hprimxml.$_evt.send_ack == "1"}}checked="checked"{{/if}}/>
        <label for="send_ack_{{$_evt}}_0">{{tr}}bool.0{{/tr}}</label>
        <input type="radio" name="hprimxml[{{$_evt}}][send_ack]" value="0" onchange="this.form.onsubmit();" {{if $dPconfig.hprimxml.$_evt.send_ack == "0"}}checked="checked"{{/if}}/>
      </form>
    </td>
  </tr>
  {{/foreach}}
</table>