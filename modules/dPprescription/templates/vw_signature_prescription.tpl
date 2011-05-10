{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


<script type="text/javascript">

Main.add(function(){
  document.signaturePrescriptionPopup.password.focus();
});

</script>

<form name="signaturePrescriptionPopup" method="post" action="" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_valide_all_lines_aed" />
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="prescription_id" value="{{$prescription_id}}" />
  <input type="hidden" name="chapitre" value="all" />
  <input type="hidden" name="annulation" value="{{$annulation}}" />
  <input type="hidden" name="del" value="0" />
  
  <table class="main form">
    <tr>
      <th class="category" colspan="2">Signature de toutes les lignes</th>
    </tr>
    <tr>
      <th>
        <label for="praticien_id">{{tr}}CUser-user_username{{/tr}}</label>
      </th>
      <td>
        <select name="praticien_id">
          <option value="">&mdash; Choix d'un praticien</option>
          {{foreach from=$praticiens item=_praticien}}
          <option {{if $_praticien->_id == $praticien_id}}selected="selected"{{/if}} value="{{$_praticien->_id}}" class="mediuser" 
                  style="border-color: #{{$_praticien->_ref_function->color}};" >{{$_praticien->_view}}</option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    <tr>
      <th>
        <label for="password">{{tr}}CUser-user_password{{/tr}}</label>
      </th>
      <td>
        <input type="password"  class="notNull str" size="10" maxlength="32" name="password" />
      </td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        {{if $annulation}}
          <button type="submit" class="cancel">Annuler les signatures</button>
        {{else}}
          <button type="submit" class="submit">Signer toutes les lignes</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>
