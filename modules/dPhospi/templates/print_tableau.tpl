<script type="text/javascript">
Main.add(function () {
  window.print();
});
</script>

<table class="main layout affectations">
  <tr>
    <th colspan="100">Affectations du {{$date|date_format:$conf.longdate}}</th>
  </tr>
  <tr>
  {{foreach from=$services item=_service}}
    {{if $_service->_ref_chambres|@count}}
    <td>
      <table class="tbl">
        <tr>
          <th class="title" colspan="3">{{$_service->nom}}</th>
        </tr>
        {{foreach from=$_service->_ref_chambres item=_chambre}}
        {{if !$_chambre->annule}}
        {{foreach from=$_chambre->_ref_lits item=_lit}}
        {{if !$_lit->_ref_affectations|@count}}
        <tr>
          <th class="opacity-70" colspan="3">
            <span style="float: left">{{$_chambre->_shortview}}</span>
            <span style="float: right">{{$_lit->_shortview}}</span>
          </th>
        </tr>
        {{else}}
        {{foreach from=$_lit->_ref_affectations item=_aff}}
        {{assign var="_sejour" value=$_aff->_ref_sejour}}
        {{assign var="_patient" value=$_sejour->_ref_patient}}
        {{assign var="_aff_prev" value=$_aff->_ref_prev}}
        {{assign var="_aff_next" value=$_aff->_ref_next}}
        <tr>
          <th colspan="3">
            <span style="float: left">{{$_chambre->_shortview}}</span>
            <span style="float: right">{{$_lit->_shortview}}</span>
          </th>
        </tr>
        <tr>
          <td class="text button" style="width: 1%;">
            {{if $_sejour->_couvert_cmu}}
            <div><strong>CMU</strong></div>
            {{/if}}
            {{if $_sejour->_couvert_ald}}
            <div><strong>ALD</strong></div>
            {{/if}}
            {{if $_sejour->type == "ambu"}}
            <img src="modules/dPhospi/images/X.png" alt="X" title="Sortant ce soir" />
            {{elseif $_aff->sortie|iso_date == $demain}}
              {{if $_aff_next->_id}}
            <img src="modules/dPhospi/images/OC.png" alt="OC" title="Sortant demain" />
              {{else}}
            <img src="modules/dPhospi/images/O.png" alt="O" title="Sortant demain" />
              {{/if}}
            {{elseif $_aff->sortie|iso_date == $date}}
              {{if $_aff_next->_id}}
            <img src="modules/dPhospi/images/OoC.png" alt="OoC" title="Sortant aujourd'hui" />
              {{else}}
            <img src="modules/dPhospi/images/Oo.png" alt="Oo" title="Sortant aujourd'hui" />
              {{/if}}
            {{/if}}
          </td>
          <td class="text" {{if $_aff->confirme}}style="background-image:url(images/icons/ray.gif); background-repeat:repeat;"{{/if}}>
            {{if !$_sejour->entree_reelle || ($_aff_prev->_id && $_aff_prev->effectue == 0)}}
              <span class="patient-not-arrived">
            {{elseif $_sejour->septique}}
              <span class="septique">
            {{else}}
              <span>
            {{/if}} 
	            <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}')">
	              {{if $_sejour->type == "ambu"}}
	              <strong><em>{{$_patient}}</em></strong>
	              {{else}}
	              <strong>{{$_patient}}</strong>
	              {{/if}}
	            </span>
            </span>
          </td>
          <td style="width: 1%; background:#{{$_sejour->_ref_praticien->_ref_function->color}}">
            {{$_sejour->_ref_praticien->_shortview}}
          </td>
        </tr>
        {{/foreach}}
        {{/if}}
        {{/foreach}}
        {{/if}}
        {{/foreach}}
      </table>
    </td>
    {{/if}}
  {{/foreach}}
  </tr>
</table>

