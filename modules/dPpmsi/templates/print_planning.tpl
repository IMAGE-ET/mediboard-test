<!-- $Id$ -->

<table class="main">
  <tr>
    <th>
      <a href="#" onclick="window.print()">
        Planning du {{$filter->_date_min|date_format:"%d/%m/%Y"}}
        {{if $filter->_date_min != $filter->_date_max}}
        au {{$filter->_date_max|date_format:"%d/%m/%Y"}}
        {{/if}}
      </a>
    </th>
  </tr>
  {{foreach from=$plagesop item=curr_plageop}}
  <tr>
    <td class="text">
      {{if $curr_plageop->_id}}
      <strong>Dr {{$curr_plageop->_ref_chir->_view}}</strong> -
      <strong>{{$curr_plageop->_ref_salle->nom}}</strong>
      de {{$curr_plageop->debut|date_format:$conf.time}} à {{$curr_plageop->fin|date_format:$conf.time}}
      le {{$curr_plageop->date|date_format:"%d/%m/%Y"}}
    
      {{if $curr_plageop->anesth_id}}
        - Anesthesiste : <strong>Dr {{$curr_plageop->_ref_anesth->_view}}</strong>
      {{/if}}
      
      {{else}}
      <strong>Liste des urgences hors plage</strong>
      {{/if}}
    </td>
  </tr>
  <tr>
    <td>
    </td>
  </tr>
      
  <tr>
    <td>
      <table class="tbl">
        <tr>
          {{if !$curr_plageop->_id}}
          <th class="title" colspan="2">Urgence</th>
          {{/if}}
          <th class="title" colspan="2">Patient</th>
          <th class="title" colspan="4">Sejour</th>
          <th class="title" colspan="4">Intervention</th>
        </tr>
        <tr>
          {{if !$curr_plageop->_id}}
          <!-- Cas des urgences -->
          <th>Date</th>
          <th>Praticien</th>
          {{/if}}
          
          <!-- Patient -->
          <th>Nom - Prénom</th>
          <th>Naissance</th>

          <!-- Sejour -->
          <th>Entree</th>
          <th>Sortie</th>
          <th>Chambre</th>
          <th>DP</th>
          
          <!-- Intervention -->
          <th>Heure</th>
          <th>Libellé</th>
          <th>Codes prévus</th>
          <th>Codage au bloc</th>
        </tr>

        {{foreach from=$curr_plageop->_ref_operations item=curr_op}}
        {{assign var=sejour value=$curr_op->_ref_sejour}}
        {{assign var=patient value=$sejour->_ref_patient}}
        <tr>
          {{if !$curr_plageop->_id}}
          <!-- Cas des urgences -->
          <td>{{$curr_op->date|date_format:"%d/%m/%Y"}}</td>
          <td class="text">Dr {{$curr_op->_ref_chir->_view}}</td>
          {{/if}}
          <!-- Patient -->
          <td class="text">
            <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">
              {{$patient->_view}}
            </span>
          </td>
          <td>
            {{$patient->naissance|date_format:"%d/%m/%Y"}}
          </td>
      
          <!-- Sejour -->
          <td class="text">
            <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}');">
              {{mb_value object=$sejour field=entree}}
            </span>
          </td>
          <td class="text">
            {{mb_value object=$sejour field=sortie}}
          </td>
          <td class="text">
            {{mb_include module=hospi template=inc_placement_sejour sejour=$sejour}}
          </td>
          <td>
            {{$sejour->DP}}
          </td>

          <!-- Intervention -->
          <td>
          {{if $curr_op->rank}}
            {{$curr_op->time_operation|date_format:$conf.time}}
          {{elseif $curr_op->date}}
          URGENCE
          {{else}}
            NP
          {{/if}}
          </td>
          <td class="text">
            <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_op->_guid}}');">
              {{$curr_op->libelle}}
            </span>
          </td>
          <td>
            <ul>
            {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
              <li>{{$curr_code->code}}</li>
            {{/foreach}}
            </ul>
          </td>
          <td class="text">
            <ul>
            {{foreach from=$curr_op->_ref_actes_ccam item=curr_acte}}
              <li>
                Dr {{$curr_acte->_ref_executant->_view}} : {{$curr_acte->code_acte}}
                <br />
                Act. : {{$curr_acte->code_activite}}
                &mdash; Phase : {{$curr_acte->code_phase}}
                &mdash; Code asso : 
                {{if $curr_acte->code_association}}
                  {{$curr_acte->code_association}}
                {{else}}
                  aucun
                {{/if}}
                {{if $curr_acte->modificateurs}}
                  &mdash; Modifs : {{$curr_acte->modificateurs}}
                {{/if}}
                {{if $curr_acte->montant_depassement}}
                  &mdash; DH : {{$curr_acte->montant_depassement|currency}} 
                {{/if}}
                {{if $curr_acte->commentaire}}
                  <br />
                  Rques : {{$curr_acte->commentaire|nl2br}}
                {{/if}}
              </li>
            {{/foreach}}
            </ul>
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
  {{/foreach}}
</table>