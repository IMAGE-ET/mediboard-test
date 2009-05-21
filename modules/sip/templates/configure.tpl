{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<h1>Configuration du module {{tr}}{{$m}}{{/tr}}</h1>
<hr />
<script type="text/javascript">
function doAction(sAction) {
  var url = new Url;
  url.setModuleAction("sip", "ajax_do_cfg_action");
  url.addParam("action", sAction);
  url.requestUpdate(sAction);
}
</script>
<table class="tbl">
	<tr>
	  <th class="category" colspan="10">Installation des schémas HPRIM XML</th>
	</tr>
	<tr>
	  <th class="category">Action</th>
	  <th class="category">Status</th>
	</tr>
	<tr>
	  <td onclick="doAction('extractFiles');">
	    <button class="tick">Installation HPRIM 'EvementPatient'</button>
	  </td>
	  <td class="text" id="extractFiles" />
	</tr>
</table>

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  <table class="form">
    {{mb_include module=system template=configure_handler class_handler=CSipObjectHandler}}
    
    <tr>
	    {{assign var="var" value="export_segment"}}
	    <th>
	      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
	        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
	      </label>  
	    </th>
	    <td>
	      <input class="num" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}" />
	    </td>
	  </tr>
	  
	  <tr>
	    {{assign var="var" value="export_id_min"}}
	    <th>
	      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
	        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
	      </label>  
	    </th>
	    <td>
	      <input class="numchar" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}" />
	    </td>
	  </tr>
	
	  <tr>
	    {{assign var="var" value="export_id_max"}}
	    <th>
	      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
	        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
	      </label>  
	    </th>
	    <td>
	      <input class="numchar" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}" />
	    </td>
	  </tr>
	  
	  <tr>
      {{assign var="var" value="batch_count"}}
      <th>
        <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$var}}{{/tr}}
        </label>  
      </th>
      <td>
        <input class="numchar" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}" />
      </td>
    </tr>
  
    {{assign var="mod" value="sip"}}
    <tr>
      <th class="category" colspan="10">{{tr}}config-{{$mod}}{{/tr}}</th>
    </tr>
    
    {{mb_include module=system template=inc_config_bool var=server}}
		{{mb_include module=system template=inc_config_bool var=wsdl_mode}}
		    
	  <tr>
	    <td class="button" colspan="10">
	      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
	    </td>
	  </tr>
  </table>
</form>

<hr />

<!-- Import des tables -->
<script type="text/javascript">

var Action = {
  module: "sip",
  action: "ajax_export_patient",
  
  import: function (sAction) {
    var url = new Url;
    url.setModuleAction(this.module, this.action);
    url.addParam("action", sAction);
    url.requestUpdate("import-patient");
  },
}

</script>

<table class="tbl">

<tr>
  <th class="title" colspan="100">{{tr}}sip-export-classes{{/tr}}</th>
</tr>

<tr>
  <td>
    {{tr}}sip-export-class{{/tr}} '{{tr}}CPatient{{/tr}}'
  </td>
  <td>
    <button class="new" onclick="Action.import('start')">
      {{tr}}Start{{/tr}}      
    </button>
    <button class="change" onclick="Action.import('retry')">
      {{tr}}Retry{{/tr}}      
    </button>
    <button class="tick" onclick="Action.import('continue')">
      {{tr}}Continue{{/tr}}      
    </button>
  </td>
  <td id="import-patient"></td>
</tr>

</table>