{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision: 11962 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfig-CPlageconsult" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_configure" />
  
  <table class="form">

    {{assign var="class" value="CPlageconsult"}}
        
    <tr>
      <th class="category" colspan="2">{{tr}}{{$class}}{{/tr}}</th>
    </tr>
    
    {{assign var="var" value="hours_start"}}
    <tr>
      <th>
        <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
        </label>  
      </th>
      <td>
        <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
        {{foreach from=$hours item=_hour}}
          <option value="{{$_hour}}" {{if $_hour == $conf.$m.$class.$var}} selected="selected" {{/if}}>
            {{$_hour|string_format:"%02d"}}
          </option>
        {{/foreach}}
        </select>
      </td>
    </tr>
    
    {{assign var="var" value="hours_stop"}}
    <tr>
      <th>
        <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
        </label>  
      </th>
      <td>
        <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
        {{foreach from=$hours item=_hour}}
          <option value="{{$_hour}}" {{if $_hour == $conf.$m.$class.$var}} selected="selected" {{/if}}>
            {{$_hour|string_format:"%02d"}}
          </option>
        {{/foreach}}
        </select>
      </td>
    </tr>
    
    {{assign var="var" value="minutes_interval"}}
    <tr>
      <th>
        <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
        </label>  
      </th>
      <td>
        <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
        {{foreach from=$intervals item=_interval}}
          <option value="{{$_interval}}" {{if $_interval == $conf.$m.$class.$var}} selected="selected" {{/if}}>
            {{$_interval}}
          </option>
        {{/foreach}}
        </select>
      </td>
    </tr>
    
    {{assign var="var" value="hour_limit_matin"}}
    <tr>
      <th>
        <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
        </label>  
      </th>
      <td>
        <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
        {{foreach from=$hours item=_hour}}
          <option value="{{$_hour}}" {{if $_hour == $conf.$m.$class.$var}} selected="selected" {{/if}}>
            {{$_hour|string_format:"%02d"}}
          </option>
        {{/foreach}}
        </select>
      </td>
    </tr>

    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>