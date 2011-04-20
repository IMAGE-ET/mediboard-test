{{*
 * Configure objects servers EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

<table class="tbl">
  <tr>
    <th class="category">Serveurs d'objets</th>
    <th class="category">Gestionnnaire</th>
  </tr>
    
  {{foreach from=$object_servers item=_object_server}}  
  <tr>
    <td>{{tr}}config-object_server-{{$_object_server}}{{/tr}}</td>
    <td>
      <form name="editConfig_object_server-{{$_object_server}}" action="?m={{$m}}&amp;{{$actionType}}=configure" 
        method="post" onsubmit="return onSubmitFormAjax(this);">
        <input type="hidden" name="m" value="system" />
        <input type="hidden" name="dosql" value="do_configure" />
        
        <label for="object_handlers_{{$_object_server}}_1">{{tr}}bool.1{{/tr}}</label>
        <input type="radio" name="object_handlers[{{$_object_server}}]" value="1" onchange="this.form.onsubmit();" 
          {{if array_key_exists($_object_server, $conf.object_handlers) &&
            $conf.object_handlers.$_object_server == "1"}}checked="checked"
          {{/if}}/>
        <label for="object_handlers_{{$_object_server}}_0">{{tr}}bool.0{{/tr}}</label>
        <input type="radio" name="object_handlers[{{$_object_server}}]" value="0" onchange="this.form.onsubmit();" 
          {{if array_key_exists($_object_server, $conf.object_handlers) &&
            $conf.object_handlers.$_object_server == "0"}}checked="checked"
          {{/if}}/>
      </form>
    </td>
  </tr>
  {{/foreach}}
</table>