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
      <a onclick="zoomViewport(this);">{{$listSejours|@count}} hospitalisation(s) au {{$date|date_format:$conf.date}}</a>
    </th>
  </tr>
  <tr>
    <th class="narrow">{{mb_title class=CAffectation field=lit_id}}</th>
    <th>{{mb_title class=CGrossesse field=parturiente_id}}</th>
    <th class="narrow">{{mb_title class=CSejour field=entree}}</th>
    <th class="narrow">Acc.</th>
    <th class="narrow">Act. Mère</th>
    <th>Naissances</th>
    <th class="narrow">Act. Enf.</th>
  </tr>
  {{foreach from=$listSejours item=_sejour}}
    <tbody>
    {{assign var=nb_naissance value=$_sejour->_ref_grossesse->_ref_naissances|@count}}
    {{if  $nb_naissance == 0}}
      {{assign var=nb_naissance value=1}}
    {{/if}}

    {{foreach from=$_sejour->_ref_grossesse->_ref_naissances item=_naissance name=loop_naissance}}
      <tr>
        {{if $smarty.foreach.loop_naissance.first}}
          <td rowspan="{{$nb_naissance}}">
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_ref_curr_affectation->_guid}}');">
              {{mb_value object=$_sejour->_ref_curr_affectation field=lit_id}}
            </span>
          </td>
          <td rowspan="{{$nb_naissance}}" class="text">
            <span class="CPatient-view" onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_ref_grossesse->_ref_parturiente->_guid}}');">
              {{mb_value object=$_sejour->_ref_grossesse field=parturiente_id}}
            </span>
          </td>
          <td rowspan="{{$nb_naissance}}">
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">{{mb_value object=$_sejour field=entree}}</span>
          </td>
          <td rowspan="{{$nb_naissance}}" style="text-align: center; font-weight: bold">
              {{if $_sejour->_ref_grossesse->_days_relative_acc != ''}}
                J{{$_sejour->_ref_grossesse->_days_relative_acc}}
              {{/if}}
          </td>
          <td rowspan="{{$nb_naissance}}">
            <button type="button" class="edit notext" onclick="Tdb.editSejour('{{$_sejour->_id}}')">{{tr}}CSejour{{/tr}}</button>
            <button type="button" class="soins notext" onclick="Tdb.editD2S('{{$_sejour->_id}}')">{{tr}}dossier_soins{{/tr}}</button>
            {{if $_sejour->_ref_grossesse->_ref_last_operation}}
              <button onclick="Tdb.dossierAccouchement('{{$_sejour->_ref_grossesse->_ref_last_operation->_id}}');" type="button">acc</button>
            {{/if}}
            <button type="button" class="accouchement_create notext" onclick="Tdb.editAccouchement(null, '{{$_sejour->_id}}', '{{$_sejour->_ref_grossesse->_id}}', '')">Accouchement</button>
          </td>
        {{/if}}
        <td class="text">
          <span class="gender_{{$_naissance->_ref_sejour_enfant->_ref_patient->sexe}}" onmouseover="ObjectTooltip.createEx(this, '{{$_naissance->_ref_sejour_enfant->_guid}}');">
            {{$_naissance->_ref_sejour_enfant->_ref_patient}} {{if $_naissance->date_time}}<strong>(J{{$_naissance->_day_relative}})</strong>{{/if}}
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
        <td rowspan="{{$nb_naissance}}" class="text">
            <span class="CPatient-view" onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_ref_grossesse->_ref_parturiente->_guid}}');">
              {{mb_value object=$_sejour->_ref_grossesse field=parturiente_id}}
            </span>
        </td>
        <td rowspan="{{$nb_naissance}}">
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">{{mb_value object=$_sejour field=entree}}</span>
        </td>
        <td rowspan="{{$nb_naissance}}" style="text-align: center; font-weight: bold">
          {{if $_sejour->_ref_grossesse->_days_relative_acc != ''}}
            J{{$_sejour->_ref_grossesse->_days_relative_acc}}
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
    </tbody>
  {{foreachelse}}
    <tr>
      <td colspan="7" class="empty">{{tr}}CSejour.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>