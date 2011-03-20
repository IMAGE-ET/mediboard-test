{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $_sejour->type == 'ambu'}} {{assign var=background value="#faa"}}
{{elseif $_sejour->type == 'comp'}} {{assign var=background value="#fff"}}
{{elseif $_sejour->type == 'exte'}} {{assign var=background value="#afa"}}
{{elseif $_sejour->type == 'consult'}} {{assign var=background value="#cfdfff"}}
{{else}}
{{assign var=background value="#ccc"}}
{{/if}}

{{assign var="patient" value=$_sejour->_ref_patient}}

<td style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{if $canAdmissions->edit}}
  {{if $conf.dPplanningOp.COperation.verif_cote}}
  {{foreach from=$_sejour->_ref_operations item=curr_op}}
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
  <form name="editAdmFrm{{$_sejour->_id}}" action="?" method="post">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="sejour_id" value="{{$_sejour->_id}}" />
  <input type="hidden" name="patient_id" value="{{$_sejour->patient_id}}" />

  {{if !$_sejour->entree_reelle}}
    <input type="hidden" name="entree_reelle" value="now" />
    <button class="tick" type="button" onclick="{{if (($date_actuelle > $_sejour->entree_prevue) || ($date_demain < $_sejour->entree_prevue))}}confirmation(this.form);{{else}}submitAdmission(this.form);{{/if}};">
      Admettre
    </button>
    
  {{else}}
    <input type="hidden" name="entree_reelle" value="" />
    <button class="cancel" type="button" onclick="submitAdmission(this.form);">
      {{tr}}Cancel{{/tr}}
    </button>
    <br />
  
    {{if ($_sejour->entree_reelle < $date_min) || ($_sejour->entree_reelle > $date_max)}}
      {{$_sejour->entree_reelle|date_format:$conf.datetime}}
      <br>
    {{else}}
    {{$_sejour->entree_reelle|date_format:$conf.time}}
    {{/if}}
  {{/if}}
  </form>
  {{elseif $_sejour->entree_reelle}}
    {{if ($_sejour->entree_reelle < $date_min) || ($_sejour->entree_reelle > $date_max)}}
      {{$_sejour->entree_reelle|date_format:$conf.datetime}}
      <br>
    {{else}}
      {{$_sejour->entree_reelle|date_format:$conf.time}}
    {{/if}}
  {{else}}
    -
  {{/if}}
</td>

<td colspan="2" style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{if $canPlanningOp->read}}
  <a class="action" style="float: right" title="Modifier le séjour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$_sejour->_id}}">
    <img src="images/icons/planning.png" />
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
    SejourHprimSelector.init{{$_sejour->_id}} = function(){
      this.sForm      = "editNumdos{{$_sejour->_id}}";
      this.sId        = "id400";
      this.sIPPForm   = "editIPP{{$patient->_id}}";
      this.sIPPId     = "id400";
      this.sIPP       = document.forms.editIPP{{$patient->_id}}.id400.value;
      this.sPatNom    = "{{$patient->nom}}";
      this.sPatPrenom = "{{$patient->prenom}}";
      this.pop();
    };
  </script>
  {{if $_sejour->_ref_numdos}}
  <form name="editNumdos{{$_sejour->_id}}" action="?m={{$m}}" method="post" onsubmit="return ExtRefManager.submitNumdosForm({{$_sejour->_id}})">
    <input type="hidden" name="dosql" value="do_idsante400_aed" />
    <input type="hidden" name="m" value="dPsante400" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="ajax" value="1" />
    <input type="hidden" name="id_sante400_id" value="{{$_sejour->_ref_numdos->_id}}" />
    <input type="hidden" class="notNull" name="id400" value="{{$_sejour->_ref_numdos->id400}}" size="8" />
    <input type="hidden" class="notNull" name="tag" value="{{$_sejour->_ref_numdos->tag}}" />
    <input type="hidden" class="notNull" name="object_id" value="{{$_sejour->_id}}" />
    <input type="hidden" class="notNull" name="object_class" value="CSejour" />
    <input type="hidden" class="notNull" name="sejour_id" value="{{$_sejour->_id}}" />
    <input type="hidden" name="last_update" value="{{$_sejour->_ref_numdos->last_update}}" />
    {{if @$modules.hprim21}}
      <button type="button" class="edit notext" onclick="setExternalIds(this.form)">Edit external Ids</button>
    {{/if}}
  </form>
  {{/if}}
  {{/if}}
  {{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$_sejour->_num_dossier}}
  <a class="action" style="margin-right: 18px;" name="adm{{$_sejour->sejour_id}}" href="#1" onclick="printAdmission({{$_sejour->sejour_id}})">
    <span class="CPatient-view" onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">
      {{$patient}}
    </span>
  </a>
</td>

<td style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  <a href="#1" onclick="printAdmission({{$_sejour->sejour_id}})">
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
  </a>
</td>

<td style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  <a href="#1" onclick="printAdmission({{$_sejour->sejour_id}})">
    <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">
    {{$_sejour->entree_prevue|date_format:$conf.time}} 
    <br />
		{{$_sejour->type|upper|truncate:1:"":true}}
    {{$_sejour->_ref_operations|@count}} Int.
    </span>
  </a>
</td>

<td class="text" style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{if !($_sejour->type == 'exte') && !($_sejour->type == 'consult') && $_sejour->annule != 1}}
    {{if $canAdmissions->edit}}
      <form name="editChFrm{{$_sejour->sejour_id}}" action="?" method="post">
      <input type="hidden" name="m" value="dPplanningOp" />
      <input type="hidden" name="dosql" value="do_sejour_aed" />
      <input type="hidden" name="sejour_id" value="{{$_sejour->sejour_id}}" />
      <input type="hidden" name="patient_id" value="{{$_sejour->patient_id}}" />
      {{if $_sejour->chambre_seule}}
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
      <form name="editPrestFrm{{$_sejour->sejour_id}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_sejour_aed" />
        <input type="hidden" name="sejour_id" value="{{$_sejour->sejour_id}}" />
        <input type="hidden" name="patient_id" value="{{$_sejour->patient_id}}" />
        <select name="prestation_id" onchange="submitFormAjax(this.form, 'systemMsg')">
        <option value="">&mdash; Prestation</option>
        {{foreach from=$prestations item="_prestation"}}
          <option value="{{$_prestation->_id}}" {{if $_sejour->prestation_id==$_prestation->_id}} selected = selected {{/if}}>{{$_prestation->_view}}</option>
        {{/foreach}}
        </select>
      </form>
      {{/if}}
    {{else}}
      {{if $_sejour->chambre_seule}}
        Simple
      {{else}}
        Double
      {{/if}}
      {{if $_sejour->prestation_id && $prestations}}
        {{assign var=_prestation_id value=$_sejour->prestation_id}}
        <br />
        Prest. {{$prestations.$_prestation_id->_view}}
      {{/if}}
    {{/if}}
    <br />
    {{assign var=affectation value=$_sejour->_ref_first_affectation}}
    {{if $affectation->affectation_id}}
      {{$affectation->_ref_lit->_view}}
    {{else}}
      Non placé
    {{/if}}
  {{/if}}  
</td>

<td style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{if $canAdmissions->edit}}
    <form name="editSaisFrm{{$_sejour->_id}}" action="?" method="post">
      <input type="hidden" name="m" value="dPplanningOp" />
      <input type="hidden" name="dosql" value="do_sejour_aed" />
      <input type="hidden" name="sejour_id" value="{{$_sejour->_id}}" />
    	<input type="hidden" name="patient_id" value="{{$_sejour->patient_id}}" />
      
      {{if !$_sejour->saisi_SHS}}
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
      
      {{if ($_sejour->modif_SHS == 1) && ($conf.dPplanningOp.CSejour.modif_SHS == 1)}}
        <img src="images/icons/warning.png" title="Le dossier a été modifié, il faut le préparer" />
      {{/if}}
    </form>
  {{else}}
    {{mb_value object=$_sejour field="saisi_SHS"}}
  {{/if}}
</td>

<td style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{foreach from=$_sejour->_ref_operations item=curr_op}}
  {{if $curr_op->_ref_consult_anesth->_id}}
  <div class="{{if $curr_op->_ref_consult_anesth->_ref_consultation->chrono == 64}}small-success{{else}}small-info{{/if}}" style="margin: 0px;">
    <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_op->_ref_consult_anesth->_guid}}');">
    {{$curr_op->_ref_consult_anesth->_date_consult|date_format:$conf.date}}
    </span>
  </div>
  {{/if}}
  {{/foreach}}
</td>

<td style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}" class="button">
  {{if $_sejour->_couvert_cmu}}
    <img src="images/icons/tick.png" title="Droits CMU en cours" />
  {{else}}
    -
  {{/if}}
</td>

{{if $conf.dPadmissions.show_dh}}
  <td style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
    {{foreach from=$_sejour->_ref_operations item=curr_op}}
    {{if $curr_op->_ref_actes_ccam|@count}}
    <span style="color: #484;">
    {{foreach from=$curr_op->_ref_actes_ccam item=_acte}}
      {{if $_acte->montant_depassement}}
        {{if $_acte->code_activite == 1}}
        Chir :
        {{elseif $_acte->code_activite == 4}}
        Anesth :
        {{else}}
        Activité {{$_acte->code_activite}} :
        {{/if}}
        {{mb_value object=$_acte field=montant_depassement}}
        <br />
      {{/if}}
    {{/foreach}}
    </span>
    {{/if}}
    {{if $curr_op->depassement}}
    <!-- Pas de possibilité d'imprimer les dépassements pour l'instant -->
    <!-- <a href="#" onclick="printDepassement({{$_sejour->sejour_id}})"></a> -->
    Prévu : {{mb_value object=$curr_op field="depassement"}}
    <br />
    {{/if}}
    {{foreachelse}}
    -
    {{/foreach}}
  </td>
{{/if}}