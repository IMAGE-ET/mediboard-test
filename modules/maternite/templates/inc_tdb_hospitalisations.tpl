{{*
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Maternite
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<table class="tbl" id="hospitalisation_tab">
  <tr>
    <th class="title" colspan="10">{{$listSejours|@count}} hospitalisation(s) au {{$date|date_format:$conf.date}}</th>
  </tr>
  <tr>
    <th class="narrow">{{mb_title class=CAffectation field=lit_id}}</th>
    <th>{{mb_title class=CGrossesse field=parturiente_id}}</th>
    <th class="narrow">{{mb_title class=CSejour field=entree}}</th>
    <th>Accouchement</th>
    <th>Naissances</th>
    <th class="narrow">{{tr}}Action{{/tr}}</th>
  </tr>
  {{foreach from=$listSejours item=_sejour}}
    <tr>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_ref_curr_affectation->_guid}}');">
          {{mb_value object=$_sejour->_ref_curr_affectation field=lit_id}}
        </span>
      </td>
      <td>
        <span class="CPatient-view" onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_ref_grossesse->_ref_parturiente->_guid}}');">{{mb_value object=$_sejour->_ref_grossesse field=parturiente_id}}</span>
      </td>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">{{mb_value object=$_sejour field=entree}}</span>
      </td>
      <td>
        {{if $_sejour->_ref_grossesse->datetime_debut_travail}}
          Démarré à {{mb_value object=$_sejour->_ref_grossesse field=datetime_debut_travail}}
        {{/if}}

        {{if $_sejour->_ref_grossesse->datetime_accouchement}}
          Terminé à {{mb_value object=$_sejour->_ref_grossesse field=datetime_accouchement}}
        {{/if}}
      </td>
      <td>
        <ul>
        {{foreach from=$_sejour->_ref_grossesse->_ref_naissances item=_naissance}}
          <li class="gender_{{$_naissance->_ref_sejour_enfant->_ref_patient->sexe}}">
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_naissance->_ref_sejour_enfant->_guid}}');">{{$_naissance->_ref_sejour_enfant->_ref_patient}} {{if $_naissance->heure}}({{$_naissance->heure|date_format:$conf.time}}){{/if}}</span>
          </li>
        {{/foreach}}
        </ul>
      </td>
      <td>
        <button type="button" class="edit notext" onclick="Tdb.editSejour('{{$_sejour->_id}}')">{{tr}}CSejour{{/tr}}</button>
        <button type="button" class="soins notext" onclick="Tdb.editD2S('{{$_sejour->_id}}')">{{tr}}dossier_soins{{/tr}}</button>
        <button type="button" class="new notext" onclick="Tdb.editAccouchement(null, '{{$_sejour->_id}}', '{{$_sejour->_ref_grossesse->_id}}', '')">Accouchement</button>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="6" class="empty">{{tr}}CSejour.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>