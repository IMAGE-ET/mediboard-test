<script type="text/javascript">
var series = {{$series|@json}};
var options = {{$options|@json}};
var countChecked = {{$count_checked|@json}};

function refreshCount(value) {
  $("selected-categories-count").update(value.split('|').without('').length || 'toutes');
}

function resetEvts() {
  $('categories').select('input').each(function(e) {$V(e, false, true)}); 
  oEvenementField.setValues('');

  $H(countChecked).each(function (pair) {
    if (pair.key !== null) {
      countChecked[pair.key] = 0;
    }
  });
  refreshEvtCounts();
}

function refreshEvtCounts() {
  $H(countChecked).each(function (pair) {
    var counter = $('selected-evts-'+pair.key+'-count');
    if (counter) {
      counter.update(pair.value);
    }
  });
}

function toggleEvt(catId, val, forceTo) {
  oEvenementField.toggle(val, forceTo);
  countChecked[catId] = countChecked[catId] + (forceTo ? 1 : -1);
  refreshEvtCounts();
}

Main.add(function () {
  filterForm = getForm('stats-filter');

  // Let's draw the graph
  var graph = Flotr.draw(
    $('stats'),
    series, Object.extend({
      bars: {show:true, barWidth:0.5, stacked:true, fillOpacity: 0.6},
      mouse: {track: false},
      yaxis: {min: 0},
      HtmlText: false,
      showDataGrid: true,
      tabGraphLabel: 'Graphique',
      tabDataLabel: 'Données'
    }, options)
  );
  
  Control.Tabs.create('filters-tabs');
  Control.Tabs.create('evenements-tabs');
  
  refreshCount($V(filterForm.evenements));
  refreshEvtCounts();
  
  oEvenementField = new TokenField(filterForm.evenements, {onChange: refreshCount});
});

var oEvenementField = null;
var filterForm = null;
</script>

<form name="stats-filter" action="?" method="get">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  <input type="hidden" name="evenements" value="{{$evenements}}" />
  
  <label for="comparison">Données de comparaison</label>
  <select name="comparison">
    {{foreach from=$fiche->_enumsTrans item=enum key=type}}
      <option value="{{$type}}" {{if $comparison == $type}}selected="selected"{{/if}}>{{tr}}CFicheEi-{{$type}}{{/tr}}</option>
    {{/foreach}}
  </select>
  
  <label for="months_count">Depuis</label>
  <select name="months_count">
    <option value="24" {{if $months_count == 24}}selected="selected"{{/if}}>24 mois</option>
    <option value="12" {{if $months_count == 12}}selected="selected"{{/if}}>12 mois</option>
    <option value="6" {{if $months_count == 6}}selected="selected"{{/if}}>6 mois</option>
  </select>
          
  <ul class="control_tabs" id="filters-tabs">
    <li><a href="#data">Filtres données</a></li>
    <li>
      <a href="#categories" style="padding-top: 2px; padding-bottom: 1px; padding-right: 1px;">
        Catégories (<span id="selected-categories-count">0</span> sélectionnées) 
        <button type="button" class="cancel" onclick="resetEvts()">Tout décocher</button>
      </a>
    </li>
  </ul>
  <hr class="control_tabs" />
  
  <table class="main form" id="data" style="display: none;">
    {{foreach from=$enums item=enum key=name name=enum}}
      {{if $smarty.foreach.enum.index % 2 == 0 || $smarty.foreach.enum.first}}
        <tr>
      {{/if}}
      <th><label for="filters[{{$name}}]">{{tr}}CFicheEi-{{$name}}{{/tr}}</label></th>
      <td>
        <select name="filters[{{$name}}]">
          <option value=""> &mdash; Tous</option>
          {{foreach from=$enum item=val key=key}}
            <option value="{{$key}}" {{if $filters.$name == $key}}selected="selected"{{/if}}>{{$val}}</option>
          {{/foreach}}
        </select>
      </td>
      {{if $smarty.foreach.enum.index+1 % 2 == 0 || $smarty.foreach.enum.last}}
        </tr>
      {{/if}}
    {{/foreach}}
  </table>
  
  <table id="categories" style="display: none;">
    <tr>
      <td>
        <ul class="control_tabs_vertical" id="evenements-tabs">
          {{foreach from=$list_categories item=curr_evenement name=categories}}
          <li>
            <a href="#category-{{$curr_evenement->ei_categorie_id}}" style="font-size: 1em; padding: 1px 3px; font-weight: normal;">
            {{$curr_evenement->nom}}
            (<span id="selected-evts-{{$curr_evenement->ei_categorie_id}}-count">0</span>)
            </a>
          </li>
          {{/foreach}}
        </ul>
      </td>
      <td style="vertical-align: top;">
        {{foreach from=$list_categories item=curr_evenement name=categories}}
          <table class="tbl" id="category-{{$curr_evenement->ei_categorie_id}}" style="display: none;">
           {{foreach from=$curr_evenement->_ref_items item=curr_item name=evt_item}}
             <tr>
               <td class="text">
                 <input type="checkbox" name="{{$curr_item->ei_item_id}}" onclick="toggleEvt('{{$curr_evenement->ei_categorie_id}}', this.name, this.checked); " {{if $curr_item->checked}}checked="checked"{{/if}}/>
                 <label for="{{$curr_item->ei_item_id}}" id="titleItem{{$curr_item->ei_item_id}}" title="{{$curr_item->nom}}">{{$curr_item->nom}}</label>
               </td>
             </tr>
           {{foreachelse}}
             <tr>
               <td>{{tr}}_CFicheEi-noitemscat{{/tr}}</td>
             </tr>
           {{/foreach}}
          </table>
        {{foreachelse}}
          {{tr}}CEiItem.none{{/tr}}
        {{/foreach}}
      </td>
    </tr>
  </table>
  
  <button type="submit" class="search">Filtrer</button>

  <div id="stats" style="width: 600px; height: 300px; margin: auto;"></div>
</form>

