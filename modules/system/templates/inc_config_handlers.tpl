{{* $Id: configure.tpl 13345 2011-10-01 12:32:00Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 13345 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="EditConfig-handlers" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">  
  {{assign var=types value="-"|explode:"index_handlers-object_handlers-template_placeholders"}}
  {{foreach from=$types item=type}}
  {{assign var=class value=$type}}
  <tr><th class="title" colspan="2">{{tr}}{{$class}}{{/tr}}</th></tr>

  {{foreach from=$conf.$class key=var item=value}}
        
    {{assign var=field  value="$class[$var]"}}
    {{assign var=locale value=$var}}
    <tr>
      <th style="width: 50%">
        <label for="{{$field}}" title="{{tr}}{{$locale}}-desc{{/tr}}">
          {{tr}}{{$locale}}{{/tr}}
        </label>  
      </th>

      <td>
        <label for="{{$field}}_1">{{tr}}bool.1{{/tr}}</label>
        <input type="radio" name="{{$field}}" value="1" {{if $value == "1"}}checked="checked"{{/if}}/>
        <label for="{{$field}}_0">{{tr}}bool.0{{/tr}}</label>
        <input type="radio" name="{{$field}}" value="0" {{if $value != "1"}}checked="checked"{{/if}}/>
      </td>
    </tr>

  {{foreachelse}}
  <tr><td colspan="2" class="empty">{{tr}}CMbIndexHandler{{/tr}}</td></tr>
  {{/foreach}}
	
{{/foreach}}

  <tr>
    <td class="button" colspan="6">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>

</table>

</form>
