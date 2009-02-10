<br />
<form name="ChoixStat" method="post" action="#">
  <label for="stat" title="Statistique à afficher">Statistique</label>
  <select name="stat" onchange="form.submit()">
  {{foreach from=$stats item=_stat}}
    <option value="{{$_stat}}" {{if $_stat == $stat}}selected="selected"{{/if}}> 
    	{{tr}}mod-dPboard-tab-{{$_stat}}{{/tr}}
    </option>
  {{/foreach}}
  </select>
</form>

{{if !$stat}}
<div class="big-info">
  Plusieurs statistiques sont disponibles pour le praticien.
  <br />Merci d'en <strong>sélectionner</strong> une dans la liste ci-dessus.
</div>
{{/if}}
