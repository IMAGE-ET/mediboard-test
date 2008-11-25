<script type="text/javascript">
  Main.add(function() {
    var list = $("tab-incident").select('a[href="#{{$type}}"] span');
    list.last().update("{{$countFiches}}");
  });
</script>

{{if $countFiches > 20}}
<div style="text-align: right;">
  {{if $first >= 20}}
  <a href="#1" onclick="loadListFiches('{{$type}}', '{{$first-20}}')" style="font-weight: bold; font-size: 1.5em; float: left;">
  [{{$first-20}} - {{$first}}] &lt;&lt;
  </a>
  {{/if}}
  {{if $first < $countFiches - 20}}
  <a href="#1" onclick="loadListFiches('{{$type}}', '{{$first+20}}')" style="font-weight: bold; font-size: 1.5em;">
  &gt;&gt; [{{$first+21}} - {{$first+41}}]
  </a>
  
  {{/if}}
</div>
{{/if}}

<table class="tbl" style="clear: both;">
  <tr>
    <th class="category">{{tr}}Date{{/tr}}</th>
    <th class="category">
      {{if $type=="ALL_TERM" && $can->admin}}
        <form name="Ei{{$type}}" action="?m={{$m}}">
          <select name="user_id" onchange="search_AllEI(this)">
          <option value="">&mdash; {{tr}}_CFicheEi_allusers{{/tr}}</option>
          {{foreach from=$listUsersTermine item=curr_user}}
            <option value="{{$curr_user->user_id}}"{{if $curr_user->user_id==$selected_user_id}} selected="selected"{{/if}}>
              {{$curr_user->_view}}
            </option>
          {{/foreach}}
          </select>
        </form>
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
  {{foreach from=$listeFiches item=currFiche}}
  <tr {{if $currFiche->_id == $selected_fiche_id}}class="selected"{{/if}}>
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
  {{foreachelse}}
  <tr>
    <td colspan="7">{{tr}}CFicheEi.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>