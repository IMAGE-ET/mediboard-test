{{*
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Maternite
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<script>
  Main.add(function() {
    Tdb.views.filterByText('hospitalisation_tab');
  });
</script>

<table class="tbl" id="hospitalisation_tab">
  <tr>
    <th class="title" colspan="7">
      <button type="button" class="change notext" onclick="Tdb.views.listHospitalisations(false);" style="float: right;">
        {{tr}}Refresh{{/tr}}
      </button>
      <button class="sejour_create notext" onclick="Tdb.editSejour(null);" style="float: left;">
        {{tr}}CSejour-title-create{{/tr}}
      </button>
      {{$listSejours|@count}} hospitalisation(s) au {{$date|date_format:$conf.date}}</th>
  </tr>
  <tr>
    <th class="narrow">{{mb_title class=CAffectation field=lit_id}}</th>
    <th>{{mb_title class=CGrossesse field=parturiente_id}}</th>
    <th class="narrow">{{mb_title class=CSejour field=entree}}</th>
    <th>Acc.</th>
    <th class="narrow">Act. M�re</th>
    <th class="narrow">Naissances</th>
    <th class="narrow">Act. Enf.</th>
  </tr>
  {{foreach from=$listSejours item=_sejour}}
    {{assign var=nb_naissance value=$_sejour->_ref_grossesse->_ref_naissances|@count}}
    {{foreach from=$_sejour->_ref_grossesse->_ref_naissances item=_naissance name=loop_naissance}}
      <tr>
        {{if $smarty.foreach.loop_naissance.first}}
          <td rowspan="{{$nb_naissance}}">
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_ref_curr_affectation->_guid}}');">
              {{mb_value object=$_sejour->_ref_curr_affectation field=lit_id}}
            </span>
          </td>
          <td rowspan="{{$nb_naissance}}">
            <span class="CPatient-view" onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_ref_grossesse->_ref_parturiente->_guid}}');">
              {{mb_value object=$_sejour->_ref_grossesse field=parturiente_id}}
            </span>
          </td>
          <td rowspan="{{$nb_naissance}}">
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">{{mb_value object=$_sejour field=entree}}</span>
          </td>
          <td rowspan="{{$nb_naissance}}">
            {{if $_sejour->_ref_grossesse->datetime_debut_travail}}
              D�marr� � {{mb_value object=$_sejour->_ref_grossesse field=datetime_debut_travail}}
            {{/if}}

            {{if $_sejour->_ref_grossesse->datetime_accouchement}}
              Termin� � {{mb_value object=$_sejour->_ref_grossesse field=datetime_accouchement}}
            {{/if}}
          </td>
          <td rowspan="{{$nb_naissance}}">
            <button type="button" class="edit notext" onclick="Tdb.editSejour('{{$_sejour->_id}}')">{{tr}}CSejour{{/tr}}</button>
            <button type="button" class="soins notext" onclick="Tdb.editD2S('{{$_sejour->_id}}')">{{tr}}dossier_soins{{/tr}}</button>
            <button type="button" class="accouchement_create notext" onclick="Tdb.editAccouchement(null, '{{$_sejour->_id}}', '{{$_sejour->_ref_grossesse->_id}}', '')">Accouchement</button>
          </td>
        {{/if}}
        <td>
          <span class="gender_{{$_naissance->_ref_sejour_enfant->_ref_patient->sexe}}" onmouseover="ObjectTooltip.createEx(this, '{{$_naissance->_ref_sejour_enfant->_guid}}');">
            {{$_naissance->_ref_sejour_enfant->_ref_patient}} {{if $_naissance->heure}}<strong>(J{{$_naissance->_day_relative}})</strong>{{/if}}
          </span>
        </td>
        <td>
          <button class="soins notext" onclick="Tdb.editD2S('{{$_naissance->_ref_sejour_enfant->_id}}');">{{tr}}dossier_soins{{/tr}}</button>
        </td>
      </tr>
    {{foreachelse}}
      <tr>
        <td rowspan="{{$nb_naissance}}">
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_ref_curr_affectation->_guid}}');">
              {{mb_value object=$_sejour->_ref_curr_affectation field=lit_id}}
            </span>
        </td>
        <td rowspan="{{$nb_naissance}}">
            <span class="CPatient-view" onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_ref_grossesse->_ref_parturiente->_guid}}');">
              {{mb_value object=$_sejour->_ref_grossesse field=parturiente_id}}
            </span>
        </td>
        <td rowspan="{{$nb_naissance}}">
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">{{mb_value object=$_sejour field=entree}}</span>
        </td>
        <td rowspan="{{$nb_naissance}}">
          {{if $_sejour->_ref_grossesse->datetime_debut_travail}}
            D�marr� � {{mb_value object=$_sejour->_ref_grossesse field=datetime_debut_travail}}
          {{/if}}

          {{if $_sejour->_ref_grossesse->datetime_accouchement}}
            Termin� � {{mb_value object=$_sejour->_ref_grossesse field=datetime_accouchement}}
          {{/if}}
        </td>
        <td rowspan="{{$nb_naissance}}">
          <button type="button" class="edit notext" onclick="Tdb.editSejour('{{$_sejour->_id}}')">{{tr}}CSejour{{/tr}}</button>
          <button type="button" class="soins notext" onclick="Tdb.editD2S('{{$_sejour->_id}}')">{{tr}}dossier_soins{{/tr}}</button>
          <button type="button" class="accouchement_create notext" onclick="Tdb.editAccouchement(null, '{{$_sejour->_id}}', '{{$_sejour->_ref_grossesse->_id}}', '')">Accouchement</button>
        </td>
        <td colspan="2"></td>
      </tr>
    {{/foreach}}
  {{foreachelse}}
    <tr>
      <td colspan="7" class="empty">{{tr}}CSejour.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>