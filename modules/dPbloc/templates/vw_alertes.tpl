<table class="tbl">
  <tr>
    <th>Date</th>
    <th>Chirurgien</th>
    <th>Salle</th>
    <th>Intervention</th>
  </tr>
  {{if $listAlertes|@count}}
  <tr>
    <th colspan="4">{{$listAlertes|@count}} alerte(s) sur des interventions</th>
  </tr>
  {{/if}}
  {{foreach from=$listAlertes item=_alerte}}
  {{assign var="_operation" value=$_alerte->_ref_object}}
  <tr>
    {{mb_include module=dPbloc template=inc_line_alerte is_alerte=1}}
  </tr>
  {{/foreach}}
  {{if $listNonValidees|@count}}
  <tr>
    <th colspan="4">{{$listNonValidees|@count}} intervention(s) non validées</th>
  </tr>
  {{/if}}
  {{foreach from=$listNonValidees item=_operation}}
  <tr>
    {{mb_include module=dPbloc template=inc_line_alerte is_alerte=0}}
  </tr>
  {{/foreach}}
  {{if $listHorsPlage|@count}}
  <tr>
    <th colspan="4">{{$listHorsPlage|@count}} intervention(s) hors plage</th>
  </tr>
  {{/if}}
  {{foreach from=$listHorsPlage item=_operation}}
  <tr>
    {{mb_include module=dPbloc template=inc_line_alerte is_alerte=0}}
  </tr>
  {{/foreach}}
</table>