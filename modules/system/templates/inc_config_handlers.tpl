{{* $Id: configure.tpl 13345 2011-10-01 12:32:00Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 13345 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="form">    
  {{foreach from=$handlers key=_handler_name item=_handler_object}}
    <tr>
      <th style="width:30%"> {{$_handler_name}} </th>
      <td>  
        <form name="editConfigHandler-{{$_handler_name}}" action="?m={{$m}}&amp;{{$actionType}}=configure" 
            method="post" onsubmit="return onSubmitFormAjax(this);">
          <input type="hidden" name="m" value="system" />
          <input type="hidden" name="dosql" value="do_configure" />
          
          <label for="handlers_{{$_handler_name}}_1">{{tr}}bool.1{{/tr}}</label>
          <input type="radio" name="object_handlers[{{$_handler_name}}]" value="1" onchange="this.form.onsubmit();" 
            {{if array_key_exists($_handler_name, $conf.object_handlers) &&
              $conf.object_handlers.$_handler_name == "1"}}checked="checked"
            {{/if}}/>
          <label for="object_handlers_{{$_handler_name}}_0">{{tr}}bool.0{{/tr}}</label>
          <input type="radio" name="object_handlers[{{$_handler_name}}]" value="0" onchange="this.form.onsubmit();" 
            {{if array_key_exists($_handler_name, $conf.object_handlers) &&
              $conf.object_handlers.$_handler_name == "0"}}checked="checked"
            {{/if}}/>
        </form>
      </td>
    </tr>  
  {{/foreach}}
</table>