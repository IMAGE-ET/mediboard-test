<table class="form">
  {{if !$app->user_prefs.simpleCabinet}}
  <tr id="sejours-trigger">
    <td>Séjours ({{$patient->_ref_sejours|@count}})</td>
  </tr>
  <tbody id="sejours">
    <tr class="script"><td><script type="text/javascript">new PairEffect("sejours");</script></td></tr>
    {{foreach from=$patient->_ref_sejours item=curr_sejour}}
    <tr>
      <td>
        <strong>Dr. {{$curr_sejour->_ref_praticien->_view}}</strong>
        Du {{$curr_sejour->entree_prevue|date_format:"%d %b %Y"}}
        au {{$curr_sejour->sortie_prevue|date_format:"%d %b %Y"}}
        <ul>
        {{foreach from=$curr_sejour->_ref_operations item=curr_op}}
          <li>
            <a href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->_id}}"
              onmouseover="ObjectTooltip.create(this, 'COperation', {{$curr_op->_id}})"
              >
              <strong>Dr. {{$curr_op->_ref_chir->_view}}</strong>
              le {{$curr_op->_ref_plageop->date|date_format:"%d %b %Y"}}
            </a>
          </li>
        {{foreachelse}}
          <li>Pas d'intevention</li>
        {{/foreach}}
        </ul>
      </td>
    </tr>
    {{foreachelse}}
    <tr>
      <td><em>Aucun séjour disponible</em></td>
    </tr>
    {{/foreach}}
  </tbody>
  {{/if}}
  <tr id="consultations-trigger">
    <td>Consultations ({{$patient->_ref_consultations|@count}})</td>
  </tr>
  <tbody id="consultations">
    <tr class="script"><td><script type="text/javascript">new PairEffect("consultations");</script></td></tr>
    {{foreach from=$patient->_ref_consultations item=curr_consult}}
    <tr>
      <td>
        <a href="?m=dPcabinet&amp;tab=edit_consultation&amp;selConsult={{$curr_consult->_id}}"
          onmouseover="ObjectTooltip.create(this, 'CConsultation', {{$curr_consult->_id}})"
        >
          <strong>Dr. {{$curr_consult->_ref_plageconsult->_ref_chir->_view}}</strong>
          {{$curr_consult->_ref_plageconsult->date|date_format:"%d %b %Y"}}
        </a>
      </td>
    </tr>
    {{foreachelse}}
    <tr>
      <td><em>Aucune consultation disponible</em></td>
    </tr>
    {{/foreach}}
  </tbody>
</table>
