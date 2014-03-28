{{*
 * $Id$
 *  
 * @category SalleOp
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
  Main.add(function() {
    getForm("lock_sortie").user_password.focus();
  });
</script>

<form name="lock_sortie" method="post" action="?m=system&a=ajax_password_action"
      onsubmit="return onSubmitFormAjax(this, {useFormAction: true})">
  <input type="hidden" name="callback" value="callbackSortie" />
  <table class="form">
    <tr>
      <th>
        Anesthésiste
      </th>
      <td>
        <select name="user_id">
          {{mb_include module=mediusers template=inc_options_mediuser list=$anesths}}
        </select>
      </td>
    </tr>
    <tr>
      <th>
        <label for="user_password">Mot de passe</label>
      </th>
      <td>
        <input type="password" name="user_password" class="notNull password str" />
      </td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button type="button" class="tick singleclick" onclick="return this.form.onsubmit();">Valider</button>
      </td>
    </tr>
</form>