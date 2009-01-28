<table class="form">

  {{if !$app->user_prefs.simpleCabinet}}
  <!-- Séjour et interventions -->
  <tr id="sejours-trigger">
    <td>Séjours ({{$patient->_ref_sejours|@count}})</td>
  </tr>
  
  <tbody id="sejours">
    <tr class="script">
    	<td>
    		<script type="text/javascript">
    		new PairEffect("sejours");
    		</script>
    	</td>
    </tr>
    {{foreach from=$patient->_ref_sejours item=_sejour}}
    <tr>
      <td>
        <strong>Dr {{$_sejour->_ref_praticien->_view}}</strong>
        du {{$_sejour->entree_prevue|date_format:$dPconfig.date}}
        au {{$_sejour->sortie_prevue|date_format:$dPconfig.date}}
        <ul>
        {{foreach from=$_sejour->_ref_operations item=_op}}
          <li>
            <a href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={{$_op->_id}}"
              onmouseover="ObjectTooltip.createEx(this, '{{$_op->_guid}}')">
              <strong>Dr {{$_op->_ref_chir->_view}}</strong>
              le {{$_op->_datetime|date_format:$dPconfig.date}}
            </a>
          </li>
        {{foreachelse}}
          <li>{{tr}}COperation.none{{/tr}}</li>
        {{/foreach}}
        </ul>
      </td>
    </tr>
    {{foreachelse}}
    <tr>
      <td><em>{{tr}}CSejour.none{{/tr}}</em></td>
    </tr>
    {{/foreach}}
  </tbody>
  {{/if}}
  
  <!-- Consultations -->
  <tr id="consultations-trigger">
    <td>Consultations ({{$patient->_ref_consultations|@count}})</td>
  </tr>
  
  <tbody id="consultations">
    <tr class="script">
    	<td>
    		<script type="text/javascript">
    		new PairEffect("consultations");
    		</script>
    	</td>
    </tr>
    
    {{foreach from=$patient->_ref_consultations item=_consult}}
    <tr>
      <td>
        <a href="?m=dPcabinet&amp;tab=edit_consultation&amp;selConsult={{$_consult->_id}}"
          onmouseover="ObjectTooltip.createEx(this, '{{$_consult->_guid}}')">
          <strong>Dr {{$_consult->_ref_plageconsult->_ref_chir->_view}}</strong>
          le {{$_consult->_ref_plageconsult->date|date_format:$dPconfig.date}}
        </a>
      </td>
    </tr>
    {{foreachelse}}
    
    <tr>
      <td><em>{{tr}}CConsultation.none{{/tr}}</em></td>
    </tr>
    {{/foreach}}
  </tbody>
</table>
