{{*
 * $Id$
 *
 * @category test
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
Main.add(function(){
  var form = getForm("admit-patient");
  Calendar.regField(form.admission_datetime);
})
</script>

<form name="admit-patient">
  <table class="tbl">
    <tr>
      <th>Photo</th>
      <th>Identité</th>
      <th>ID</th>
      <th>Codable</th>
      <th></th>
    </tr>
    <tr>
      <td>{{mb_include module=patients template=inc_vw_photo_identite}}</td>
      <td>{{$patient->_view}}</td>
      <td>{{$patient->_id}}</td>
      <td>
        {{foreach from=$patient->_ref_sejours item=_sejour}}
          <label>
            <input type="radio" name="sejour_id" value="{{$_sejour->_id}}"/>
            {{$_sejour->_view}}
          </label>
          <br />
        {{foreachelse}}
          <div style="empty">{{tr}}CSejour.none{{/tr}}</div>
        {{/foreach}}
      </td>
      <td>
        <table class="main form">
          <tr>
            <th><label for="numero_visite">Numéro visite</label></th>
            <td><input name="numero_visite" type="text" value=""/></td>
          </tr>
          <tr>
            <th><label for="admission_datetime">Entrée</label></th>
            <td><input type="hidden" class="datetime" value="" name="admission_datetime"/></td>
          </tr>
          <tr>
            <th><label for="point_of_care">Point of Care</label></th>
            <td></td>
          </tr>
          <tr>
            <th><label for="bed">Lit</label></th>
            <td>
              <select name="bed">
                {{foreach from=$lits item=_lit}}
                  <option value="{{$_lit->_id}}">{{$_lit}}</option>
                {{/foreach}}
              </select>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <button type="submit" class="tick">Lancer</button>
</form>