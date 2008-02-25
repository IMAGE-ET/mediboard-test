{{foreach from=$plages item=curr_plage}}
<hr />

<table class="form">
  <tr>
    <th class="category{{if $vueReduite}} text{{/if}}" colspan="2">
      <a href="?m=dPbloc&amp;tab=vw_edit_interventions&amp;plageop_id={{$curr_plage->_id}}" title="Administrer la plage">
        {{$curr_plage->_ref_salle->_view}}
        {{if $vueReduite}}
          <br />
        {{else}}
          -
        {{/if}}
        {{$curr_plage->debut|date_format:"%Hh%M"}} à {{$curr_plage->fin|date_format:"%Hh%M"}}
      </a>
    </th>
  </tr>
</table>

<table class="tbl">
  {{if $curr_plage->_ref_operations|@count}}
  {{include file="../../dPsalleOp/templates/inc_liste_operations.tpl" urgence=0 operations=$curr_plage->_ref_operations}}
  {{/if}}
  
  {{if $curr_plage->_unordered_operations|@count}}
  <tr>
    <th colspan="10">Non placées</th>
  </tr>
  {{include file="../../dPsalleOp/templates/inc_liste_operations.tpl" urgence=0 operations=$curr_plage->_unordered_operations}}
  {{/if}}
</table>
{{/foreach}}

{{if $deplacees|@count}}

<hr />

<table class="form">
  <tr>
    <th class="category" colspan="2">
      Déplacées
    </th>
  </tr>
</table>
<table class="tbl">
  {{include file="../../dPsalleOp/templates/inc_liste_operations.tpl" urgence=1 operations=$deplacees}}
</table>
{{/if}}

{{if $urgences|@count}}

<hr />

<table class="form">
  <tr>
    <th class="category" colspan="2">
      Urgences
    </th>
  </tr>        
</table>
<table class="tbl">
  {{include file="../../dPsalleOp/templates/inc_liste_operations.tpl" urgence=1 operations=$urgences}}
</table>
{{/if}}