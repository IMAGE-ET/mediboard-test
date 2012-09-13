{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=background value="#ccc"}}
{{if $_sejour->type == 'ambu'   }} {{assign var=background value="#faa"}} {{/if}}
{{if $_sejour->type == 'comp'   }} {{assign var=background value="#fff"}} {{/if}}
{{if $_sejour->type == 'exte'   }} {{assign var=background value="#afa"}} {{/if}}
{{if $_sejour->type == 'consult'}} {{assign var=background value="#cdf"}} {{/if}}

{{assign var=patient value=$_sejour->_ref_patient}}

<td style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{if $canAdmissions->edit}}
  {{if $conf.dPplanningOp.COperation.verif_cote}}
  {{foreach from=$_sejour->_ref_operations item=curr_op}}
    {{if $curr_op->cote == "droit" || $curr_op->cote == "gauche"}}
      <form name="editCoteOp{{$curr_op->_id}}" action="?" method="post" class="prepared">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        {{mb_key object=$curr_op}}
        {{mb_label object=$curr_op field="cote_admission"}} :
        {{mb_field emptyLabel="Choose" object=$curr_op field="cote_admission" onchange="submitCote(this.form);"}}
      </form>
      <br />
    {{/if}}
  {{/foreach}}
  {{/if}}
  <script type="text/javascript">
    Main.add(function(){
      // Ceci doit rester ici !! prepareForm necessaire car pas appelé au premier refresh d'un periodical update
      prepareForm("editAdmFrm{{$_sejour->_id}}");
    });
  </script>
  <form name="editAdmFrm{{$_sejour->_id}}" action="?" method="post">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="sejour_id" value="{{$_sejour->_id}}" />
  <input type="hidden" name="patient_id" value="{{$_sejour->patient_id}}" />
  
  {{if !$_sejour->entree_reelle}}
    <input type="hidden" name="entree_reelle" value="now" />
    <input type="hidden" name="_modifier_entree" value="1" />
    {{mb_field object=$_sejour field=mode_entree onchange="\$V(this.form._modifier_entree, 0); submitAdmission(this.form);"}}
    <button class="tick" type="button" onclick="{{if (($date_actuelle > $_sejour->entree_prevue) || ($date_demain < $_sejour->entree_prevue))}}confirmation(this.form);{{else}}submitAdmission(this.form);{{/if}};">
      {{tr}}CSejour-admit{{/tr}}
    </button>
    <div id="listEtabExterne-editAdmFrm{{$_sejour->_id}}" {{if $_sejour->mode_entree != "7"}} style="display: none;" {{/if}}>
      {{mb_field object=$_sejour field="etablissement_entree_id" form="editAdmFrm`$_sejour->_id`" 
        autocomplete="true,1,50,true,true" onchange="changeEtablissementId(this.form)"}}
    </div>
  {{else}}
    <input type="hidden" name="_modifier_entree" value="0" />
    <input type="hidden" name="mode_entree" value="{{$_sejour->mode_entree}}" />
    <input type="hidden" name="etablissement_entree_id" value="{{$_sejour->etablissement_entree_id}}" />
    
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
    - {{tr}}CSejour.mode_entree.{{$_sejour->mode_entree}}{{/tr}}
          
    {{if $_sejour->etablissement_entree_id}}
      - {{$_sejour->_ref_etablissement_provenance}}
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
    {{if $_sejour->mode_sortie}}
      <br />
      {{tr}}CSejour.mode_entree.{{$_sejour->mode_entree}}{{/tr}}
    {{/if}}
    
    {{if $_sejour->etablissement_entree_id}}
      <br />{{$_sejour->_ref_etablissement_provenance}}
    {{/if}}
  {{else}}
    -
  {{/if}}
</td>

<td colspan="2" class="text" style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{if $canPlanningOp->read}}
    <div style="float: right;">
      {{if "web100T"|module_active}}
        {{mb_include module=web100T template=inc_button_iframe}}
      {{/if}}
      
      {{if $conf.dPadmissions.show_deficience}}
        {{mb_include module=patients template=inc_vw_antecedents type=deficience}}
      {{/if}}
      
      {{foreach from=$_sejour->_ref_operations item=_op}}
      <a class="action" title="Imprimer la DHE de l'intervention" href="#printDHE" onclick="Admissions.printDHE('operation_id', {{$_op->_id}}); return false;">
        <img src="images/icons/print.png" />
      </a>
      {{foreachelse}}
      <a class="action" title="Imprimer la DHE du séjour" href="#printDHE" onclick="Admissions.printDHE('sejour_id', {{$_sejour->_id}}); return false;">
        <img src="images/icons/print.png" />
      </a>
      {{/foreach}}
        
      <a class="action" title="Modifier le séjour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$_sejour->_id}}">
        <img src="images/icons/planning.png" />
      </a>
      
      {{mb_include module=system template=inc_object_notes object=$_sejour}}
    </div>
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
  <form name="editIPP{{$patient->_id}}" action="?m={{$m}}" method="post" class="prepared">
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
  {{if $_sejour->_ref_NDA}}
  <form name="editNumdos{{$_sejour->_id}}" action="?m={{$m}}" method="post" class="prepared" onsubmit="return ExtRefManager.submitNumdosForm({{$_sejour->_id}})">
    <input type="hidden" name="dosql" value="do_idsante400_aed" />
    <input type="hidden" name="m" value="dPsante400" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="ajax" value="1" />
    <input type="hidden" name="id_sante400_id" value="{{$_sejour->_ref_NDA->_id}}" />
    <input type="hidden" class="notNull" name="id400" value="{{$_sejour->_ref_NDA->id400}}" size="8" />
    <input type="hidden" class="notNull" name="tag" value="{{$_sejour->_ref_NDA->tag}}" />
    <input type="hidden" class="notNull" name="object_id" value="{{$_sejour->_id}}" />
    <input type="hidden" class="notNull" name="object_class" value="CSejour" />
    <input type="hidden" class="notNull" name="sejour_id" value="{{$_sejour->_id}}" />
    <input type="hidden" name="last_update" value="{{$_sejour->_ref_NDA->last_update}}" />
    {{if @$modules.hprim21}}
      <button type="button" class="edit notext" onclick="setExternalIds(this.form)">Edit external Ids</button>
    {{/if}}
  </form>
  {{/if}}
  {{/if}}
  <input type="checkbox" name="print_doc" value="{{$_sejour->_id}}"/>
  {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$_sejour _show_numdoss_modal=1}}
  <span class="CPatient-view" onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">
    {{$patient}}
  </span>
</td>

<td class="text" style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
</td>

<td style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">
    {{$_sejour->entree_prevue|date_format:$conf.time}} 
    <br />
    {{$_sejour->type|upper|truncate:1:"":true}}
    {{$_sejour->_ref_operations|@count}} Int.
  </span>
</td>

<td style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{if !($_sejour->type == 'exte') && !($_sejour->type == 'consult') && $_sejour->annule != 1}}
    {{mb_include template=inc_form_prestations sejour=$_sejour edit=$canAdmissions->edit}}
    {{mb_include module=hospi template=inc_placement_sejour sejour=$_sejour}}
  {{/if}}  
</td>

<td style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{if $canAdmissions->edit}}
    <form name="editSaisFrm{{$_sejour->_id}}" action="?" method="post" class="prepared">
      <input type="hidden" name="m" value="dPplanningOp" />
      <input type="hidden" name="dosql" value="do_sejour_aed" />
      <input type="hidden" name="sejour_id" value="{{$_sejour->_id}}" />
      <input type="hidden" name="patient_id" value="{{$_sejour->patient_id}}" />
      
      {{if !$_sejour->entree_preparee}}
        <input type="hidden" name="entree_preparee" value="1" />
        <button class="tick" type="button" onclick="submitAdmission(this.form, 1);">
          {{tr}}CSejour-entree_preparee{{/tr}}
        </button>
      {{else}}
        <input type="hidden" name="entree_preparee" value="0" />
        <button class="cancel" type="button" onclick="submitAdmission(this.form, 1);">
          {{tr}}Cancel{{/tr}}
        </button>
      {{/if}}
      
      {{if ($_sejour->entree_modifiee == 1) && ($conf.dPplanningOp.CSejour.entree_modifiee == 1)}}
        <img src="images/icons/warning.png" title="Le dossier a été modifié, il faut le préparer" />
      {{/if}}
    </form>
  {{else}}
    {{mb_value object=$_sejour field="entree_preparee"}}
  {{/if}}
</td>

<td style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{foreach from=$_sejour->_ref_operations item=_op}}
  {{if $_op->_ref_consult_anesth->_id}}
  <div class="{{if $_op->_ref_consult_anesth->_ref_consultation->chrono == 64}}small-success{{else}}small-info{{/if}}" style="margin: 0px;">
    <span onmouseover="ObjectTooltip.createEx(this, '{{$_op->_ref_consult_anesth->_ref_consultation->_guid}}');">
    {{$_op->_ref_consult_anesth->_date_consult|date_format:$conf.date}}
    </span>
  </div>
  {{/if}}
  {{/foreach}}
</td>

<td style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}" class="button">
  {{if $_sejour->_couvert_cmu}}
  <div><strong>CMU</strong></div>
  {{/if}}
  {{if $_sejour->_couvert_ald}}
  <div><strong {{if $_sejour->ald}}style="color: red;"{{/if}}>ALD</strong></div>
  {{/if}}
</td>

{{if $conf.dPadmissions.show_dh}}
  <td style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
    {{foreach from=$_sejour->_ref_operations item=_op}}
    {{if $_op->_ref_actes_ccam|@count}}
    <span style="color: #484;">
    {{foreach from=$_op->_ref_actes_ccam item=_acte}}
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
    {{if $_op->depassement}}
    <!-- Pas de possibilité d'imprimer les dépassements pour l'instant -->
    <!-- <a href="#" onclick="printDepassement({{$_sejour->sejour_id}})"></a> -->
    Prévu : {{mb_value object=$_op field="depassement"}}
    <br />
    {{/if}}
    {{foreachelse}}
    -
    {{/foreach}}
  </td>
{{/if}}