{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPqualite
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_configure" />
<input type="hidden" name="m" value="system" />

<table class="form">

  {{assign var="class" value="CDocGed"}}
  
  <tr>
    <th class="category" colspan="6">{{tr}}config-{{$m}}-{{$class}}{{/tr}}</th>
  </tr>
  
  {{assign var="var" value="_reference_doc"}}
  <tr>
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>    
    </th>
    <td colspan="3">
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
      <label for="{{$m}}[{{$class}}][{{$var}}]_1">Categorie-Chapitres-Numero</label>
      <br />
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
      <label for="{{$m}}[{{$class}}][{{$var}}]_0">Chapitres-Categorie-Numero</label>
    </td>             
  </tr>

  {{assign var="class" value="CChapitreDoc"}}
  
  <tr>
    <th class="category" colspan="6">{{tr}}config-{{$m}}-{{$class}}{{/tr}}</th>
  </tr>
  
  {{assign var="var" value="profondeur"}}
  <tr>
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>    
    </th>
    <td colspan="3">
      <input type="text" maxlength="1" name="{{$m}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$m.$class.$var}}" />
    </td>             
  </tr>
  
  <tr>
    <td class="button" colspan="100">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
    
  </tr>
</table>
</form>