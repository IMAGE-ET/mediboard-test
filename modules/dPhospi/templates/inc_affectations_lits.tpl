<tr {{if !$conf.dPhospi.CLit.show_in_tableau}}class="lit"{{/if}} id="lit-{{$curr_lit->_id}}">
  <td>
    {{if $curr_lit->_overbooking}}
      <img src="images/icons/warning.png" alt="warning" title="Over-booking: {{$curr_lit->_overbooking}} collisions" />
    {{/if}}
    {{$curr_lit->_shortview}}
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
      <form name="addAffectationaffectation_{{$curr_affectation->_id}}" action="?m={{$m}}" method="post" class="prepared">
        <input type="hidden" name="m" value="dPhospi" />
        <input type="hidden" name="dosql" value="do_affectation_aed" />
        <input type="hidden" name="affectation_id" value="{{$curr_affectation->_id}}" />
        <input type="hidden" name="lit_id" value="" />
        <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
        <input type="hidden" name="entree" value="{{$curr_affectation->entree}}" />
        <input type="hidden" name="sortie" value="{{$curr_affectation->sortie}}" />
      </form>
  
      <table class="tbl" id="affectation_{{$curr_affectation->_id}}">
        <tr class="patient">
          {{if $curr_affectation->sejour_id}}
          <td class="text button" style="width: 1%;">
            {{if $can->edit}}
            <script>new Draggable('affectation_{{$curr_affectation->_id}}', {revert:true})</script>
            {{/if}}
            <!--
            <a href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$sejour->_id}}">
              <img src="images/icons/planning.png" title="Modifier le séjour">
            </a>
            -->
            {{if $sejour->_couvert_cmu}}
            <div><strong>CMU</strong></div>
            {{/if}}
            {{if $sejour->_couvert_ald}}
            <div><strong {{if $sejour->ald}}style="color: red;"{{/if}}>ALD</strong></div>
            {{/if}}
            {{if $conf.dPhospi.CLit.alt_icons_sortants}}
              {{assign var=suffixe_icons value="2"}}
            {{else}}
              {{assign var=suffixe_icons value=""}}
            {{/if}}
            {{if $sejour->type == "ambu"}}
            <img src="modules/dPhospi/images/X{{$suffixe_icons}}.png" alt="X" title="Ambulatoire" />
            {{elseif $curr_affectation->sortie|iso_date == $demain}}
              {{if $aff_next->_id}}
            <img src="modules/dPhospi/images/OC{{$suffixe_icons}}.png" alt="OC" title="Déplacé demain" />
              {{else}}
            <img src="modules/dPhospi/images/O{{$suffixe_icons}}.png" alt="O" title="Sortant demain" />
              {{/if}}
            {{elseif $curr_affectation->sortie|iso_date == $date}}
              {{if $aff_next->_id}}
            <img src="modules/dPhospi/images/OoC{{$suffixe_icons}}.png" alt="OoC" title="Déplacé aujourd'hui" />
              {{else}}
            <img src="modules/dPhospi/images/Oo{{$suffixe_icons}}.png" alt="Oo" title="Sortant aujourd'hui" />
              {{/if}}
            {{/if}}
          </td>
          {{if $sejour->confirme}}
            <td class="text" style="background-image:url(images/icons/ray.gif); background-repeat:repeat;">
          {{else}}
            <td class="text">
          {{/if}}
          {{if !$sejour->entree_reelle || ($aff_prev->_id && $aff_prev->effectue == 0)}}
            <span class="patient-not-arrived">
          {{elseif $sejour->septique}}
            <span class="septique">
          {{else}}
            <span>
          {{/if}}
          {{if "dPImeds"|module_active && "dPhospi vue_tableau show_labo_results"|conf:"CGroups-$g"}}
            {{mb_include module=Imeds template=inc_sejour_labo link="#1"}}
            <script>
              ImedsResultsWatcher.addSejour('{{$sejour->_id}}', '{{$sejour->_NDA}}');
            </script>
          {{/if}}

          <span style="float: right;">
            {{if $prestation_id && $sejour->_liaisons_for_prestation|@count}}
              {{mb_include module=hospi template=inc_vw_liaisons_prestation liaisons=$sejour->_liaisons_for_prestation}}
            {{/if}}
            {{mb_include module=patients template=inc_vw_antecedents type=deficience readonly=1 handicap=$sejour->handicap}}
          </span>

          <span {{if $app->touch_device}}onclick{{else}}onmouseover{{/if}}="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')" {{if $sejour->recuse == "-1"}}class="opacity-70"{{/if}}>
            <strong {{if $sejour->type == "ambu"}}style="font-style: italic;"{{/if}}>
              {{if $sejour->recuse == "-1"}}[Att] {{/if}}{{$patient}}
            </strong>
          </span>

          {{if (!$sejour->entree_reelle) || ($aff_prev->_id && $aff_prev->effectue == 0)}}
            {{$curr_affectation->entree|date_format:"%d/%m %Hh%M"}}
          {{/if}}
          </span>
          </td>
          <td class="action" style="background:#{{$sejour->_ref_praticien->_ref_function->color}}"
                onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_ref_praticien->_guid}}');">
            {{$sejour->_ref_praticien->_shortview}}
          </td>
          {{else}}
          <td colspan="2">
            <strong><em>[LIT BLOQUE]</em></strong>
          </td>
          {{/if}}
    </tr>
    {{if !$curr_affectation->sejour_id}}
      <tr class="dates">
        <td class="text" colspan="2">
          {{if $can->edit}}
          <form name="entreeAffectation{{$curr_affectation->_id}}" action="?m={{$m}}" method="post" style="float: right;" class="prepared">
            <input type="hidden" name="m" value="dPhospi" />
            <input type="hidden" name="dosql" value="do_affectation_aed" />
            <input type="hidden" name="affectation_id" value="{{$curr_affectation->_id}}" />
            <input type="hidden" name="entree" class="dateTime notNull" value="{{$curr_affectation->entree}}" onchange="this.form.submit()" />
          </form>
          {{/if}}
          <strong>Du</strong>:
          {{$curr_affectation->entree|date_format:"%a %d %b %Hh%M"}}
          ({{$curr_affectation->_entree_relative}}j)
        </td>
        <td class="action">
          {{if $can->edit}}
          <form name="rmvAffectation{{$curr_affectation->_id}}" action="?m={{$m}}" method="post" class="prepared">
            <input type="hidden" name="m" value="dPhospi" />
            <input type="hidden" name="dosql" value="do_affectation_aed" />
            <input type="hidden" name="del" value="1" />
            <input type="hidden" name="affectation_id" value="{{$curr_affectation->_id}}" />
          </form>
          <a href="#" onclick="confirmDeletion(document.rmvAffectation{{$curr_affectation->_id}},{typeName:'l\'affectation',objName:'{{$patient->_view|smarty:nodefaults|JSAttribute}}'})">
            <img src="images/icons/trash.png" alt="trash" title="Supprimer l'affectation" />
          </a>
          {{/if}}
        </td>
      </tr>
      <tr class="dates">
        <td class="text" colspan="2">
          {{if $can->edit && (!$sejour->sortie_reelle || $aff_next->_id)}}
          <form name="sortieAffectation{{$curr_affectation->_id}}" action="?m={{$m}}" method="post" style="float: right;" class="prepared">
            <input type="hidden" name="m" value="dPhospi" />
            <input type="hidden" name="dosql" value="do_affectation_aed" />
            <input type="hidden" name="affectation_id" value="{{$curr_affectation->_id}}" />
            <input type="hidden" name="sortie" class="dateTime notNull" value="{{$curr_affectation->sortie}}" onchange="this.form.submit()" />
          </form>
          {{/if}}
          <strong>Au</strong>:
          {{$curr_affectation->sortie|date_format:"%a %d %b %Hh%M"}}
          ({{$curr_affectation->_sortie_relative}}j)
        </td>
        <td class="action">
        </td>
      </tr>

      {{if $curr_affectation->rques}}
      <tr class="dates">
        <td class="text highlight" colspan="3">
          <strong>Remarques:</strong> {{$curr_affectation->rques|nl2br}}
        </td>
      </tr>
    {{/if}}
    {{else}}

    <tr class="dates">
      <td class="text" colspan="2">
        {{if $can->edit && (!$sejour->entree_reelle || $aff_prev->_id)}}
        <form name="entreeAffectation{{$curr_affectation->_id}}" action="?m={{$m}}" method="post" style="float: right;" class="prepared">
          <input type="hidden" name="m" value="dPhospi" />
          <input type="hidden" name="dosql" value="do_affectation_aed" />
          <input type="hidden" name="affectation_id" value="{{$curr_affectation->_id}}" />
          <input type="hidden" name="entree" class="dateTime notNull" value="{{$curr_affectation->entree}}" onchange="return onSubmitFormAjax(this.form, {onComplete: reloadTableau});" />
        </form>
        {{/if}}
        
        {{if $curr_service->externe}}
          <strong>Départ</strong>
          {{if $aff_prev->_id}}
            ({{$aff_prev->_ref_lit->_ref_chambre->_shortview}})
          {{/if}}
        {{elseif $aff_prev->_id}}
          <strong>Déplacé</strong> ({{if $aff_prev->lit_id}}{{$aff_prev->_ref_lit->_ref_chambre->_shortview}}{{else}}{{$aff_prev->_ref_service}}{{/if}})
        {{else}}
          <strong>Entrée</strong>
        {{/if}}
        :
        {{$curr_affectation->entree|date_format:"%a %d %b %Hh%M"}}
        ({{$curr_affectation->_entree_relative}}j)
      </td>
      <td class="action">
        {{if $can->edit}}
        <form name="rmvAffectation{{$curr_affectation->_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="dPhospi" />
          <input type="hidden" name="dosql" value="do_affectation_aed" />
          <input type="hidden" name="del" value="1" />
          <input type="hidden" name="affectation_id" value="{{$curr_affectation->_id}}" />
        </form>
        <a href="#" onclick="confirmDeletion(document.rmvAffectation{{$curr_affectation->_id}},{typeName:'l\'affectation',objName:'{{$patient->_view|smarty:nodefaults|JSAttribute}}'})">
          <img src="images/icons/trash.png" alt="trash" title="Supprimer l'affectation" />
        </a>
        {{/if}}
      </td>
    </tr>
    <tr class="dates">
      <td class="text" colspan="2">
        {{if $can->edit && (!$sejour->sortie_reelle || $aff_next->_id)}}
        <form name="sortieAffectation{{$curr_affectation->_id}}" action="?m={{$m}}" method="post" style="float: right;" class="prepared">
          <input type="hidden" name="m" value="dPhospi" />
          <input type="hidden" name="dosql" value="do_affectation_aed" />
          <input type="hidden" name="affectation_id" value="{{$curr_affectation->_id}}" />
          <input type="hidden" name="sortie" class="dateTime notNull" value="{{$curr_affectation->sortie}}" onchange="return onSubmitFormAjax(this.form, {onComplete: reloadTableau});" />
        </form>
        {{/if}}
        
        {{if $curr_service->externe}}
          <strong>Retour</strong>
          {{if $aff_next->_id}}
            ({{$aff_next->_ref_lit->_ref_chambre->_shortview}})
          {{/if}}
        {{elseif $aff_next->_id}}
        <strong>Déplacé</strong> ({{if $aff_next->lit_id}}{{$aff_next->_ref_lit->_ref_chambre->_shortview}}{{else}}{{$aff_next->_ref_service}}{{/if}})
        {{else}}
        <strong>Sortie</strong>
        {{/if}}
        :
        {{$curr_affectation->sortie|date_format:"%a %d %b %Hh%M"}}
        ({{$curr_affectation->_sortie_relative}}j)
      </td>
      <td class="action">
        {{if $can->edit && !$aff_next->_id}}
        <form name="splitAffectation{{$curr_affectation->_id}}" action="?m={{$m}}" method="post" class="prepared">
          <input type="hidden" name="m" value="dPhospi" />
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
      </td>
    </tr>

    <tr class="dates">
      <td colspan="3"><strong>Age</strong>: {{$patient->_age}} ({{mb_value object=$patient field=naissance}})
        {{if $conf.dPhospi.show_uf}}
          <a style="float: right;" href="#1" title=""
            onclick="AffectationUf.affecter('{{$curr_affectation->_guid}}','{{$curr_lit->_guid}}')"  >
            <img src="images/icons/uf.png" width="16" height="16" title="Affecter les UF"/>
          </a>
        {{/if}}
      </td>
    </tr>

    {{if $sejour->prestation_id}}
    <tr class="dates">
      <td colspan="3">
        <strong>Prestation:</strong> {{$sejour->_ref_prestation->_view}}
      </td>
    </tr>
    {{/if}}
    
    <tr class="dates">
      <td class="text" colspan="3">
        <strong>
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$sejour->_ref_praticien}}
        </strong>
      </td>
    </tr>

    {{if $sejour->libelle}}
    <tr class="dates">
      <td class="text" colspan="3">
        {{$sejour->libelle}}
      </td>
    </tr>
    {{/if}}

    <tr class="dates">
      <td class="text" colspan="3">
        {{foreach from=$sejour->_ref_operations item=_operation}}
          {{mb_include module=planningOp template=inc_vw_operation operation=$_operation}}
        {{/foreach}}
      </td>
    </tr>
    <tr class="dates">
      <td class="text" colspan="3">
        <form name="SeptieSejour{{$sejour->_id}}" action="?m=dPhospi" method="post" class="prepared">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="postRedirect" value="m=dPhospi" />
          <input type="hidden" name="dosql" value="do_sejour_aed" />
          <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
          <strong>Pathologie</strong>:
          {{$sejour->pathologie}}
          -
          {{if $can->edit}}
          <label title="Séjour propre">
            <input type="radio" name="septique" value="0" {{if $sejour->septique == 0}} checked="checked" {{/if}} onclick="this.form.submit()" />
            Propre
          </label>
          <label title="Séjour septique">
            <input type="radio" name="septique" value="1" {{if $sejour->septique == 1}} checked="checked" {{/if}} onclick="this.form.submit()" />
            Septique
          </label>
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
      <td class="text highlight" colspan="3">
        <strong>Séjour</strong>: {{$sejour->rques|nl2br}}
      </td>
    </tr>
    {{/if}}
    {{foreach from=$sejour->_ref_operations item=curr_operation}}
    {{if $curr_operation->rques != ""}}
    <tr class="dates">
      <td class="text highlight" colspan="3">
        <strong>Intervention</strong>: {{$curr_operation->rques|nl2br}}
      </td>
    </tr>
    {{/if}}
    {{/foreach}}
    {{if $patient->rques != ""}}
    <tr class="dates">
      <td class="text highlight" colspan="3">
        <strong>Patient</strong>: {{$patient->rques|nl2br}}
      </td>
    </tr>
    {{/if}}
    <tr class="dates">
      <td class="text" colspan="3">
        {{mb_include module=admissions template=inc_form_prestations edit=$can->edit}}
      </td>
    </tr>
    {{/if}}
  </table>
  {{foreachelse}}
  {{if $curr_service->externe}}
  <table class="tbl">
    <tr class="litdispo">
      <td colspan="2">Aucun patient</td>
    </tr>
  </table>
  {{else}}
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
  {{/if}}
  {{/foreach}}
  </td>
</tr>