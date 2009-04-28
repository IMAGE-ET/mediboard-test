{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">

	<!-- CCodeCCAM -->  
	{{assign var=class value=CCodeCCAM}}
	  
	<tr>
	  <th class="category" colspan="100">{{tr}}{{$class}}{{/tr}}</th>
	</tr>
	
	<tr>
	  {{assign var=var value=use_cache}}
	  <th>
	    <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
	      {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
	    </label>  
	  </th>
	  <td>
	    <select class="bool" name="{{$m}}[{{$class}}][{{$var}}]">
	      <option value="0" {{if "0" == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>{{tr}}bool.0{{/tr}}</option>
	      <option value="1" {{if "1" == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>{{tr}}bool.1{{/tr}}</option>
	    </select>
	  </td>
	</tr>
	
		{{assign var=class value=CCodable}}
		<tr>
	  {{assign var=var value=use_getMaxCodagesActes}}
	  <th>
	    <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
	      {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
	    </label>  
	  </th>
	  <td>
	    <select class="bool" name="{{$m}}[{{$class}}][{{$var}}]">
	      <option value="0" {{if "0" == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>{{tr}}bool.0{{/tr}}</option>
	      <option value="1" {{if "1" == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>{{tr}}bool.1{{/tr}}</option>
	    </select>
	  </td>
	</tr>
	
	
  <tr>
    <td class="button" colspan="6">
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
  </tr>
</table>

</form>

{{include file="../../system/templates/configure_dsn.tpl" dsn=ccamV2}}

<h2>Import de la base de données CCAM</h2>

<script type="text/javascript">

function startCCAM() {
  var CCAMUrl = new Url;
  CCAMUrl.setModuleAction("dPccam", "httpreq_do_add_ccam");
  CCAMUrl.requestUpdate("ccam");
}

function startNGAP(){
  var NGAPUrl = new Url;
  NGAPUrl.setModuleAction("dPccam", "httpreq_do_add_ngap");
  NGAPUrl.requestUpdate("ngap");
}

</script>

<table class="tbl">
  <tr>
    <th>Action</th>
    <th>Status</th>
  </tr>
  
  <tr>
    <td><button class="tick" onclick="startCCAM()" >Importer la base de données CCAM</button></td>
    <td id="ccam"></td>
  </tr>
</table>

<h2>Import de la base de codes NGAP</h2>
<table class="tbl">
  <tr>
    <th>Action</th>
    <th>Status</th>
  </tr>
  
  <tr>
    <td><button class="tick" onclick="startNGAP()" >Importer la base de codes NGAP</button></td>
    <td id="ngap"></td>
  </tr>
</table>
