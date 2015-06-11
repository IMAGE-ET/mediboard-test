<script>
  window.sejour_selected = null;
  window.lit_selected    = null;
  window.affectation_selected = null;
  
  Main.add(function() {
    Calendar.regField(getForm('filterMouv').date);

    var vp_height = document.viewport.getHeight();

    var view_affectations = $("view_affectations");
    view_affectations.setStyle({
      height: vp_height*0.59+"px"
    });

    var list_affectations = $("list_affectations");
    list_affectations.setStyle({
      height: vp_height*0.3+"px"
    });

    if (!window.events_attached) {
      Event.observe(window, "resize", function(e){
        $("time_line_temporelle").style.width = $("tableau_vue_temporel").getWidth()+"px";
      });

      window.events_attached = true;
    }

    refreshMouvements(loadNonPlaces.curry(function() {
      var time_line_temporelle    = $("time_line_temporelle");
      var time_line_temporelle_na = $("time_line_temporelle_non_affectes");

      window.top_tempo = time_line_temporelle.getStyle("top");
      time_line_temporelle.setStyle({
        top: window.top_tempo
      });

      window.top_tempo_na = time_line_temporelle_na.getStyle("top");
      time_line_temporelle_na.setStyle({
        top: window.top_tempo_na
      });

      var tableau_vue_temporelle = $("tableau_vue_temporel");
      time_line_temporelle.setStyle({
        width: tableau_vue_temporelle.getWidth()+"px"
      });

      var view_affectations = $("view_affectations");

      if (!Prototype.Browser.IE) {
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

    // Zones des non placés redimensionnables
    var grippie = $("grippie_tempo");
    if (grippie) {
      height_temporel = $('temporel').getHeight() - $('temporel_filtre').getHeight() - 8;
      grippie.observe('mousedown', function(e) {
        Event.stop(e);
        staticOffset = $('view_affectations') .getHeight() - e.pointerY();

        document.observe('mousemove', performDrag)
          .observe('mouseup', endDrag);
      });
    }
  });

  performDrag = function(e) {
    Event.stop(e);

    var h = Math.min(Math.max(100, staticOffset + e.pointerY()), height_temporel - 100);
    var h2 =  height_temporel - h - 50;
    $('view_affectations').setStyle({height: h  + 'px'});
    $('list_non_places').setStyle({height: h2 + 'px'});
    $('list_affectations').setStyle({height: h2 + 'px'});
  }

  endDrag = function(e) {
    Event.stop(e);
    document.stopObserving('mousemove', performDrag)
      .stopObserving('mouseup', endDrag);
  }
</script>

<div style="display: none;" id="choose_interv_sejour">
  <table class="tbl">
    <tr>
      <th class="title" colspan="2">
        Création de :
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
  <div id="temporel_filtre" style="width: 100%;">
    Granularité :
    {{foreach from=$granularites item=_granularite}}
      <label>    
        <input type="radio" name="granularite" value="{{$_granularite}}" onclick="refreshMouvements(loadNonPlaces);"
          {{if $_granularite == $granularite}}checked="checked"{{/if}} />
          {{tr}}CService-granularite-{{$_granularite}}{{/tr}}
      </label>
    {{/foreach}}
    
    &mdash;
    
    Date :
    <input type="hidden" class="date notNull" name="date" value="{{$date}}"
      onchange="$V(this.form.date_da, new Date(Date.fromDATE($V(this))).toLocaleDate()); refreshMouvements(loadNonPlaces); ">

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

    {{if "dPhospi prestations systeme_prestations"|conf:"CGroups-$g" == "expert"}}
      &mdash;

      Axe de prestation :
      <select name="prestation_id" onchange="savePrefAndReload(this.value);">
        <option value="">&mdash; {{tr}}None{{/tr}}</option>
        <option value="all" {{if $prestation_id == "all"}}selected{{/if}}>{{tr}}All{{/tr}}</option>
        {{foreach from=$prestations_journalieres item=_prestation}}
          <option value="{{$_prestation->_id}}" {{if $_prestation->_id == $prestation_id}}selected="selected"{{/if}}>{{$_prestation->nom}}</option>
        {{/foreach}}
      </select>
    {{/if}}

    <button type="button" class="pause notext" onclick="togglePlayPause(this);"
      title="{{tr}}CAffectation-play_pause_temporel{{/tr}}"></button>
  </div>
  
</form>

<div id="view_affectations" style="overflow-x: auto; overflow-y: scroll;" onscroll="syncBars(this)"></div>
<div id="grippie_tempo" class="grippie-h" style="margin-top: 3px; margin-bottom: 3px; height: 6px"></div>
<div id="list_affectations"></div>