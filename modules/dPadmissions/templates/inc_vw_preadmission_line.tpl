{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var="patient" value=$curr_consult->_ref_patient}}
{{assign var="curr_adm"  value=$curr_consult->_ref_consult_anesth->_ref_sejour}}

<td class="text">
  {{if $canPatients->edit}}
  <a class="action" style="float: right"  title="Modifier le dossier administratif" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$patient->patient_id}}">
    <img src="images/icons/edit.png" title="{{tr}}Edit{{/tr}}" />
  </a>
  {{/if}}
  <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">
    {{$patient->_view}}
  </span>
  </a>
</td>
<td class="text">{{$curr_consult->heure|date_format:$dPconfig.time}}</td>

{{if $curr_adm->_id}}

{{if $curr_adm->annule == 1}} {{assign var=background value="#f00"}}
{{elseif $curr_adm->type == 'ambu'}} {{assign var=background value="#faa"}}
{{elseif $curr_adm->type == 'comp'}} {{assign var=background value="#fff"}}
{{elseif $curr_adm->type == 'exte'}} {{assign var=background value="#afa"}}
{{elseif $curr_adm->type == 'urg'}} {{assign var=background value="#ff6"}}
{{else}}
{{assign var=background value="#ccc"}}
{{/if}}

<td class="text" style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$curr_adm->_ref_praticien}}
</td>
<td class="text" style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{if $curr_adm->_num_dossier}}[{{$curr_adm->_num_dossier}}]{{/if}}
  <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_adm->_guid}}');">
  le {{$curr_adm->_entree|date_format:$dPconfig.date}}
  </span>
</td>
<td class="text" style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
{{if $canAdmissions->edit}}
  <form name="editChFrm{{$curr_adm->sejour_id}}" action="?" method="post">
  
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="sejour_id" value="{{$curr_adm->sejour_id}}" />
  <input type="hidden" name="patient_id" value="{{$curr_adm->patient_id}}" />
  {{if $curr_adm->chambre_seule}}
  <input type="hidden" name="chambre_seule" value="0" />
  <button class="change" type="button" style="color: #f22" onclick="submitPreAdmission(this.form, 1);">
    Chambre simple
  </button>
  {{else}}
  <input type="hidden" name="chambre_seule" value="1" />
  <button class="change" type="button" onclick="submitPreAdmission(this.form, 1);">
    Chambre double
  </button>
  {{/if}}
  </form>
  
  <!-- Prestations -->
  {{if $prestations}}
  <form name="editPrestFrm{{$curr_adm->sejour_id}}" method="post">
    <input type="hidden" name="m" value="dPplanningOp" />
    <input type="hidden" name="dosql" value="do_sejour_aed" />
    <input type="hidden" name="sejour_id" value="{{$curr_adm->sejour_id}}" />
    <input type="hidden" name="patient_id" value="{{$curr_adm->patient_id}}" />
  <select name="prestation_id" onchange="submitFormAjax(this.form, 'systemMsg')">
  <option value="">&mdash; Prestation</option>
  {{foreach from=$prestations item="_prestation"}}
    <option value="{{$_prestation->_id}}" {{if $curr_adm->prestation_id==$_prestation->_id}} selected = selected {{/if}}>{{$_prestation->_view}}</option>
  {{/foreach}}
  </select>
  </form>
  {{/if}}
  {{else}}
  {{if $curr_adm->chambre_seule}}
    Chambre simple
  {{else}}
    Chambre double
  {{/if}}
  {{if $curr_adm->prestation_id && $prestations}}
  {{assign var=_prestation_id value=$curr_adm->prestation_id}}
  <br />
  Prest. {{$prestations.$_prestation_id->_view}}
  {{/if}}
  {{/if}}
  <br />
  {{assign var=affectation value=$curr_adm->_ref_first_affectation}}
  {{if $affectation->affectation_id}}
  {{$affectation->_ref_lit->_view}}
  {{else}}
  Non placé
  {{/if}}
</td>

<td style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{if $canAdmissions->edit}}
  <form name="editSaisFrm{{$curr_adm->_id}}" action="?" method="post">

  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="sejour_id" value="{{$curr_adm->_id}}" />
	<input type="hidden" name="patient_id" value="{{$curr_adm->patient_id}}" />
  {{if !$curr_adm->saisi_SHS}}
  <input type="hidden" name="saisi_SHS" value="1" />
  <button class="tick" type="button" onclick="submitPreAdmission(this.form);">
    {{tr}}CSejour-saisi_SHS{{/tr}}
  </button>
  {{else}}
  <input type="hidden" name="saisi_SHS" value="0" />
  <button class="cancel" type="button" onclick="submitPreAdmission(this.form);">
    {{tr}}Cancel{{/tr}}
  </button>
  {{/if}}
  {{if ($curr_adm->modif_SHS == 1) && ($dPconfig.dPplanningOp.CSejour.modif_SHS == 1)}}
    <img src="images/icons/warning.png" title="Le dossier a été modifié, il faut le préparer" />
  {{/if}}
  </form>
  {{else}}
  {{mb_value object=$curr_adm field="saisi_SHS"}}
  {{/if}}
</td>

<td style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{if $curr_adm->_couvert_cmu}}
    <img src="images/icons/tick.png" title="Droits CMU en cours" />
  {{else}}
    -
  {{/if}}
</td>

<td style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{foreach from=$curr_adm->_ref_operations item=curr_op}}
  {{if $curr_op->depassement}}
  <!-- Pas de possibilité d'imprimer les dépassements pour l'instant -->
  <!-- <a href="#" onclick="printDepassement({{$curr_adm->sejour_id}})"></a> -->
  {{mb_value object=$curr_op field="depassement"}}
  <br />
  {{/if}}
  {{foreachelse}}
  -
  {{/foreach}}
</td>
{{else}}
<td colspan="6" class="button">
  Pas de séjour associé à la consultation
</td>
{{/if}}