{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage messagerie
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

<input type="checkbox" value="" onclick="UserMessage.toggleSelect(this);"/>

<button type="button" title="{{tr}}CUserMessage-title-create{{/tr}}" onclick="UserMessage.edit('', '', '{{$inputMode}}', UserMessage.refreshListCallback);">
  <i class="msgicon fa fa-envelope"></i>
  {{tr}}CUserMessage-title-create{{/tr}}
</button>

{{if $mode == 'inbox'}}
  <button type="button" title="{{tr}}CUserMessageDest-title-to_archive-0{{/tr}}" onclick="UserMessage.editAction('archive', '1');">
    <i class="msgicon fa fa-archive"></i>
    {{tr}}CUserMessageDest-title-to_archive-0{{/tr}}
  </button>
{{/if}}

{{if $mode == 'archive'}}
  <button type="button" title="{{tr}}CUserMessageDest-title-to_archive-1{{/tr}}" onclick="UserMessage.editAction('archive', '0');">
    <i class="msgicon fa fa-inbox"></i>
    {{tr}}CUserMessageDest-title-to_archive-1{{/tr}}
  </button>
{{/if}}

{{if $mode != 'sentbox'}}
  <button type="button" title="{{tr}}Delete{{/tr}}" onclick="UserMessage.editAction('delete');">
    <i class="msgicon fa fa-trash"></i>
    {{tr}}Delete{{/tr}}
  </button>
{{/if}}

{{if $mode == 'inbox'}}
  <button type="button" title="{{tr}}CUserMessage-title-read{{/tr}}" onclick="UserMessage.editAction('mark_read');">
    <i class="msgicon fa fa-eye"></i>
    {{tr}}CUserMessageDest-title-read{{/tr}}
  </button>

  <button type="button" title="{{tr}}CUserMessage-title-unread{{/tr}}" onclick="UserMessage.editAction('mark_unread');">
    <i class="msgicon fa fa-eye-slash"></i>
    {{tr}}CUserMessageDest-title-unread{{/tr}}
  </button>

  <button type="button" title="{{tr}}CUserMessageDest-title-to_star-0{{/tr}}" onclick="UserMessage.editAction('star', '1');">
    <i class="msgicon fa fa-star"></i>
    {{tr}}CUserMessageDest-title-to_star-0{{/tr}}
  </button>

  <button type="button" title="{{tr}}CUserMessageDest-title-to_star-0{{/tr}}" onclick="UserMessage.editAction('star', '0');">
    <i class="msgicon fa fa-star-o"></i>
    {{tr}}CUserMessageDest-title-to_star-1{{/tr}}
  </button>
{{/if}}