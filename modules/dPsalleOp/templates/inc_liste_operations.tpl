{{** 
  * @param $urgence bool Urgence mode
  * @param $vueReduite bool Affichage compact
  * @param $operations array|COperation interventions à afficher
  *}}

<!-- Entêtes -->
<tr>
  {{if $urgence && $salle}}
  <th>Praticien</th>
  {{else}}
  <th>Heure</th>
  {{/if}}
  <th>Patient</th>
  <th>Interv</th>
  <th>Coté</th>
  {{if !$vueReduite}}
  <th>Durée</th>
  {{/if}}
</tr>

{{assign var=systeme_materiel value=$conf.dPbloc.CPlageOp.systeme_materiel}}

{{assign var=save_op value=$operations|@reset}}

{{foreach from=$operations item=_operation name=ops}}
{{if $conf.dPsalleOp.COperation.modif_salle}}
  {{assign var="rowspan" value=2}}
{{else}}
  {{assign var="rowspan" value=1}}
{{/if}}
{{if !$_operation->annulee}}

{{if $smarty.foreach.ops.index > 0}}
  {{assign var=preop_curr value=$_operation->presence_preop}}
  {{assign var=timeop_curr value=$_operation->time_operation}}
  {{assign var=postop_save value=$save_op->presence_postop}}
  {{assign var=timeop_save value=$save_op->time_operation}}
  {{assign var=tempop_save value=$save_op->temp_operation}}
  
  
  {{assign var=intervalle_a value=$postop_save|mbAddTime:$tempop_save|mbAddTime:$timeop_save}}
  {{assign var=intervalle_b value=$preop_curr|mbSubTime:$timeop_curr}}
  {{if $intervalle_a < $intervalle_b}}
    <tr>
      <th colspan="4" class="section">
        [PAUSE] ({{$intervalle_a|mbTimeRelative:$intervalle_b:"%02dh%02d"}})
      </th>
    </tr>
  {{/if}}
  {{assign var=save_op value=$_operation}}
{{/if}}
<tbody class="hoverable">
<tr {{if $_operation->_id == $operation_id}}class="selected"{{/if}}>
  {{if $_operation->_deplacee && $_operation->salle_id != $salle->_id}}
  <td class="text" rowspan="{{$rowspan}}" style="background-color:#ccf">
  {{elseif $_operation->entree_salle && $_operation->sortie_salle}}
  <td class="text" rowspan="{{$rowspan}}" style="background-image:url(images/icons/ray.gif); background-repeat:repeat;">
  {{elseif $_operation->entree_salle}}
  <td class="text" rowspan="{{$rowspan}}" style="background-color:#cfc">
  {{elseif $_operation->sortie_salle}}
  <td class="text" rowspan="{{$rowspan}}" style="background-color:#fcc">
  {{elseif $_operation->entree_bloc}}
  <td class="text" rowspan="{{$rowspan}}" style="background-color:#ffa">
  {{else}}
  <td class="text" rowspan="{{$rowspan}}">
  {{/if}}
    <a href="?m=dPsalleOp&amp;tab=vw_operations&amp;op={{$_operation->_id}}" title="Coder l'intervention">
      {{if ($urgence && $salle) || $_operation->_ref_plageop->spec_id}}
        {{$_operation->_ref_chir->_view}} -
      {{if $_operation->_ref_anesth->_id && $vueReduite}}
        {{$_operation->_ref_anesth->_view}} -
      {{/if}}
      {{/if}}
      {{if $_operation->time_operation != "00:00:00"}}
        {{$_operation->time_operation|date_format:$conf.time}}
      {{else}}
        NP
      {{/if}}
      {{if $vueReduite && $urgence && $salle}}
        - {{$_operation->temp_operation|date_format:$conf.time}}
        
        {{if $_operation->presence_preop}}
          <div>
            Pré-op : {{$_operation->presence_preop|date_format:$conf.time}}
          </div>
        {{/if}}
        {{if $_operation->presence_postop}}
          <div>
            Post-op : {{$_operation->presence_postop|date_format:$conf.time}}
          </div>
        {{/if}}
      {{/if}}
    </a>
    {{if $vueReduite && $systeme_materiel == "expert"}}
      <button class="print notext not-printable" onclick="printFicheBloc({{$_operation->_id}})">{{tr}}Print{{/tr}}</button>
      {{if $urgence && $salle && $_operation->_ref_besoins|@count}}
        
        <img src="style/mediboard/images/icons/equipement.png" onmouseover="ObjectTooltip.createDOM(this, 'besoins_{{$_operation->_id}}')"/>
        <div id="besoins_{{$_operation->_id}}" style="display: none;">
          {{tr}}CBesoinRessource.all{{/tr}} :
          <ul>
            {{foreach from=$_operation->_ref_besoins item=_besoin}}
             <li>
               {{$_besoin->_ref_type_ressource}}
             </li>
            {{/foreach}}
          </ul>
        </div>
      {{/if}}
    {{/if}}
  </td>
 
  {{if $_operation->_deplacee && $_operation->salle_id != $salle->_id}}
  <td class="text" colspan="5">
    <div class="warning">
      <span class="{{if !$_operation->_ref_sejour->entree_reelle}}patient-not-arrived{{/if}} {{if $_operation->_ref_sejour->septique}}septique{{/if}}"
            onmouseover="ObjectTooltip.createEx(this, '{{$_operation->_ref_sejour->_ref_patient->_guid}}')">
        {{$_operation->_ref_patient->_view}}
        {{if $vueReduite}}
          ({{$_operation->_ref_patient->_age}})
        {{/if}}
      </span>
      <br />
      Intervention déplacée vers {{$_operation->_ref_salle->_view}}
    </div>
  </td>
  
  {{else}}
  <td class="text">
    <a href="?m=dPsalleOp&amp;tab=vw_operations&amp;salle={{$salle->_id}}&amp;op={{$_operation->_id}}">
    <span class="{{if !$_operation->_ref_sejour->entree_reelle}}patient-not-arrived{{/if}} {{if $_operation->_ref_sejour->septique}}septique{{/if}}"
          onmouseover="ObjectTooltip.createEx(this, '{{$_operation->_ref_sejour->_ref_patient->_guid}}')">
      {{$_operation->_ref_patient->_view}}
      {{if $vueReduite}}
        ({{$_operation->_ref_patient->_age}})
      {{/if}}
    </span>
    {{if $_operation->_ref_affectation && $_operation->_ref_affectation->_ref_lit->_id && $conf.dPbloc.CPlageOp.chambre_operation == 1}}
      <div style="text-align: center; font-size: 0.8em; width: 1%; white-space: nowrap; border: none;">
        {{$_operation->_ref_affectation->_ref_lit->_ref_chambre->_ref_service->_view}}<br />
        {{$_operation->_ref_affectation->_ref_lit->_view}}
      </div>
    {{/if}}
    </a>
  </td>
  
  <td class="text">
    {{mb_ternary var=direction test=$urgence value=vw_edit_urgence other=vw_edit_planning}}
    <a href="?m=dPsalleOp&amp;tab=vw_operations&amp;salle={{$salle->_id}}&amp;op={{$_operation->_id}}" {{if $_operation->_count_actes == "0"}}style="border-color: #F99" class="mediuser"{{/if}}>
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_operation->_guid}}')" >
      {{if $_operation->libelle}}
        {{$_operation->libelle}}
      {{else}}
        {{foreach from=$_operation->_ext_codes_ccam item=curr_code}}
          {{$curr_code->code}}
        {{/foreach}}
      {{/if}}
      {{if $_operation->_ref_sejour->type == "comp" && $vueReduite}}
        ({{$_operation->_ref_sejour->type|truncate:1:""|capitalize}} {{$_operation->_ref_sejour->_duree}}j)
      {{else}}
         ({{$_operation->_ref_sejour->type|truncate:1:""|capitalize}})
      {{/if}}
      </span>
    </a>
  </td>
  <td>
    {{if $conf.dPplanningOp.COperation.verif_cote && ($_operation->cote == "droit" || $_operation->cote == "gauche") && !$vueReduite}}
      <form name="editCoteOp{{$_operation->_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        {{mb_key object=$_operation}}
        {{mb_field emptyLabel="COperation-cote_bloc" object=$_operation field="cote_bloc" onchange="this.form.submit();"}}
      </form>
    {{else}}
    {{mb_value object=$_operation field="cote"}}
  {{/if}}
  </td>
  {{if !$vueReduite}}
  <td>{{$_operation->temp_operation|date_format:$conf.time}}</td>
  {{/if}}
  {{/if}}
</tr>

{{if $conf.dPsalleOp.COperation.modif_salle && !($_operation->_deplacee && $_operation->salle_id != $salle->_id) && @$allow_moves|default:1}}
<tr {{if $_operation->_id == $operation_id}}class="selected"{{/if}}>
  <td colspan="5" class="not-printable">
    <form name="changeSalle{{$_operation->_id}}" action="?m={{$m}}" method="post">
    <input type="hidden" name="dosql" value="do_planning_aed" />
    <input type="hidden" name="m" value="dPplanningOp" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="operation_id" value="{{$_operation->_id}}" />
    <select name="salle_id" onchange="this.form.submit();">

      {{foreach from=$listBlocs item=curr_bloc}}
      <optgroup label="{{$curr_bloc->nom}}">
        {{foreach from=$curr_bloc->_ref_salles item=curr_salle}}
        <option value="{{$curr_salle->_id}}" {{if $curr_salle->_id == $_operation->salle_id}}selected="selected"{{/if}}>
          {{$curr_salle->nom}}
        </option>
        {{foreachelse}}
        <option value="" disabled="disabled">{{tr}}CSalle.none{{/tr}}</option>
        {{/foreach}}
      </optgroup>
      {{/foreach}}
    </select>
    </form>
  </td>
</tr>
{{/if}}
</tbody>
{{/if}}
{{/foreach}}
