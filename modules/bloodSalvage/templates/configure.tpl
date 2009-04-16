{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  <table class="form">    
	  {{if array_key_exists('dPmedicament', $modules)}}
		  {{assign var="var" value="inLivretTherapeutique"}}
		  <tr>
		    <th class="category" colspan="2">{{tr}}config-{{$m}}{{/tr}}</th>
		  </tr>
		  <tr>
		    <th style="width:50%">
		        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
		    </th>
		    <td>
		      <label for="{{$m}}[{{$var}}]_1">Oui</label>
		      <input type="radio" name="{{$m}}[{{$var}}]" value="1" {{if $dPconfig.$m.$var == "1"}}checked="checked"{{/if}}/> 
		      <label for="{{$m}}[{{$var}}]_0">Non</label>
		      <input type="radio" name="{{$m}}[{{$var}}]" value="0" {{if $dPconfig.$m.$var == "0"}}checked="checked"{{/if}}/> 
		    </td>             
		  </tr>
	  {{else}}  
		  {{assign var="var" value="AntiCoagulantList"}}
		  <tr>
		    <th style="width:50%">
		        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
		    </th>
		    <td>
		      <input type="text" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}" size="40" /> 
		    </td>           
		  </tr>
	  {{/if}}
	  <tr>
	    <td class="button" colspan="100">
	      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
	    </td>
	  </tr>
  </table>
</form>

    {{if !array_key_exists('dPmedicament', $modules)}}

      <div class="big-info">
      Les Anticoagulants sont à renseignés ci-dessus, séparés par des | .
      </div>
      
      {{/if}}