{{if $voletAcc}}
<form name="Ei{{$voletAcc}}" action="?m={{$m}}">
{{/if}}
<table class="tbl" id="ei_liste">
  <tr>
    <th class="category">{{tr}}Date{{/tr}}</th>
    <th class="category">
      {{if $voletAcc=="ALL_TERM"}}
        <select name="allEi_user_id" onchange="search_AllEI()">
        <option value="">&mdash; {{tr}}_CFicheEi_allusers{{/tr}}</option>
        {{foreach from=$listUsersTermine item=curr_user}}        
          <option value="{{$curr_user->user_id}}"{{if $curr_user->user_id==$allEi_user_id}} selected="selected"{{/if}}>
            {{$curr_user->_view}}
          </option>
        {{/foreach}}
        </select>
      {{else}}
        {{tr}}CFicheEi-user_id-court{{/tr}}
      {{/if}}
    </th>
    <th class="category">{{tr}}CFicheEi-service_valid_user_id-court{{/tr}}</th>
    <th class="category">{{tr}}CFicheEi-degre_urgence-court{{/tr}}</th>
    <th class="category">{{tr}}CFicheEi-_criticite-court{{/tr}}</th>
    <th class="category">{{tr}}CFicheEi-qualite_date_verification-court{{/tr}}</th>
    <th class="category">{{tr}}CFicheEi-qualite_date_controle-court{{/tr}}</th>
  </tr>
  {{if $listeFiches|@count}}
  {{foreach from=$listeFiches item=currFiche}}
  <tr>
    <td class="text">
      <a href="?m=dPqualite&amp;tab=vw_incidentvalid&amp;fiche_ei_id={{$currFiche->fiche_ei_id}}">
        {{$currFiche->date_incident|date_format:"%d/%m/%Y %Hh%M"}}
      </a>
    </td>
    <td class="text">
      <a href="?m=dPqualite&amp;tab=vw_incidentvalid&amp;fiche_ei_id={{$currFiche->fiche_ei_id}}">
        {{$currFiche->_ref_user->_view}}
      </a>
    </td>
    <td class="text">
      <a href="?m=dPqualite&amp;tab=vw_incidentvalid&amp;fiche_ei_id={{$currFiche->fiche_ei_id}}">
        {{$currFiche->_ref_service_valid->_view}}
      </a>
    </td>
    <td>
      {{if $currFiche->degre_urgence}}
      {{$currFiche->degre_urgence}}
      {{else}}-{{/if}}
    </td>
    <td>
      {{if $currFiche->_criticite}}
      {{$currFiche->_criticite}}
      {{else}}-{{/if}}
    </td>
    <td>
      {{if $currFiche->qualite_date_verification}}
      {{$currFiche->qualite_date_verification|date_format:"%d/%m/%Y"}}
      {{else}}-{{/if}}
    </td>
    <td>
      {{if $currFiche->qualite_date_controle}}
      {{$currFiche->qualite_date_controle|date_format:"%d/%m/%Y"}}
      {{else}}-{{/if}}
    </td>
  </tr>
  {{/foreach}}
  
  {{else}}
  <tr>
    <td colspan="7">{{tr}}CFicheEi.none{{/tr}}</td>
  </tr>
  {{/if}}
</table>
{{if $voletAcc}}
</form>
{{/if}}
{{if $reloadAjax}}
<script type="text/javascript">
  $("QualAllEIHeader").update("{{if $allEi_user_id}}{{tr}}_CFicheEi_allfichesuser{{/tr}} {{$listUsersTermine.$allEi_user_id->_view}}{{else}}{{tr}}_CFicheEi_allfiches{{/tr}}{{/if}} ({{$listeFiches|@count}})");
</script>
{{/if}}