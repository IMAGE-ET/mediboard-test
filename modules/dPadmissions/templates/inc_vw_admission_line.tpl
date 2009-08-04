{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $curr_adm->annule == 1}} {{assign var=background value="#f33"}}
{{elseif $curr_adm->type == 'ambu'}} {{assign var=background value="#faa"}}
{{elseif $curr_adm->type == 'comp'}} {{assign var=background value="#fff"}}
{{elseif $curr_adm->type == 'exte'}} {{assign var=background value="#afa"}}
{{elseif $curr_adm->type == 'urg'}} {{assign var=background value="#ff6"}}
{{else}}
{{assign var=background value="#ccc"}}
{{/if}}

{{assign var="patient" value=$curr_adm->_ref_patient}}

<td class="text" style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  <a class="action" style="float: right"  title="Modifier le dossier administratif" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$patient->patient_id}}">
    <img src="images/icons/edit.png" alt="modifier" />
  </a>
  {{if $canPlanningOp->read}}
  <a class="action" style="float: right"  title="Modifier le séjour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$curr_adm->_id}}">
    <img src="images/icons/planning.png" alt="modifier" />
  </a>
  {{/if}}
  
  {{if $patient->_ref_IPP}}
  <script type="text/javascript">
    PatHprimSelector.init{{$patient->_id}} = function(){
      this.sForm      = "editIPP{{$patient->_id}}";
      this.sId        = "id400";
      this.sPatNom    = "{{$patient->nom}}";
      this.sPatPrenom = "{{$patient->prenom}}";
      this.pop();
    };
  </script>
  <form name="editIPP{{$patient->_id}}" action="?m={{$m}}" method="post">
    <input type="hidden" name="dosql" value="do_idsante400_aed" />
    <input type="hidden" name="m" value="dPsante400" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="ajax" value="1" />
    <input type="hidden" name="id_sante400_id" value="{{$patient->_ref_IPP->_id}}" />
    <input type="hidden" class="notNull" name="id400" value="{{$patient->_ref_IPP->id400}}" />
    <input type="hidden" class="notNull" name="tag" value="{{$patient->_ref_IPP->tag}}" />
    <input type="hidden" class="notNull" name="object_id" value="{{$patient->_id}}" />
    <input type="hidden" class="notNull" name="object_class" value="CPatient" />
    <input type="hidden" name="last_update" value="{{$patient->_ref_IPP->last_update}}" />
  </form>
  
  <script type="text/javascript">
    SejourHprimSelector.init{{$curr_adm->_id}} = function(){
      this.sForm      = "editNumdos{{$curr_adm->_id}}";
      this.sId        = "id400";
      this.sIPPForm   = "editIPP{{$patient->_id}}";
      this.sIPPId     = "id400";
      this.sIPP       = document.forms.editIPP{{$patient->_id}}.id400.value;
      this.sPatNom    = "{{$patient->nom}}";
      this.sPatPrenom = "{{$patient->prenom}}";
      this.pop();
    };
  </script>
  {{if $curr_adm->_ref_numdos}}
  <form name="editNumdos{{$curr_adm->_id}}" action="?m={{$m}}" method="post" onsubmit="return ExtRefManager.submitNumdosForm({{$curr_adm->_id}})">
    <input type="hidden" name="dosql" value="do_idsante400_aed" />
    <input type="hidden" name="m" value="dPsante400" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="ajax" value="1" />
    <input type="hidden" name="id_sante400_id" value="{{$curr_adm->_ref_numdos->_id}}" />
    <input type="hidden" class="notNull" name="id400" value="{{$curr_adm->_ref_numdos->id400}}" size="8" />
    <input type="hidden" class="notNull" name="tag" value="{{$curr_adm->_ref_numdos->tag}}" />
    <input type="hidden" class="notNull" name="object_id" value="{{$curr_adm->_id}}" />
    <input type="hidden" class="notNull" name="object_class" value="CSejour" />
    <input type="hidden" class="notNull" name="sejour_id" value="{{$curr_adm->_id}}" />
    <input type="hidden" name="last_update" value="{{$curr_adm->_ref_numdos->last_update}}" />
    {{if @$modules.hprim21}}
      <button type="button" class="edit notext" onclick="setExternalIds(this.form)">Edit external Ids</button>
    {{/if}}
  </form>
  {{/if}}
  {{/if}}
  {{if $curr_adm->_num_dossier}}[{{$curr_adm->_num_dossier}}]{{/if}}
  <a class="action" name="adm{{$curr_adm->sejour_id}}" href="#" onclick="printAdmission({{$curr_adm->sejour_id}})">
    {{$patient->_view}}
  </a>
</td>

<td class="text" style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  <a href="#" onclick="printAdmission({{$curr_adm->sejour_id}})">
  Dr {{$curr_adm->_ref_praticien->_view}}
  </a>
</td>

<td style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  <a href="#" onclick="printAdmission({{$curr_adm->sejour_id}})">
  {{$curr_adm->entree_prevue|date_format:$dPconfig.time}} ({{$curr_adm->type|truncate:1:"":true}})
  </a>
</td>

<td class="text" style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  <form name="editChFrm{{$curr_adm->sejour_id}}" action="?" method="post">
  
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="sejour_id" value="{{$curr_adm->sejour_id}}" />
  <input type="hidden" name="patient_id" value="{{$curr_adm->patient_id}}" />
  {{if $curr_adm->chambre_seule}}
  <input type="hidden" name="chambre_seule" value="0" />
  <button class="change" type="button" style="color: #f22" onclick="submitAdmission(this.form, 1);">
    Chambre simple
  </button>
  {{else}}
  <input type="hidden" name="chambre_seule" value="1" />
  <button class="change" type="button" onclick="submitAdmission(this.form, 1);">
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
  <br />
  {{assign var=affectation value=$curr_adm->_ref_first_affectation}}
  {{if $affectation->affectation_id}}
  {{$affectation->_ref_lit->_view}}
  {{else}}
  Pas de chambre
  {{/if}}
  
</td>

{{if $curr_adm->annule == 1}}
<td style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}" align="center" colspan="5">
  <strong>ANNULE</strong></td>
{{else}}
<td style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{if $dPconfig.dPplanningOp.COperation.verif_cote}}
  {{foreach from=$curr_adm->_ref_operations item=curr_op}}
    {{if $curr_op->cote == "droit" || $curr_op->cote == "gauche"}}
      <form name="editCoteOp{{$curr_op->_id}}" action="?" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        {{mb_key object=$curr_op}}
        {{mb_label object=$curr_op field="cote_admission"}} :
        {{mb_field defaultOption="&mdash; choisir" object=$curr_op field="cote_admission" onchange="submitCote(this.form);"}}
      </form>
      <br />
    {{/if}}
  {{/foreach}}
  {{/if}}
  <form name="editAdmFrm{{$curr_adm->_id}}" action="?" method="post">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="sejour_id" value="{{$curr_adm->_id}}" />
  <input type="hidden" name="patient_id" value="{{$curr_adm->patient_id}}" />

  {{if !$curr_adm->entree_reelle}}
	  <input type="hidden" name="entree_reelle" value="now" />
	  <button class="tick" type="button" onclick="{{if (($date_actuelle > $curr_adm->entree_prevue) || ($date_demain < $curr_adm->entree_prevue))}}confirmation(this.form);{{else}}submitAdmission(this.form);{{/if}};">
	    Admettre
	  </button>
	  
  {{else}}
	  <input type="hidden" name="entree_reelle" value="" />
	  <button class="cancel" type="button" onclick="submitAdmission(this.form);">
	    {{tr}}Cancel{{/tr}}
	  </button>
	  <br />
  
 	  {{if ($curr_adm->entree_reelle < $date_min) || ($curr_adm->entree_reelle > $date_max)}}
	    {{$curr_adm->entree_reelle|date_format:$dPconfig.datetime}}
	    <br>
	  {{else}}
	  {{$curr_adm->entree_reelle|date_format:$dPconfig.time}}
	  {{/if}}
  {{/if}}
  </form>
</td>

<td style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  <form name="editSaisFrm{{$curr_adm->_id}}" action="?" method="post">

  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="sejour_id" value="{{$curr_adm->_id}}" />
	<input type="hidden" name="patient_id" value="{{$curr_adm->patient_id}}" />
  {{if !$curr_adm->saisi_SHS}}
  <input type="hidden" name="saisi_SHS" value="1" />
  <button class="tick" type="button" onclick="submitAdmission(this.form, 1);">
    {{tr}}CSejour-saisi_SHS{{/tr}}
  </button>
  {{else}}
  <input type="hidden" name="saisi_SHS" value="0" />
  <button class="cancel" type="button" onclick="submitAdmission(this.form, 1);">
    {{tr}}Cancel{{/tr}}
  </button>
  {{/if}}
  {{if $curr_adm->modif_SHS == 1}}
  <img src="images/icons/warning.png" alt="warning" title="Le dossier a été modifié, il faut le répréparer" />
  {{/if}}
  </form>
</td>

<td style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{foreach from=$curr_adm->_ref_operations item=curr_op}}
  {{if $curr_op->_ref_consult_anesth->consultation_anesth_id}}
  <div class="{{if $curr_op->_ref_consult_anesth->_ref_consultation->chrono == 64}}small-success{{/if}}">
  {{$curr_op->_ref_consult_anesth->_date_consult|date_format:$dPconfig.date}}
  </div>
  {{/if}}
  {{/foreach}}
</td>

<td style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}" class="button">
  {{if $curr_adm->_couvert_cmu}}
    <img src="images/icons/tick.png" alt="Droits CMU en cours" />
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
{{/if}}