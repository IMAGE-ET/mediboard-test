{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage ccam
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

{{mb_default var=error value=0}}

{{if !$error}}
  <form name="lock_codage" method="post" action="?m=ccam&a=ajax_lock_codage"
        onsubmit="return onSubmitFormAjax(this, {useFormAction: true, onComplete: Control.Modal.close.curry()})">
    <input type="hidden" name="praticien_id" value="{{$praticien_id}}"/>
    <input type="hidden" name="codable_id" value="{{$codable_id}}"/>
    <input type="hidden" name="codable_class" value="{{$codable_class}}"/>
    <input type="hidden" name="lock" value="{{$lock}}"/>
    <input type="hidden" name="date" value="{{$date}}"/>

    <table class="form">
      {{if $conf.dPccam.CCodable.lock_codage_ccam == 'password'}}
        <script>
          Main.add(function() {
            getForm("lock_codage").user_password.focus();
          });
        </script>
        <tr>
          <td colspan="2">
            <div class="small-info">
              {{tr}}CCodageCCAM-msg-lock{{/tr}}
            </div>
          </td>
        </tr>
        <tr>
          <th>
            <label for="user_password">{{tr}}Password{{/tr}}</label>
          </th>
          <td>
            <input type="password" name="user_password" class="notNull password str" />
          </td>
        </tr>
      {{/if}}
      {{if $codable_class == 'CSejour'}}
        {{mb_ternary var=msg test=$lock value='CCodageCCAM-msg-lock_all_codages' other='CCodageCCAM-msg-unlock_all_codages'}}
        <tr>
          <th>
            <label for="lock_all_codage">{{tr}}{{$msg}}{{/tr}}</label>
          </th>
          <td>
            <input type="checkbox" name="lock_all_codages" />
          </td>
        </tr>
      {{/if}}
      <tr>
        <td class="button" colspan="2">
          <button type="button" class="tick" onclick="return this.form.onsubmit();">{{tr}}Validate{{/tr}}</button>
          <button type="button" class="cancel" onclick="Control.Modal.close();">{{tr}}Cancel{{/tr}}</button>
        </td>
      </tr>
    </table>
  </form>
{{else}}
  <div class="small-error">
    {{tr}}CCodageCCAM-error-not_owner{{/tr}}
  </div>
{{/if}}