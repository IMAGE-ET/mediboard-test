{{if $conf.dPhospi.CLit.alt_icons_sortants}}
  {{assign var=suffixe_icons value="2"}}
{{else}}
  {{assign var=suffixe_icons value=""}}
{{/if}}

<script>
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
            {{if $_lit->nom_complet}}
              <span style="float: left">{{$_lit->nom_complet}}</span>
            {{else}}
              <span style="float: left">{{$_chambre->_shortview}}</span>
              <span style="float: right">{{$_lit->_shortview}}</span>
            {{/if}}
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
            <img src="modules/dPhospi/images/X{{$suffixe_icons}}.png" alt="X" title="Ambulatoire" />
            {{elseif $_aff->sortie|iso_date == $demain}}
              {{if $_aff_next->_id}}
            <img src="modules/dPhospi/images/OC{{$suffixe_icons}}.png" alt="OC" title="Déplacé demain" />
              {{else}}
            <img src="modules/dPhospi/images/O{{$suffixe_icons}}.png" alt="O" title="Sortant demain" />
              {{/if}}
            {{elseif $_aff->sortie|iso_date == $date}}
              {{if $_aff_next->_id}}
            <img src="modules/dPhospi/images/OoC{{$suffixe_icons}}.png" alt="OoC" title="Déplacé aujourd'hui" />
              {{else}}
            <img src="modules/dPhospi/images/Oo{{$suffixe_icons}}.png" alt="Oo" title="Sortant aujourd'hui" />
              {{/if}}
            {{/if}}
          </td>
          <td class="text" {{if $_sejour->confirme}}style="background-image:url(images/icons/ray.gif); background-repeat:repeat;"{{/if}}>
            {{if !$_sejour->entree_reelle || ($_aff_prev->_id && $_aff_prev->effectue == 0)}}
              <span class="patient-not-arrived">
            {{elseif $_sejour->septique}}
              <span class="septique">
            {{else}}
              <span>
            {{/if}} 
              <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}')">
                <strong>
                  {{if $_sejour->type == "ambu"}}<em>{{/if}}
                  {{$_patient}}
                  {{if $_patient->naissance}}({{$_patient->_age}}){{/if}}
                  {{if $_sejour->type == "ambu"}}</em>{{/if}}
                </strong>
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

