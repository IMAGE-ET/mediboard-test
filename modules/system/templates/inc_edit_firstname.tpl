{{*
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<form name="edit_firstname" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: Control.Modal.close})">
  <input type="hidden" name="m" value="system"/>
  {{mb_class object=$object}}
  {{mb_key object=$object}}

  <table class="form">
    <tr>
      <th>{{mb_label object=$object field=firstname}}</th>
      <td>{{mb_field object=$object field=firstname}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$object field=sex}}</th>
      <td>{{mb_field object=$object field=sex}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$object field=language}}</th>
      <td>{{mb_field object=$object field=language}}</td>
    </tr>

    <tr>
      <td colspan="2" class="button">
        <button type="button" class="save" onclick="this.form.onsubmit();">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>