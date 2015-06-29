{{*
 * $Id$
 *
 * @category soins
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}


<script>
  viewDossierSoin = function(sejour_id) {
    var oForm = document.viewSoin;
    oForm.sejour_id.value = sejour_id;
    oForm.submit();
  };

  showDossierSoins = function(sejour_id) {
    PlanSoins.save_nb_decalage = PlanSoins.nb_decalage;
    var url = new Url("soins", "ajax_vw_dossier_sejour");
    url.addParam("sejour_id", sejour_id);
    url.addParam("modal", 1);
    url.requestModal("95%", "90%", {
      showClose: false
    });
    modalWindow = url.modalObject;
  };

  refreshLinePancarte = function(prescription_id) {
    PlanSoins.init({
      composition_dossier: {{$composition_dossier|@json}},
      date: "{{$date}}",
      manual_planif: "{{$manual_planif}}",
      bornes_composition_dossier:  {{$bornes_composition_dossier|@json}},
      nb_postes: {{$bornes_composition_dossier|@count}},
      nb_decalage: PlanSoins.save_nb_decalage ? PlanSoins.save_nb_decalage : {{$nb_decalage}},
      plan_soin_id: 'plan_soin_pancarte'
    });
    var url = new Url("soins", "ajax_vw_pancarte");
    url.addParam("prescription_id", prescription_id);
    url.requestUpdate("pancarte_line_"+prescription_id, {onComplete: PlanSoins.moveDossierSoin.curry($('plan_soin_pancarte'))});
  };

  loadSuivi = function(sejour_id, user_id, cible, show_obs, show_trans, show_const, show_header) {
    if(!sejour_id) return;
    updateNbTrans(sejour_id);
    var urlSuivi = new Url("dPhospi", "httpreq_vw_dossier_suivi");
    urlSuivi.addParam("sejour_id", sejour_id);
    urlSuivi.addParam("user_id", user_id);
    urlSuivi.addParam("cible", cible);
    if (!Object.isUndefined(show_obs) && show_obs != null) {
      urlSuivi.addParam("_show_obs", show_obs);
    }
    if (!Object.isUndefined(show_trans) && show_trans != null) {
      urlSuivi.addParam("_show_trans", show_trans);
    }
    if (!Object.isUndefined(show_const) && show_const != null) {
      urlSuivi.addParam("_show_const", show_const);
    }
    if (!Object.isUndefined(show_header)) {
      urlSuivi.addParam("show_header", show_header);
    }
    urlSuivi.requestUpdate("dossier_suivi");
  };

  Main.add(function() {
    {{if "dPprescription"|module_active}}
    PlanSoins.init({
      composition_dossier: {{$composition_dossier|@json}},
      date: "{{$date}}",
      manual_planif: "{{$manual_planif}}",
      bornes_composition_dossier:  {{$bornes_composition_dossier|@json}},
      nb_postes: {{$bornes_composition_dossier|@count}},
      nb_decalage: {{$nb_decalage}},
      plan_soin_id: 'plan_soin_pancarte'
    });

    PlanSoins.moveDossierSoin($('plan_soin_pancarte'));
    {{/if}}
  });
</script>

<table class="form">
  <tr>
    <th class="title">
      <button class="hslip notext" type="button" style="float:left" onclick="$('categories').toggle();"></button>
      <button class="change" style="float: left" onclick="viewPancarte();">{{tr}}Search{{/tr}}</button>
      Pancarte du service {{$service->_view}}
    </th>
  </tr>
</table>

<table id="plan_soin_pancarte" class="tbl">
  <tr>
    <th rowspan="2" class="title" style="width: 20%">Patient</th>
    <th rowspan="2" class="title" style="width: 10%">Lit</th>
    <th rowspan="2" class="title" style="width: 10%">Prat.</th>
    {{foreach from=$count_composition_dossier key=_date item=_hours_by_moment}}
      {{foreach from=$_hours_by_moment key=moment_journee item=_count}}

        {{if $composition_dossier|@count == 1}}
          {{assign var=view_poste value="Journée"}}
        {{else}}
          {{assign var=tab_poste value='-'|explode:$moment_journee}}
          {{assign var=num_poste value=$tab_poste|@end}}
          {{assign var=libelle_poste value="Libelle poste $num_poste"}}
          {{assign var=view_poste value=$configs.$libelle_poste}}
        {{/if}}

        <th class="{{$_date}}-{{$moment_journee}} title" colspan="{{$_count}}" style="width: 60%">
          {{if $composition_dossier|@count > 1}}
            <a href="#1" onclick="PlanSoins.showBefore()" class="prevPeriod" style="float: left">
              <img src="images/icons/prev.png" alt="&lt;"/>
            </a>
            <a href="#1" onclick="PlanSoins.showAfter()" class="nextPeriod" style="float: right">
              <img src="images/icons/next.png" alt="&gt;" />
            </a>
          {{/if}}
          <strong>
            {{assign var=key_borne value="$_date-$moment_journee"}}
            {{assign var=bornes_poste value=$bornes_composition_dossier.$key_borne}}
            {{$view_poste}} du
            {{if $bornes_poste.min|iso_date != $bornes_poste.max|iso_date}}
              {{$bornes_poste.min|date_format:"%d/%m"}} au {{$bornes_poste.max|date_format:"%d/%m"}}
            {{else}}
              {{$_date|date_format:"%d/%m"}}
            {{/if}}
          </strong>
        </th>
      {{/foreach}}
    {{/foreach}}
  </tr>
  <tr>
    {{foreach from=$tabHours key=_date item=_hours_by_moment}}
      {{foreach from=$_hours_by_moment key=moment_journee item=_dates}}
        {{foreach from=$_dates key=_date_reelle item=_hours}}
          {{foreach from=$_hours key=_heure_reelle item=_hour}}
            <th class="{{$_date}}-{{$moment_journee}}" style="font-size: 0.8em;">{{$_hour}}h</th>
          {{/foreach}}
        {{/foreach}}
      {{/foreach}}
    {{/foreach}}
  </tr>
  {{foreach from=$prescriptions item=_prescription}}
    {{assign var=_prescription_id value=$_prescription->_id}}
    <tr id="pancarte_line_{{$_prescription_id}}">
      {{mb_include module=soins template=inc_vw_line_pancarte_service}}
    </tr>
    {{foreachelse}}
    <tr>
      <td colspan="30" class="empty">
        {{tr}}CPrisePosologie.none{{/tr}}
      </td>
    </tr>
  {{/foreach}}
</table>