{{if $voletAcc}}
<form name="Ei{{$voletAcc}}" action="?m={{$m}}">
{{/if}}
<table class="form">
  {{if $listeFichesTitle}}
  <tr>
    <th class="title" colspan="5">
      {{$listeFichesTitle}}
    </th>
  </tr>
  {{/if}}
  <tr>
    <th class="category">Date</th>
    <th class="category">
      {{if $voletAcc=="ALL_TERM"}}
        <select name="allEi_user_id" onchange="search_AllEI()">
        <option value="">&mdash; Tous les Auteurs</option>
        {{foreach from=$listUsersTermine item=curr_user}}        
          <option value="{{$curr_user->user_id}}"{{if $curr_user->user_id==$allEi_user_id}} selected="selected"{{/if}}>
            {{$curr_user->_view}}
          </option>
        {{/foreach}}
        </select>
      {{else}}
        Auteur
      {{/if}}
    </th>
    <th class="category">Deg. Urg.</th>
    <th class="category">Verification</th>
    <th class="category">Controle</th>
  </tr>
  {{if $listeFiches|@count}}
  {{foreach from=$listeFiches item=currFiche}}
  <tr>
    <td class="text">
      <a href="index.php?m=dPqualite&amp;tab=vw_incidentvalid&amp;fiche_ei_id={{$currFiche->fiche_ei_id}}">
        {{$currFiche->date_incident|date_format:"%d %b %Y à %Hh%M"}}
      </a>
    </td>
    <td class="text">
      <a href="index.php?m=dPqualite&amp;tab=vw_incidentvalid&amp;fiche_ei_id={{$currFiche->fiche_ei_id}}">
        {{$currFiche->_ref_user->_view}}
      </a>
    </td>
    <td>
      {{if $currFiche->degre_urgence}}
      {{$currFiche->degre_urgence}}
      {{else}}-{{/if}}
    </td>
    <td>
      {{if $currFiche->qualite_date_verification}}
      {{$currFiche->qualite_date_verification|date_format:"%d %b %Y"}}
      {{else}}-{{/if}}
    </td>
    <td>
      {{if $currFiche->qualite_date_controle}}
      {{$currFiche->qualite_date_controle|date_format:"%d %b %Y"}}
      {{else}}-{{/if}}
    </td>
  </tr>
  {{/foreach}}
  
  {{else}}
  <tr>
    <td colspan="5">Aucune Fiches disponible</td>
  </tr>
  {{/if}}
</table>
{{if $voletAcc}}
</form>
{{/if}}
{{if $reloadAjax}}
<script language="Javascript" type="text/javascript">
writeHeader("QualAllEIHeader", "Toutes les fiches d'EI Traitées {{if $allEi_user_id}}pour {{$listUsersTermine.$allEi_user_id->_view}}{{/if}} ({{$listeFiches|@count}})");
</script>
{{/if}}