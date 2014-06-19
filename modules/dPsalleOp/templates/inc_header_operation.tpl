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

<table class="tbl">
  <tr>
    <th class="title text" colspan="2">
      <button class="hslip notext" id="listplages-trigger" type="button" style="float:left">
        {{tr}}Programme{{/tr}}
      </button>
      <a style="float: left" href="?m=patients&tab=vw_full_patients&patient_id={{$patient->_id}}">
        {{mb_include module=patients template=inc_vw_photo_identite patient=$patient size=42}}
      </a>
      <a class="action" style="float: right;" title="Modifier le dossier administratif" href="?m=dPpatients&tab=vw_edit_patients&patient_id={{$patient->_id}}">
        <img src="images/icons/edit.png" />
      </a>

      <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">{{$patient->_view}}</span>
      ({{$patient->_age}}
      {{if $patient->_annees != "??"}}- {{mb_value object=$patient field="naissance"}}{{/if}})
      &mdash; Dr {{$selOp->_ref_chir->_view}}
      {{if $sejour->_ref_curr_affectation->_id}}- {{$sejour->_ref_curr_affectation->_ref_lit->_ref_chambre->_view}}{{/if}}
      <br />

      {{mb_include module=planningOp template=inc_reload_infos_interv operation=$selOp}}

      &mdash; {{mb_label object=$selOp field=temp_operation}} : {{mb_value object=$selOp field=temp_operation}}
      <br />

      {{tr}}CSejour{{/tr}}
      du {{mb_value object=$sejour field=entree}}
      au
      {{if $sejour->canEdit() || $currUser->_is_praticien}}
        {{assign var=sejour_guid value=$sejour->_guid}}
        <form name="editSortiePrevue-{{$sejour_guid}}" method="post" action="?"
              style="font-size: 0.9em;" onsubmit="return onSubmitFormAjax(this)">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_sejour_aed" />
          <input type="hidden" name="del" value="0" />
          {{mb_key object=$sejour}}
          {{mb_field object=$sejour field=entree_prevue hidden=true}}
          {{mb_field object=$sejour field=sortie_prevue register=true form="editSortiePrevue-$sejour_guid" onchange="this.form.onsubmit()"}}
        </form>
      {{else}}
        {{mb_value object=$sejour field=sortie_prevue}}
      {{/if}}
    </th>
  </tr>

  {{if $conf.dPplanningOp.COperation.verif_cote && $selOp->cote_bloc && ($selOp->cote == "droit" || $selOp->cote == "gauche")}}
    <!-- Vérification du côté -->
    <tr>
      <td colspan="2">
        <strong>Côté DHE : {{mb_value object=$selOp field="cote"}}</strong> -
        <span class="{{if !$selOp->cote_admission}}warning{{elseif $selOp->cote_admission != $selOp->cote}}error{{else}}ok{{/if}}">
          Admission : {{mb_value object=$selOp field="cote_admission"}}
        </span> -
        <span class="{{if !$selOp->cote_consult_anesth}}warning{{elseif $selOp->cote_consult_anesth != $selOp->cote}}error{{else}}ok{{/if}}">
          Consult Anesth : {{mb_value object=$selOp field="cote_consult_anesth"}}
        </span> -
        <span class="{{if !$selOp->cote_hospi}}warning{{elseif $selOp->cote_hospi != $selOp->cote}}error{{else}}ok{{/if}}">
          Service : {{mb_value object=$selOp field="cote_hospi"}}
        </span> -
        <span class="{{if !$selOp->cote_bloc}}warning{{elseif $selOp->cote_bloc != $selOp->cote}}error{{else}}ok{{/if}}">
          Bloc : {{mb_value object=$selOp field="cote_bloc"}}
        </span>
      </td>
    </tr>
  {{/if}}

  {{assign var=consult_anesth value=$selOp->_ref_consult_anesth}}
  {{if $selOp->_ref_sejour->rques || $selOp->rques || $selOp->materiel || ($consult_anesth->_id && $consult_anesth->_intub_difficile)}}
    <!-- Mise en avant du matériel et remarques -->
    <tr>
      {{if $selOp->_ref_sejour->rques || $selOp->rques || ($consult_anesth->_id && $consult_anesth->_intub_difficile)}}
        {{if !$selOp->materiel}}
          <td class="text big-warning" colspan="2">
            {{else}}
          <td class="text big-warning halfPane">
        {{/if}}
        {{if $selOp->_ref_sejour->rques}}
          <strong>{{mb_label object=$selOp->_ref_sejour field=rques}}</strong>
          {{mb_value object=$selOp->_ref_sejour field=rques}}
        {{/if}}
        {{if $selOp->rques || ($consult_anesth->_id && $consult_anesth->_intub_difficile)}}
          <strong>{{mb_label object=$selOp field=rques}}</strong>
        {{/if}}
        {{if $selOp->rques}}
          {{mb_value object=$selOp field=rques}}
        {{/if}}
        {{if $consult_anesth->_id && $consult_anesth->_intub_difficile}}
          <div style="font-weight: bold; color:#f00;">
            {{tr}}CConsultAnesth-_intub_difficile{{/tr}}
          </div>
        {{/if}}
        </td>
      {{/if}}

      {{if $selOp->materiel}}
        {{if !$selOp->_ref_sejour->rques && !$selOp->rques}}
          <td class="text big-info" colspan="2">
            {{else}}
          <td class="text big-info halfPane">
        {{/if}}
        {{if $selOp->materiel}}
          <strong>{{mb_label object=$selOp field=materiel}}</strong>
          {{mb_value object=$selOp field=materiel}}
        {{/if}}
        </td>
      {{/if}}
    </tr>
  {{/if}}
</table>