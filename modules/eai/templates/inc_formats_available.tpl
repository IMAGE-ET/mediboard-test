{{*
 * Formats available
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

<fieldset>
  <legend>{{tr}}CExchangeTabular{{/tr}}</legend>
  
  {{foreach from=$formats_tabular item=_format_tabular}}
    <button class="none" onclick="InteropActor.viewMessagesSupported('{{$actor_guid}}', '{{$_format_tabular}}')">
      {{tr}}{{$_format_tabular}}{{/tr}}
    </button>
  {{/foreach}}
</fieldset>

<fieldset> 
  <legend>{{tr}}CEchangeXML{{/tr}}</legend>
  
  {{foreach from=$formats_xml item=_format_xml}}
    <button class="none" onclick="InteropActor.viewMessagesSupported('{{$actor_guid}}', '{{$_format_xml}}')">
      {{tr}}{{$_format_xml}}{{/tr}}
    </button>
  {{/foreach}}
</fieldset>