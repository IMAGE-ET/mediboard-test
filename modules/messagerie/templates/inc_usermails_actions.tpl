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

<button type="button" title="{{tr}}CUserMessage-title-create{{/tr}}" onclick="messagerie.edit();">
  <i class="msgicon fa fa-envelope"></i>
  {{tr}}New{{/tr}}
</button>

<button type="button" title="{{tr}}Delete{{/tr}}" onclick="messagerie.action('delete');">
  <i class="msgicon fa fa-trash"></i>
  {{tr}}Delete{{/tr}}
</button>

{{if $mode == 'inbox'}}
  <button type="button" title="{{tr}}CUserMail-title-archive{{/tr}}" onclick="messagerie.action('archive');">
    <i class="msgicon fa fa-archive"></i>
    {{tr}}CUserMail-title-archive{{/tr}}
  </button>
{{/if}}

{{if $mode == 'archived'}}
  <button type="button" title="{{tr}}CUserMessageDest-title-to_archive-1{{/tr}}" onclick="messagerie.action('unarchive');">
    <i class="msgicon fa fa-inbox"></i>
    {{tr}}CUserMail-title-unarchive{{/tr}}
  </button>
{{/if}}

{{if $mode == 'inbox'}}
  <button type="button" title="{{tr}}CUserMail-title-read{{/tr}}" onclick="messagerie.action('mark_read');">
    <i class="msgicon fa fa-eye"></i>
    {{tr}}CUserMail-title-read{{/tr}}
  </button>

  <button type="button" title="{{tr}}CUserMail-title-unread{{/tr}}" onclick="messagerie.action('mark_unread');">
    <i class="msgicon fa fa-eye-slash"></i>
    {{tr}}CUserMail-title-unread{{/tr}}
  </button>

  <button type="button" title="{{tr}}CUserMail-title-favour{{/tr}}" onclick="messagerie.action('favour');">
    <i class="msgicon fa fa-star"></i>
    {{tr}}CUserMail-title-favour{{/tr}}
  </button>
{{/if}}

{{if $mode == 'favorites'}}
  <button type="button" title="{{tr}}CUserMail-title-unfavour{{/tr}}" onclick="messagerie.action('unfavour');">
    <i class="msgicon fa fa-star-o"></i>
    {{tr}}CUserMail-title-unfavour{{/tr}}
  </button>
{{/if}}