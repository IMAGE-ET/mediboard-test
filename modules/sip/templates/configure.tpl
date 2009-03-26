{{*  
 * @package Mediboard
 * @subpackage sip
 * @version $Revision: 
 * @author Poiron Yohann
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
*}}

<h1>Configuration du module {{tr}}{{$m}}{{/tr}}</h1>
<hr />
<script type="text/javascript">
function doAction(sAction) {
  var url = new Url;
  url.setModuleAction("sip", "httpreq_do_cfg_action");
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
    {{include file="../../system/templates/configure_handler.tpl" class_handler=CSipObjectHandler}}
    
    {{assign var="mod" value="sip"}}
    <tr>
      <th class="category" colspan="10">{{tr}}config-{{$mod}}{{/tr}}</th>
    </tr>
    {{assign var="var" value="server"}}
    <tr>
      <th>
        <label for="{{$mod}}[{{$var}}]" title="{{tr}}config-{{$mod}}-{{$var}}{{/tr}}">
          {{tr}}config-{{$mod}}-{{$var}}{{/tr}}
        </label>  
      </th>
      <td>
        <label for="{{$m}}[{{$var}}]">Non</label>
        <input type="radio" name="{{$m}}[{{$var}}]" value="0" {{if $dPconfig.$m.$var == "0"}}checked="checked"{{/if}}/> 
        <label for="{{$m}}[{{$var}}]">Oui</label>
        <input type="radio" name="{{$m}}[{{$var}}]" value="1" {{if $dPconfig.$m.$var == "1"}}checked="checked"{{/if}}/> 
      </td>
    </tr>
    
	  {{assign var="var" value="wsdl_mode"}}
	  <tr>
	    <th>
	      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
	        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
	      </label>  
	    </th>
	    <td>
	      <label for="{{$m}}[{{$var}}]">Safe</label>
	      <input type="radio" name="{{$m}}[{{$var}}]" value="1" {{if $dPconfig.$m.$var == "1"}}checked="checked"{{/if}}/> 
	      <label for="{{$m}}[{{$var}}]">Brute</label>
	      <input type="radio" name="{{$m}}[{{$var}}]" value="0" {{if $dPconfig.$m.$var == "0"}}checked="checked"{{/if}}/> 
	    </td>             
    </tr>
	  <tr>
	    <td class="button" colspan="10">
	      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
	    </td>
	  </tr>
  </table>
</form>
 