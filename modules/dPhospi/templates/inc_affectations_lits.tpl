<tr class="lit" id="lit-{{$curr_lit->_id}}">
  <td>
    {{if $curr_lit->_overbooking}}
      <img src="images/icons/warning.png" alt="warning" title="Over-booking: {{$curr_lit->_overbooking}} collisions" />
    {{/if}}
    {{$curr_lit->nom}}
  </td>
  <td class="action">
    {{if $can->edit}}
      <input name="choixLit" type="radio" id="lit{{$curr_lit->_id}}" onclick="selectLit({{$curr_lit->_id}})" />
    {{/if}}
  </td>
</tr>
{{foreach from=$curr_lit->_ref_affectations item=curr_affectation}}
{{assign var="sejour" value=$curr_affectation->_ref_sejour}}
{{assign var="patient" value=$sejour->_ref_patient}}
{{assign var="aff_prev" value=$curr_affectation->_ref_prev}}
{{assign var="aff_next" value=$curr_affectation->_ref_next}}

<tr class="patient">
 
  {{if $curr_affectation->confirme}}
    <td class="text" style="background-image:url(images/icons/ray.gif); background-repeat:repeat;">
  {{else}}
    <td class="text">
  {{/if}}
  {{if $curr_affectation->sejour_id}}
    <a style="float: right;" href="?m=dPpatients&amp;tab=vw_idx_patients&amp;patient_id={{$patient->_id}}"
              onmouseover="PrevTimeHospi.show({{$curr_affectation->_id}}, {{$sejour->praticien_id}}, '{{$sejour->_codes_ccam}}')"
              onmouseout="PrevTimeHospi.hide({{$curr_affectation->_id}})">
      <img src="images/icons/edit.png" alt="edit" title="Editer le patient" />
    </a>
    <div id="tpsPrev{{$curr_affectation->_id}}" class="tooltip" style="display: none; padding: 5px;">
    </div>
    
    {{if $sejour->_couvert_cmu}}
    <div style="float: right;"><strong>CMU</strong></div>
    {{/if}}
          
    {{if !$sejour->entree_reelle || ($aff_prev->_id && $aff_prev->effectue == 0)}}
      <font style="color:#a33">
    {{else}}
      {{if $sejour->septique == 1}}
        <font style="color:#3a3">
      {{else}}
        <font>
     {{/if}}
    {{/if}} 
    {{if $sejour->type == "ambu"}}
      <img src="images/icons/X.png" alt="X" title="Sortant ce soir" />
    {{elseif $curr_affectation->sortie|date_format:"%Y-%m-%d" == $demain}}
      {{if $aff_next->_id}}
        <img src="images/icons/OC.png" alt="OC" title="Sortant demain" />
      {{else}}
        <img src="images/icons/O.png" alt="O" title="Sortant demain" />
      {{/if}}
    {{elseif $curr_affectation->sortie|date_format:"%Y-%m-%d" == $date}}
      {{if $aff_next->_id}}
        <img src="images/icons/OoC.png" alt="OoC" title="Sortant aujourd'hui" />
      {{else}}
        <img src="images/icons/Oo.png" alt="Oo" title="Sortant aujourd'hui" />
      {{/if}}
    {{/if}}
    {{if $sejour->type == "ambu"}}
      <em>{{$patient->_view}}</em>
    {{else}}
      <strong>{{$patient->_view}}</strong>
    {{/if}}
    {{if (!$sejour->entree_reelle) || ($aff_prev->_id && $aff_prev->effectue == 0)}}
      {{$curr_affectation->entree|date_format:"%d/%m %Hh%M"}}
    {{/if}}
    </font>
  {{else}}
  <em>[LIT BLOQUE]</em>
  {{/if}}
  </td>
  <td class="action" style="background:#{{$sejour->_ref_praticien->_ref_function->color}}">
    {{$sejour->_ref_praticien->_shortview}}
  </td>
</tr>
{{if !$curr_affectation->sejour_id}}
 
  <tr class="dates">   
    <td class="text">
    {{if $can->edit}}
    <form name="rmvAffectation{{$curr_affectation->_id}}" action="?m={{$m}}" method="post">
    <input type="hidden" name="dosql" value="do_affectation_aed" />
    <input type="hidden" name="del" value="1" />
    <input type="hidden" name="affectation_id" value="{{$curr_affectation->_id}}" />
    </form>
    <a style="float: right;" href="#" onclick="confirmDeletion(document.rmvAffectation{{$curr_affectation->_id}},{typeName:'l\'affectation',objName:'{{$patient->_view|smarty:nodefaults|JSAttribute}}'})">
      <img src="images/icons/trash.png" alt="trash" title="Supprimer l'affectation" />
    </a>
    {{/if}}
    <em>Du</em>:
    {{$curr_affectation->entree|date_format:"%A %d %B %Hh%M"}}
    ({{$curr_affectation->_entree_relative}} jours)
  </td>
  <td class="action">
    {{if $can->edit}}
    <form name="entreeAffectation{{$curr_affectation->_id}}" action="?m={{$m}}" method="post">
    <input type="hidden" name="dosql" value="do_affectation_aed" />
    <input type="hidden" name="affectation_id" value="{{$curr_affectation->_id}}" />
    <input type="hidden" name="entree" value="{{$curr_affectation->entree}}" />
    </form>
    <a>
      <img id="entreeAffectation{{$curr_affectation->_id}}__trigger_entree" src="images/icons/planning.png" alt="Planning" title="Modifier la date de début" />
    </a>
    {{/if}}
  </td>
  </tr>
  <tr class="dates">
  <td class="text">
    <em>Au</em>:
    {{$curr_affectation->sortie|date_format:"%A %d %B %Hh%M"}}
    ({{$curr_affectation->_sortie_relative}} jours)
  </td>
  <td class="action">
    {{if $can->edit}}
    <form name="sortieAffectation{{$curr_affectation->_id}}" action="?m={{$m}}" method="post">
    <input type="hidden" name="dosql" value="do_affectation_aed" />
    <input type="hidden" name="affectation_id" value="{{$curr_affectation->_id}}" />
    <input type="hidden" name="sortie" value="{{$curr_affectation->sortie}}" />
    </form>
    <a>
      <img id="sortieAffectation{{$curr_affectation->_id}}__trigger_sortie" src="images/icons/planning.png" alt="Planning" title="Modifier la date de fin" />
    </a>
    {{/if}}
  </td>
  </tr>

  {{if $curr_affectation->rques}}
    <tr class="dates">
      <td class="text highlight" colspan="2">
        <em>Remarques:</em> {{$curr_affectation->rques|nl2br}}
      </td>
    </tr>
  {{/if}}
{{else}}

<tr class="dates">
  <td class="text">
    {{if $can->edit}}
      <form name="rmvAffectation{{$curr_affectation->_id}}" action="?m={{$m}}" method="post">

      <input type="hidden" name="dosql" value="do_affectation_aed" />
      <input type="hidden" name="del" value="1" />
      <input type="hidden" name="affectation_id" value="{{$curr_affectation->_id}}" />

      </form>
      <a style="float: right;" href="#" onclick="confirmDeletion(document.rmvAffectation{{$curr_affectation->_id}},{typeName:'l\'affectation',objName:'{{$patient->_view|addslashes}}'})">
        <img src="images/icons/trash.png" alt="trash" title="Supprimer l'affectation" />
      </a>
    {{/if}}
    {{if $aff_prev->_id}}
      <em>Déplacé</em> (chambre: {{$aff_prev->_ref_lit->_ref_chambre->nom}}):
      {{$curr_affectation->entree|date_format:"%A %d %B %Hh%M"}}
      ({{$curr_affectation->_entree_relative}} jours)
    {{else}}
      <em>Entrée</em>:
      {{$curr_affectation->entree|date_format:"%A %d %B %Hh%M"}}
      ({{$curr_affectation->_entree_relative}} jours)
    {{/if}}
  </td>
  <td class="action">
    {{if $can->edit}}
    <form name="entreeAffectation{{$curr_affectation->_id}}" action="?m={{$m}}" method="post">

    <input type="hidden" name="dosql" value="do_affectation_aed" />
    <input type="hidden" name="affectation_id" value="{{$curr_affectation->_id}}" />
    <input type="hidden" name="entree" value="{{$curr_affectation->entree}}" />

    </form>
    
    <a>
      <img id="entreeAffectation{{$curr_affectation->_id}}__trigger_entree" src="images/icons/planning.png" alt="Planning" title="Modifier la date d'entrée" />
    </a>
    {{/if}}
  </td>
</tr>
<tr class="dates">
  <td class="text">
    {{if $aff_next->_id}}
      <em>Déplacé</em> (chambre: {{$aff_next->_ref_lit->_ref_chambre->nom}}):
      {{$curr_affectation->sortie|date_format:"%A %d %B %Hh%M"}}
      ({{$curr_affectation->_sortie_relative}} jours)
    {{else}}
      {{if $can->edit}}
        <form name="splitAffectation{{$curr_affectation->_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="dosql" value="do_affectation_split" />
          <input type="hidden" name="affectation_id" value="{{$curr_affectation->_id}}" />
          <input type="hidden" name="sejour_id" value="{{$curr_affectation->sejour_id}}" />
          <input type="hidden" name="entree" value="{{$curr_affectation->entree}}" />
          <input type="hidden" name="sortie" value="{{$curr_affectation->sortie}}" />
          <input type="hidden" name="no_synchro" value="1" />
          <input type="hidden" name="_new_lit_id" value="" />
          <input type="hidden" name="_date_split" value="{{$curr_affectation->sortie}}" />
        </form>
        <a style="float: right;">
          <img id="splitAffectation{{$curr_affectation->_id}}__trigger_split" src="images/icons/move.gif" alt="Move" title="Déplacer un patient" />
        </a>
      {{/if}}
      <em>Sortie</em>:
      {{$curr_affectation->sortie|date_format:"%A %d %B %Hh%M"}}
      ({{$curr_affectation->_sortie_relative}} jours)
    {{/if}}
  </td>
  <td class="action">
    {{if $can->edit}}
    <form name="sortieAffectation{{$curr_affectation->_id}}" action="?m={{$m}}" method="post">

    <input type="hidden" name="dosql" value="do_affectation_aed" />
    <input type="hidden" name="affectation_id" value="{{$curr_affectation->_id}}" />
    <input type="hidden" name="sortie" value="{{$curr_affectation->sortie}}" />

    </form>
    
    <a>
      <img id="sortieAffectation{{$curr_affectation->_id}}__trigger_sortie" src="images/icons/planning.png" alt="Planning" title="Modifier la date de sortie" />
    </a>
    {{/if}}
  </td>
</tr>

<tr class="dates">
  <td colspan="2"><em>Age</em>: {{$patient->_age}} ans ({{mb_value object=$patient field=naissance}})</td>
</tr>
<tr class="dates">
  <td class="text" colspan="2"><em>Dr {{$sejour->_ref_praticien->_view}}</em></td>
</tr>

   {{if $sejour->prestation_id}}
     <tr class="dates">
       <td colspan="2">
      <strong>Prestation:</strong> {{$sejour->_ref_prestation->_view}}
     </td>
     </tr>
    {{/if}}
   
<tr class="dates">
  <td class="text" colspan="2">
    {{foreach from=$sejour->_ref_operations item=curr_operation}}
      {{if $curr_operation->libelle}}
      <em>[{{$curr_operation->libelle}}]</em>
      <br />
      {{/if}}
      {{foreach from=$curr_operation->_ext_codes_ccam item=curr_code}}
      <em>{{$curr_code->code}}</em> : {{$curr_code->libelleLong}}<br />
      {{/foreach}}
    {{/foreach}}
  </td>
</tr>
<tr class="dates">
  <td class="text" colspan="2">
    <form name="SeptieSejour{{$sejour->_id}}" action="?m=dPhospi" method="post">

    <input type="hidden" name="m" value="dPplanningOp" />
    <input type="hidden" name="otherm" value="dPhospi" />
    <input type="hidden" name="dosql" value="do_sejour_aed" />
    <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
    
    <em>Pathologie</em>:
    {{$sejour->pathologie}}
    -
    {{if $can->edit}}
    <input type="radio" name="septique" value="0" {{if $sejour->septique == 0}} checked="checked" {{/if}} onclick="this.form.submit()" />
    <label for="septique_0" title="Séjour propre">Propre</label>
    <input type="radio" name="septique" value="1" {{if $sejour->septique == 1}} checked="checked" {{/if}} onclick="this.form.submit()" />
    <label for="septique_1" title="Séjour septique">Septique</label>
    {{else}}
{{if $sejour->septique == 0}}
Propre
{{else}}
Septique
{{/if}}
    {{/if}}
    </form>

  </td>
</tr>
{{if $sejour->rques != ""}}
<tr class="dates">
  <td class="text highlight" colspan="2">
    <em>Séjour</em>: {{$sejour->rques|nl2br}}
  </td>
</tr>
{{/if}}
{{foreach from=$sejour->_ref_operations item=curr_operation}}
{{if $curr_operation->rques != ""}}
<tr class="dates">
  <td class="text highlight" colspan="2">
    <em>Intervention</em>: {{$curr_operation->rques|nl2br}}
  </td>
</tr>
{{/if}}
{{/foreach}}
{{if $patient->rques != ""}}
<tr class="dates">
  <td class="text highlight" colspan="2">
    <em>Patient</em>: {{$patient->rques|nl2br}}
  </td>
</tr>
{{/if}}
<tr class="dates">
  <td class="text" colspan="2">
    {{if $can->edit}}
    <form name="editChFrm{{$sejour->_id}}" action="?m=dPhospi" method="post">

	  <input type="hidden" name="m" value="dPplanningOp" />
	  <input type="hidden" name="dosql" value="do_sejour_aed" />
	  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />

    {{if $sejour->chambre_seule}}
    <input type="hidden" name="chambre_seule" value="0" />
    <button class="change" type="submit" style="color: #f22;">
      Chambre simple
    </button>
    {{else}}
    <input type="hidden" name="chambre_seule" value="1" />
    <button class="change" type="submit">
      Chambre double
    </button>
    {{/if}}
    </form>
    {{/if}}
  </td>
</tr>

{{/if}}

{{foreachelse}}
<tr class="litdispo"><td colspan="2">Lit disponible</td></tr>
<tr class="litdispo">
  <td class="text" colspan="2">
  depuis:
  {{if $curr_lit->_ref_last_dispo && $curr_lit->_ref_last_dispo->_id}}
  {{$curr_lit->_ref_last_dispo->sortie|date_format:"%A %d %B %Hh%M"}} 
  ({{$curr_lit->_ref_last_dispo->_sortie_relative}} jours)
  {{else}}
  Toujours
  {{/if}}
  </td>
</tr>
<tr class="litdispo">
  <td class="text" colspan="2">
  jusque: 
  {{if $curr_lit->_ref_next_dispo && $curr_lit->_ref_next_dispo->_id}}
  {{$curr_lit->_ref_next_dispo->entree|date_format:"%A %d %B %Hh%M"}}
  ({{$curr_lit->_ref_next_dispo->_entree_relative}} jours)
  {{else}}
  Toujours
  {{/if}}
  </td>
</tr>
{{/foreach}}