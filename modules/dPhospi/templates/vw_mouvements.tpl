<script>
  window.sejour_selected = null;
  window.lit_selected    = null;
  window.affectation_selected = null;
  
  Main.add(function() {
    Calendar.regField(getForm('filterMouv').date);
    var view_affectations = $("view_affectations");
    view_affectations.setStyle(
      { height: document.viewport.getHeight()*(0.59)+"px"
      });
    var list_affectations = $("list_affectations");
    list_affectations.setStyle(
      { height: document.viewport.getHeight()*(0.3)+"px"
      });
    refreshMouvements(loadNonPlaces.curry(function() {
      var time_line_temporelle = $("time_line_temporelle");
      var time_line_temporelle_na = $("time_line_temporelle_non_affectes");
      window.top_tempo = time_line_temporelle.getStyle("top");
      time_line_temporelle.setStyle({top: window.top_tempo});
      window.top_tempo_na = time_line_temporelle_na.getStyle("top");
      time_line_temporelle_na.setStyle({top: window.top_tempo_na});

      var time_line_temporelle = $("time_line_temporelle");
      var tableau_vue_temporelle = $("tableau_vue_temporel");
      var view_affectations = $("view_affectations");
      time_line_temporelle.setStyle({width: tableau_vue_temporelle.getWidth()+"px" });

      Event.observe(window, "resize", function(e){
        time_line_temporelle.setStyle({width: tableau_vue_temporelle.getWidth()+"px" });
      });

      if (!Prototype.Browser.IE)  {
        view_affectations.on('scroll', function() {
          time_line_temporelle.setClassName('scroll_shadow', view_affectations.scrollTop);
        });
      }
      else {
        view_affectations.on('scroll', function() {
          var style = view_affectations.scrollTop > 0 ?
            "progid:DXImageTransform.Microsoft.Shadow(color='#969696', Direction=180, Strength=6)" : "";
          time_line_temporelle.setStyle({
            "filter": style
          });
        });
      }
    } ));
  });
</script>

<div style="display: none;" id="choose_interv_sejour">
  <table class="tbl">
    <tr>
      <th class="title" colspan="2">
        Cr�ation de :
      </th>
    <tr>
      <td>
        <button type="button" class="new" onclick="createIntervention()">{{tr}}COperation{{/tr}}</button>
      </td>
      <td>
        <button type="button" class="new" onclick="createSejour()">{{tr}}CSejour{{/tr}}</button>
      </td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button type="button" class="cancel" onclick="Control.Modal.close()">{{tr}}Close{{/tr}}</button>
      </td>
    </tr>
  </table>
</div>

<form name="filterMouv" action="?" method="get" onsubmit="return false;">
  <input type="hidden" name="m" value="hospi" />
  <input type="hidden" name="a" value="ajax_vw_mouvements" />
  
  <!-- Filtre -->
  <div style="width: 100%;">
    Granularit� :
    {{foreach from=$granularites item=_granularite}}
      <label>    
        <input type="radio" name="granularite" value="{{$_granularite}}" onclick="refreshMouvements(loadNonPlaces);"
          {{if $_granularite == $granularite}}checked="checked"{{/if}} />
          {{tr}}CService-granularite-{{$_granularite}}{{/tr}}
      </label>
    {{/foreach}}
    
    &mdash;
    
    Date :
    <input type="hidden" class="date" name="date" value="{{$date}}"
      onchange="$V(this.form.date_da, new Date(Date.fromDATE($V(this))).toLocaleDate()); refreshMouvements(loadNonPlaces); ">
    <input type="text" readonly="readonly" name="date_da" />
    
    &mdash;
    
    Vue :
    <label>
      <input type="radio" name="mode_vue_tempo" value="classique" onclick="refreshMouvements()"
        {{if $mode_vue_tempo == "classique"}}checked="checked"{{/if}}/> Classique
    </label>
    <label>
      <input type="radio" name="mode_vue_tempo" value="compacte" onclick="refreshMouvements()"
        {{if $mode_vue_tempo == "compacte"}}checked="checked"{{/if}}/> Compacte
    </label>
    
    &mdash;
    
    Axe de prestation :
    <select name="prestation_id" onchange="savePrefAndReload(this.value);">
      <option value="">&mdash; {{tr}}None{{/tr}}</option>
      <option value="all" {{if $prestation_id == "all"}}selected{{/if}}>{{tr}}All{{/tr}}</option>
      {{foreach from=$prestations_journalieres item=_prestation}}
        <option value="{{$_prestation->_id}}" {{if $_prestation->_id == $prestation_id}}selected="selected"{{/if}}>{{$_prestation->nom}}</option>
      {{/foreach}}
    </select>
    
    <button type="button" class="pause notext" onclick="togglePlayPause(this);"
      title="{{tr}}CAffectation-play_pause_temporel{{/tr}}"></button>
  </div>
  
</form>

<div id="view_affectations" style="overflow-x: auto; overflow-y: scroll;" onscroll="syncBars(this)"></div>
<hr />
<div id="list_affectations" style="overflow-x: auto; overflow-y: scroll;" onscroll="syncBars(this)"></div>