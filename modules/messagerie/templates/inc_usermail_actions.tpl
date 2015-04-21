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

<button type="button" title="{{tr}}Delete{{/tr}}" onclick="messagerie.action('delete', '{{$mail->_id}}');">
  <i class="msgicon fa fa-trash"></i>
  {{tr}}Delete{{/tr}}
</button>

{{if !$mail->archived}}
  <button type="button" title="{{tr}}CUserMail-title-archive{{/tr}}" onclick="messagerie.action('archive', '{{$mail->_id}}');">
    <i class="msgicon fa fa-archive"></i>
    {{tr}}CUserMail-title-archive{{/tr}}
  </button>
{{else}}
  <button type="button" title="{{tr}}CUserMail-title-unarchive{{/tr}}" onclick="messagerie.action('unarchive', '{{$mail->_id}}');">
    <i class="msgicon fa fa-inbox"></i>
    {{tr}}CUserMail-title-unarchive{{/tr}}
  </button>
{{/if}}

<button type="button" title="{{tr}}CUserMail-title-answer{{/tr}}" onclick="messagerie.edit(null, '{{$mail->_id}}');">
  <i class="msgicon fa fa-reply"></i>
  {{tr}}CUserMail-title-answer{{/tr}}
</button>

<button type="button" title="{{tr}}CUserMail-title-answer_to_all{{/tr}}" onclick="messagerie.edit(null, '{{$mail->_id}}', 1);">
  <i class="msgicon fa fa-reply-all"></i>
  {{tr}}CUserMail-title-answer_to_all{{/tr}}
</button>

{{if $mail->date_read}}
  <button type="button" title="{{tr}}CUserMail-title-unread{{/tr}}" onclick="messagerie.action('mark_unread', '{{$mail->_id}}');">
    <i class="msgicon fa fa-eye-slash"></i>
    {{tr}}CUserMail-title-unread{{/tr}}
  </button>
{{else}}
  <button type="button" title="{{tr}}CUserMail-title-read{{/tr}}" onclick="messagerie.action('mark_read', '{{$mail->_id}}');">
    <i class="msgicon fa fa-eye"></i>
    {{tr}}CUserMail-title-read{{/tr}}
  </button>
{{/if}}

{{if !$mail->favorite}}
  <button type="button" title="{{tr}}CUserMail-title-favour{{/tr}}" onclick="messagerie.action('favour', '{{$mail->_id}}');">
    <i class="msgicon fa fa-star"></i>
    {{tr}}CUserMail-title-favour{{/tr}}
  </button>
{{else}}
  <button type="button" title="{{tr}}CUserMail-title-unfavour{{/tr}}" onclick="messagerie.action('unfavour', '{{$mail->_id}}');">
    <i class="msgicon fa fa-star-o"></i>
    {{tr}}CUserMail-title-unfavour{{/tr}}
  </button>
{{/if}}

{{if $app->user_prefs.LinkAttachment}}
  <button type="button" onclick="messagerie.linkAttachment('{{$mail->_id}}');">
    <i class="msgicon fa fa-link"></i>
    {{tr}}CMailAttachments-button-append{{/tr}}
  </button>
{{/if}}
