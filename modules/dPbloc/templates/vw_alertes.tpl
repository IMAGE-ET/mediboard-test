<table class="tbl">
  {{if $nbAlertes}}
    <tr>
      <th colspan="10" class="title">{{$nbAlertes}} alerte(s) sur des interventions</th>
    </tr>
    <tr>
      <th>Date</th>
      <th>Chirurgien</th>
      <th>Patient</th>
      <th>Salle</th>
      <th>Intervention</th>
    </tr>
    {{foreach from=$blocs item=bloc}}
      {{foreach from=$bloc->_alertes_intervs item=_alerte}}
        {{assign var="_operation" value=$_alerte->_ref_object}}
        {{mb_include module=bloc template=inc_line_alerte is_alerte=1}}
      {{/foreach}}
    {{/foreach}}
  {{/if}}

  {{if $listNonValidees|@count}}
    <tr>
      <th>Date</th>
      <th>Chirurgien</th>
      <th>Patient</th>
      <th>Salle</th>
      <th>Intervention</th>
    </tr>
    <tr>
      <th colspan="10" class="title">{{$listNonValidees|@count}} intervention(s) non validées</th>
    </tr>
    {{foreach from=$listNonValidees item=_operation}}
      {{mb_include module=bloc template=inc_line_alerte is_alerte=0}}
    {{/foreach}}
  {{/if}}
  {{if $listHorsPlage|@count}}
    <tr>
      <th colspan="10" class="title">{{$listHorsPlage|@count}} intervention(s) hors plage</th>
    </tr>
    <tr>
      <th>Date</th>
      <th>Chirurgien</th>
      <th>Patient</th>
      <th>Salle</th>
      <th>Intervention</th>
    </tr>
    {{foreach from=$listHorsPlage item=_operation}}
      {{mb_include module=bloc template=inc_line_alerte is_alerte=0}}
    {{/foreach}}
  {{/if}}
</table>