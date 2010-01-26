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
<tr>
  <td colspan="2" style="padding: 0px;">
  {{foreach from=$curr_lit->_ref_affectations item=curr_affectation}}
  {{assign var="sejour" value=$curr_affectation->_ref_sejour}}
  {{assign var="patient" value=$sejour->_ref_patient}}
  {{assign var="aff_prev" value=$curr_affectation->_ref_prev}}
  {{assign var="aff_next" value=$curr_affectation->_ref_next}}
  <form name="addAffectationaffectation_{{$curr_affectation->_id}}" action="?m={{$m}}" method="post">

    <input type="hidden" name="dosql" value="do_affectation_aed" />
    <input type="hidden" name="affectation_id" value="{{$curr_affectation->_id}}" />
    <input type="hidden" name="lit_id" value="" />
    <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
    <input type="hidden" name="entree" value="{{$curr_affectation->entree}}" />
    <input type="hidden" name="sortie" value="{{$curr_affectation->sortie}}" />

  </form>
  <table class="tbl" id="affectation_{{$curr_affectation->_id}}">
    <tr class="patient">
      {{if $curr_affectation->confirme}}
      <td class="text" style="background-image:url(images/icons/ray.gif); background-repeat:repeat;">
      {{else}}
      <td class="text">
      {{/if}}
      {{if $curr_affectation->sejour_id}}
        <script type="text/javascript">new Draggable('affectation_{{$curr_affectation->_id}}', {revert:true})</script>
        <a style="float: right;" href="?m=dPpatients&amp;tab=vw_idx_patients&amp;patient_id={{$patient->_id}}">
          <img src="images/icons/edit.png" alt="edit" title="Modifier le dossier administratif du patient" />
        </a>
        {{if $sejour->_couvert_cmu}}
        <div style="float: right;"><strong>CMU</strong></div>
        {{/if}}
          
        {{if !$sejour->entree_reelle || ($aff_prev->_id && $aff_prev->effectue == 0)}}
        <font class="patient-not-arrived">
        {{else}}
          {{if $sejour->septique}}
        <font class="septique">
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

        <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
          {{if $sejour->type == "ambu"}}
					<strong><em>{{$patient}}</em></strong>
          {{else}}
          <strong>{{$patient}}</strong>
          {{/if}}
        </span>

        {{if (!$sejour->entree_reelle) || ($aff_prev->_id && $aff_prev->effectue == 0)}}
          {{$curr_affectation->entree|date_format:"%d/%m %Hh%M"}}
        {{/if}}
        </font>
      {{else}}
        <strong><em>[LIT BLOQUE]</em></strong>
      {{/if}}
      </td>
      <td class="action" style="background:#{{$sejour->_ref_praticien->_ref_function->color}}" 
  	    onmouseover="ObjectTooltip.createTimeHospi(this, '{{$sejour->praticien_id}}', '{{$sejour->_codes_ccam_operations}}' );">
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
        <strong>Du</strong>:
        {{$curr_affectation->entree|date_format:"%a %d %b %Hh%M"}}
        ({{$curr_affectation->_entree_relative}}j)
      </td>
      <td class="action">
        {{if $can->edit}}
        <form name="entreeAffectation{{$curr_affectation->_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="dosql" value="do_affectation_aed" />
          <input type="hidden" name="affectation_id" value="{{$curr_affectation->_id}}" />
          <input type="hidden" name="entree" class="dateTime notNull" value="{{$curr_affectation->entree}}" onchange="this.form.submit()" />
        </form>
        {{/if}}
      </td>
    </tr>
    <tr class="dates">
      <td class="text">
        <strong>Au</strong>:
        {{$curr_affectation->sortie|date_format:"%a %d %b %Hh%M"}}
        ({{$curr_affectation->_sortie_relative}}j)
      </td>
      <td class="action">
        {{if $can->edit && (!$sejour->sortie_reelle || $aff_next->_id)}}
        <form name="sortieAffectation{{$curr_affectation->_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="dosql" value="do_affectation_aed" />
          <input type="hidden" name="affectation_id" value="{{$curr_affectation->_id}}" />
          <input type="hidden" name="sortie" class="dateTime notNull" value="{{$curr_affectation->sortie}}" onchange="this.form.submit()" />
        </form>
        {{/if}}
      </td>
    </tr>

    {{if $curr_affectation->rques}}
    <tr class="dates">
      <td class="text highlight" colspan="2">
        <stong>Remarques:</stong> {{$curr_affectation->rques|nl2br}}
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
        <a style="float: right;" href="#" onclick="confirmDeletion(document.rmvAffectation{{$curr_affectation->_id}},{typeName:'l\'affectation',objName:'{{$patient->_view|smarty:nodefaults|JSAttribute}}'})">
          <img src="images/icons/trash.png" alt="trash" title="Supprimer l'affectation" />
        </a>
        {{/if}}
        {{if $aff_prev->_id}}
          <strong>Déplacé</strong> (chambre: {{$aff_prev->_ref_lit->_ref_chambre->nom}}):
        {{else}}
          <strong>Entrée</strong>:
        {{/if}}
        {{$curr_affectation->entree|date_format:"%a %d %b %Hh%M"}}
        ({{$curr_affectation->_entree_relative}}j)
      </td>
      <td class="action">
        {{if $can->edit && (!$sejour->entree_reelle || $aff_prev->_id)}}
        <form name="entreeAffectation{{$curr_affectation->_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="dosql" value="do_affectation_aed" />
          <input type="hidden" name="affectation_id" value="{{$curr_affectation->_id}}" />
          <input type="hidden" name="entree" class="dateTime notNull" value="{{$curr_affectation->entree}}" onchange="this.form.submit()" />
        </form>
        {{/if}}
      </td>
    </tr>
    <tr class="dates">
      <td class="text">
        {{if $aff_next->_id}}
        <strong>Déplacé</strong> (chambre: {{$aff_next->_ref_lit->_ref_chambre->nom}}):
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
          <span style="float: right;">
            <input type="hidden" name="_date_split" class="dateTime notNull" value="{{$curr_affectation->sortie}}" onchange="submitAffectationSplit(this.form)" />
          </span>
        </form>
          {{/if}}
        <strong>Sortie</strong>:
        {{/if}}

        {{$curr_affectation->sortie|date_format:"%A %d %b %Hh%M"}}
        ({{$curr_affectation->_sortie_relative}}j)
      </td>
      <td class="action">
        {{if $can->edit && (!$sejour->sortie_reelle || $aff_next->_id)}}
        <form name="sortieAffectation{{$curr_affectation->_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="dosql" value="do_affectation_aed" />
          <input type="hidden" name="affectation_id" value="{{$curr_affectation->_id}}" />
          <input type="hidden" name="sortie" class="dateTime notNull" value="{{$curr_affectation->sortie}}" onchange="this.form.submit()" />
        </form>
        {{/if}}
      </td>
    </tr>

    <tr class="dates">
      <td colspan="2"><strong>Age</strong>: {{$patient->_age}} ans ({{mb_value object=$patient field=naissance}})</td>
    </tr>
    <tr class="dates">
      <td class="text" colspan="2">
	      <strong>
	        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$sejour->_ref_praticien}}
	      </strong>
      </td>
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
        {{foreach from=$sejour->_ref_operations item=_operation}}
          {{mb_include module=dPplanningOp template=inc_vw_operation operation=$_operation}}
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
          <strong>Pathologie</strong>:
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
        <strong>Séjour</strong>: {{$sejour->rques|nl2br}}
      </td>
    </tr>
    {{/if}}
    {{foreach from=$sejour->_ref_operations item=curr_operation}}
    {{if $curr_operation->rques != ""}}
    <tr class="dates">
      <td class="text highlight" colspan="2">
        <strong>Intervention</strong>: {{$curr_operation->rques|nl2br}}
      </td>
    </tr>
    {{/if}}
    {{/foreach}}
    {{if $patient->rques != ""}}
    <tr class="dates">
      <td class="text highlight" colspan="2">
        <strong>Patient</strong>: {{$patient->rques|nl2br}}
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
  </table>
  {{foreachelse}}
  <table class="tbl">
    <tr class="litdispo">
      <td colspan="2">Lit disponible</td>
    </tr>
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
  </table>
  {{/foreach}}
  </td>
</tr>