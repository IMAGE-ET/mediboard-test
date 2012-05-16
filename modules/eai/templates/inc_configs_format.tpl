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

<table class="form">
  {{foreach from=$categories key=cat_name item=_fields}}
    {{if $cat_name}}
      <tr>
        <th colspan="4" class="section" style="text-align: center;">{{$cat_name}}</th>
      </tr>
    {{/if}}
    
    {{foreach from=$_fields item=_field_name}}
      <tr>
        <th>{{mb_title object=$format_config field=$_field_name}}</th>
        <td>
          <form name="editConfigsFormat-{{$_field_name}}" 
             action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this);">
            <input type="hidden" name="sender_id" value="{{$actor->_id}}" />
            <input type="hidden" name="sender_class" value="{{$actor->_class}}" />
            {{mb_key   object=$format_config}}
            {{mb_class object=$format_config}}
            <input type="hidden" name="callback" value="InteropActor.callbackConfigsFormats" />
            
            {{mb_field object=$format_config field=$_field_name onchange="this.form.onsubmit();"}}
          </form>
        </td>
      </tr>
    {{/foreach}}
  {{/foreach}}
</table>
