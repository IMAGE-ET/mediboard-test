{{*
 * $Id$
 *  
 * @category hl7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{unique_id var=uid}}

<form name="set-session-receiver{{$uid}}" method="post" onsubmit="return onSubmitFormAjax(this);">
  <input type="hidden" name="dosql" value="do_set_session_receiver_aed" />
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="del" value="0" />

  <table class="main tbl">
    <tr>
      <td colspan="11">
        <label> {{tr}}CReceiverHL7v2{{/tr}} :
          <select name="cn_receiver_guid" onchange="this.form.onsubmit()">
            <option value="none">{{tr}}Choose{{/tr}}</option>
            {{foreach from=$receivers item=_receiver}}
              <option value="{{$_receiver->_guid}}" {{if $_receiver->_guid == $cn_receiver_guid}}selected{{/if}}>{{$_receiver->_view}}</option>
              {{foreachelse}}
              <option value="none" disabled>{{tr}}CReceiverHL7v2.none{{/tr}}</option>
            {{/foreach}}
          </select></label>
      </td>
    </tr>
  </table>
</form>