{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">

  <!-- Champs RPU -->  
  <tr>
    <th class="category" colspan="100">Mode RPU</th>
  </tr>
  
  <tr>
    {{assign var=var value=old_rpu}}
    <th>
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <select class="bool" name="{{$m}}[{{$var}}]">
        <option value="0" {{if 0 == $dPconfig.$m.$var}} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$var}}-0{{/tr}}</option>
        <option value="1" {{if 1 == $dPconfig.$m.$var}} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$var}}-1{{/tr}}</option>
      </select>
    </td>
  </tr>
  
  <tr>
    {{assign var="var" value="rpu_warning_time"}}
    <th>
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input class="str" size="40" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}" />
    </td>
  </tr>
   
  <tr>
     {{assign var="var" value="rpu_alert_time"}}
     <th>
       <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
         {{tr}}config-{{$m}}-{{$var}}{{/tr}}
       </label>  
     </th>
     <td>
       <input class="str" size="40" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}" />
     </td>
   </tr>
   
   <tr>
    {{assign var=var value=default_view}}
    <th>
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <select name="{{$m}}[{{$var}}]">
        <option value="tous" {{if "tous" == $dPconfig.$m.$var}} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$var}}-tous{{/tr}}</option>
        <option value="presents" {{if "presents" == $dPconfig.$m.$var}} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$var}}-presents{{/tr}}</option>
      </select>
    </td>
  </tr>
    
  <tr>
    <td class="button" colspan="2">
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
  </tr>
</table>

</form>
