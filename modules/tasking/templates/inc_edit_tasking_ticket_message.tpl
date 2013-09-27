{{*
 * $Id$
 *  
 * @category Tasking
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<form name="edit-message" action="" method="post" onsubmit="return Tasking.Message.submitMessage(this);">
  {{mb_key   object=$message}}
  {{mb_class object=$message}}
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="task_id" value="{{$task_id}}" />
  {{mb_field object=$message field=creation_date hidden=true}}
  {{mb_field object=$message field=user_id       hidden=true}}

  <table class="main form">
    <tr>
      <th class="narrow">{{mb_label object=$message field=title}}</th>
      <td>{{mb_field object=$message field=title style="width:100%; box-sizing: border-box;"}}</td>
    </tr>
    <tr>
      <th class="narrow">{{mb_label object=$message field=text}}</th>
      <td>{{mb_field object=$message field=text rows=6}}</td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        {{if !$message->_id}}
          <button type="submit" class="save singleclick">
            {{tr}}Save{{/tr}}
          </button>
        {{else}}
          <button type="submit" class="edit singleclick">
            {{tr}}Edit{{/tr}}
          </button>

          <button type="button" class="trash singleclick" onclick="confirmDeletion(this.form, {typeName: 'ce message', ajax:true, callback: Tasking.Message.closeAndList});">
            {{tr}}Delete{{/tr}}
          </button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>

