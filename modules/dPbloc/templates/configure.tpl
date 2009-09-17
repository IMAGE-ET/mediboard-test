{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

function doReaffectation(mode_real) {
  var url = new Url;
  url.setModuleAction("dPbloc", "httpreq_reaffect_plagesop");
  url.addParam("mode_real", mode_real);
  url.requestUpdate("resultReaffectation");
}

</script>

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">

  <!-- CPlageOp -->  
  {{assign var="class" value="CPlageOp"}}
    
  <tr>
    <th class="category" colspan="100">{{tr}}{{$class}}{{/tr}}</th>
  </tr>
  
  <tr>
    {{assign var="var" value="hours_start"}}
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
      {{foreach from=$hours item=_hour}}
        <option value="{{$_hour}}" {{if $_hour == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>
          {{$_hour|string_format:"%02d"}}
        </option>
      {{/foreach}}
      </select>
    </td>

    {{assign var="var" value="hours_stop"}}
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
      {{foreach from=$hours item=_hour}}
        <option value="{{$_hour}}" {{if $_hour == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>
          {{$_hour|string_format:"%02d"}}
        </option>
      {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <th class="category" colspan="4">Paramètres d'affichage de l'impression de plannings</th>
  </tr>
  <tr>
    <th class="category">Plages vides</th>
    <th class="category" colspan="2">Ordre des colones</th>
    <th class="category">Libellés ccam</th>
  </tr>
  <tr>
    {{assign var="var" value="plage_vide"}}
    <td style="text-align: center">
      <label for="{{$m}}[{{$class}}][{{$var}}]">Oui</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
      <label for="{{$m}}[{{$class}}][{{$var}}]">Non</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
    </td> 
    {{assign var="var" value="planning"}}
    <td colspan="2" style="text-align: center">
    {{foreach from=$dPconfig.$m.$class.$var item=value key=col}}
	  <label for="{{$m}}[{{$class}}][{{$var}}][{{$col}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-{{$col}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}-{{$col}}{{/tr}}
      </label>
      <select name="{{$m}}[{{$class}}][{{$var}}][{{$col}}]">
      	<option value="patient" {{if $value=="patient"}} selected="selected"{{/if}}>Patient</option>
        <option value="sejour" {{if $value=="sejour"}} selected="selected"{{/if}}>Sejour</option>
      	<option value="interv" {{if $value=="interv"}} selected="selected"{{/if}}>Intervention</option>
      </select>
      <br />
    {{/foreach}}
    </td>
    {{assign var="var" value="libelle_ccam"}}
    <td style="text-align: center">
      <label for="{{$m}}[{{$class}}][{{$var}}]">Oui</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
      <label for="{{$m}}[{{$class}}][{{$var}}]">Non</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
    </td>      
  </tr>

  <tr>
    <th class="category" colspan="4">Blocage des plages opératoires</th>
  </tr>
  <tr>
    {{assign var="var" value="locked"}}
    <th colspan="2">
      <label for="{{$m}}[{{$class}}][{{$var}}]_1">{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}</label>
    </th>
    <td colspan="2">
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/>
      <label for="{{$m}}[{{$class}}][{{$var}}]_1">Oui</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/>
      <label for="{{$m}}[{{$class}}][{{$var}}]_0">Non</label>
    </td>          
  </tr>
  <tr>
    {{assign var="var" value="days_locked"}}
    <th colspan="2">
      <label for="{{$m}}[{{$class}}][{{$var}}]">{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}</label>
    </th>
    <td colspan="2">
      <input type="text" name="{{$m}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$m.$class.$var}}" size="2" /> jours
    </td>          
  </tr>

  {{assign var="var" value="chambre_operation"}} 
  <tr>
    <th class="category" colspan="4">{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}</th>
  </tr>
  <tr>
    <td colspan="4" style="text-align: center">
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/>
      <label for="{{$m}}[{{$class}}][{{$var}}]_1">Oui</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/>
      <label for="{{$m}}[{{$class}}][{{$var}}]_0">Non</label>
    </td>          
  </tr>
 
  <tr>
    <td class="button" colspan="6">
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
  </tr>
</table>
</form>
<table class="tbl">
  <tr>
    <th colspan="2" class="title">Réaffectation des plages opératoires</th>
  </tr>
  <tr>
    <td class="button" style="width: 1%;">
      <button class="modify" onclick="doReaffectation(1)">Réatribuer</button>
      <br />
      <button class="modify" onclick="doReaffectation(0)">Tester</button>
    </td>
    <td>
      <div id="resultReaffectation"></div>
    </td>
  </tr>
</table>