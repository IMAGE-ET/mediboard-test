{{*
 * Configs format
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

{{assign var=fields value=$format_config->_config_fields}}

<table class="form">
  {{foreach from=$fields item=_field_name}}
  <tr>
    <th class="category">{{mb_title object=$format_config field=$_field_name}}</th>
    <td>
      <form name="editConfigsFormat-{{$_field_name}}" 
         action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this);">
        <input type="hidden" name="sender_id" value="{{$actor->_id}}" />
        <input type="hidden" name="sender_class" value="{{$actor->_class_name}}" />
        {{mb_key   object=$format_config}}
        {{mb_class object=$format_config}}
        <input type="hidden" name="callback" value="InteropActor.refreshConfigsFormats" />
        
        {{mb_field object=$format_config field=$_field_name onchange="this.form.onsubmit();"}}
      </form>
    </td>
  </tr>
  {{/foreach}}
</table>
