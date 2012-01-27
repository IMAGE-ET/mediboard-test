{{if !$readonly}}
  <script type="text/javascript">
    Main.add(function() {
      var time_line_temporelle = $("time_line_temporelle");
      var view_affectations = $("view_affectations");
      view_affectations.scrollTop = 0;
      time_line_temporelle.setStyle({width: (parseInt(view_affectations.getStyle("width")) - 15)+'px'});
      var width_th = $("tableau_vue_temporel").down("tr", 1).down("th").getStyle("width");
      $$(".first_th").each(function(th) {
        th.setStyle({minWidth: width_th});
      });
      
      if (Prototype.Browser.Gecko) {
        if (!window.top_view_affectations) {
          var top_tempo = time_line_temporelle.getStyle("top");
          time_line_temporelle.setStyle({top: top_tempo});
        }
      }
      
      if (!Prototype.Browser.IE)  {
        view_affectations.on('scroll', function() {
          time_line_temporelle.setClassName('scroll_shadow', view_affectations.scrollTop);
        });
      }
            
      view_affectations.select(".droppable").each(function(tr) {
         Droppables.add(tr, {
          onDrop: function(div, tr, event) {
            if ( !tr.isVisible(view_affectations)) return;
            
            var lit_id = tr.get("lit_id");
            
            // Création d'une affectation pour bloquer un lit
            if (div.id == "lit_bloque") {
              editAffectation(null, lit_id);
            }
            else {
              var ctrl_pressed = event.ctrlKey;
              var affectation_id = div.get("affectation_id");
              var sejour_id = div.get("sejour_id");
              
              // Si la touche ctrl est pressée dans le déplacement et que l'affectation existe,
              // ouverture de la modale pour demander quoi faire
              if (ctrl_pressed && affectation_id) {
                selectAction(affectation_id, lit_id, sejour_id);
              }
              // Sinon déplacement de l'affectation si c'est vers un autre lit
              else if (lit_id != div.get("lit_id")) {
                moveAffectation(affectation_id, lit_id, sejour_id);
              }
            }
          },
          hoverclass: "lit_hover",
          accept:'draggable'
          });
          // Bug de firefox
          tr.setStyle({position: "static"});
       });
    });
  </script>
{{/if}}

{{math equation=x+1 x=$nb_ticks assign=colspan}}
{{math equation=x-1 x=$nb_ticks assign=nb_ticks_r}}

<div style="height: 4.6em; width: 100%">
  <div id="time_line_temporelle" style="background: #fff;z-index: 200; position: absolute;">
    <strong>
      <a href="#1" onclick="$V(getForm('filterMouv').date, '{{$date_before}}');">
        &lt;&lt;&lt; {{$date_before|date_format:$conf.date}}
      </a>
      {{if $can->admin}}
        <span>{{$nb_affectations}} affectation(s)</span>
      {{/if}}
      <a href="#1" style="float: right" onclick="$V(getForm('filterMouv').date, '{{$date_after}}');">
        {{$date_after|date_format:$conf.date}} &gt;&gt;&gt;
      </a>
    </strong>
    <table class="tbl" style="width: auto; table-layout: fixed;">
      <tr>
      
      {{if $granularite == "day"}}
        <th colspan="{{$colspan}}">
          {{$date|date_format:$conf.longdate}}
        </th>
      {{else}}
        <th></th>
        {{foreach from=$days item=_day key=_datetime}}
        
          <th colspan="{{if $granularite == "week"}}4{{else}}7{{/if}}">
            {{if $granularite == "week"}}
              {{$_day|date_format:"%a"}} {{$_day|date_format:$conf.date}}
            {{else}}
              {{if isset($change_month.$_day|smarty:nodefaults)}}
                {{if isset($change_month.$_day.left|smarty:nodefaults)}}
                  <span style="float: left;">
                    {{$change_month.$_day.left|date_format:"%B"}}
                  </span>
                {{/if}}
                {{if isset($change_month.$_day.right|smarty:nodefaults)}}
                  <span style="float: right;">
                    {{$change_month.$_day.right|date_format:"%B"}}
                  </span>
                {{/if}}
              {{/if}}
              Semaine {{$_day}}
            {{/if}}
          </th>
        {{/foreach}}
      {{/if}}
      </th>
    </tr>
   {{if $granularite == "day"}}
     {{assign var=td_width value=37}}
   {{else}}
     {{assign var=td_width value=30}}
   {{/if}}
    <tr>
      <th class="first_th"></th>
      {{foreach from=$datetimes item=_date}}
        <th style="min-width: {{$td_width}}px;">
          {{if $granularite == "4weeks"}}
            {{$_date|date_format:"%a"|upper|substr:0:1}} {{$_date|date_format:"%d"}}
          {{else}}
            {{$_date|date_format:"%H"}}h
          {{/if}}
        </th>
      {{/foreach}}
    </tr>
    </table>
  </div>
</div>
<table class="tbl" style="width: auto; table-layout: fixed;" id="tableau_vue_temporel">
  <col style="width: 100px;" />

  {{foreach from=$services item=_service}}
    <tr>
      <th class="title" colspan="{{$colspan}}">{{$_service}}</th>
    </tr>
    {{assign var=show_age_patient value=$conf.dPhospi.show_age_patient}}
    {{foreach from=$_service->_ref_chambres item=_chambre}}
      {{foreach from=$_chambre->_ref_lits item=_lit}}
        <tr data-lit_id="{{$_lit->_id}}" id="{{$_lit->_guid}}" class="droppable">
          <th class="text" style="text-align: left;" onclick="this.down().click();">
            {{if isset($_lit->_lines|smarty:nodefaults) && $_lit->_lines|@count > 1}}
              <img src="modules/dPhospi/images/surb.png" title="Collision" style="float: right;">
            {{/if}}
            {{if !$readonly}}
              <input type="radio" name="lit_move" style="float: left;" id="lit_move_{{$_lit->_id}}" onchange="chooseLit('{{$_lit->_id}}');" />
            {{/if}}
            {{$_lit}}
          </th>
          {{foreach from=0|range:$nb_ticks_r item=_i}}
            {{assign var=datetime value=$datetimes.$_i}}
            <td class="mouvement_lit {{if $datetime == $current}}current_hour{{/if}}" data-date="{{$datetime}}" style="min-width: {{$td_width}}px;">
              {{if $_i == 0 && isset($_lit->_lines|smarty:nodefaults)}}
               
                {{*  Parcours des affectations *}}
                {{foreach from=$_lit->_lines item=_lines_by_level key=_level}}
                  
                  <div class="wrapper_line{{if $mode_vue_tempo == "compacte"}}_compact{{/if}}">
                    
                  {{foreach from=$_lines_by_level item=_affectations_ids}}
                    
                    {{foreach from=$_affectations_ids item=_affectation_id}}
                      {{assign var=_affectation value=$affectations.$_affectation_id}}
                      {{assign var=_sejour value=$_affectation->_ref_sejour}}
                      {{assign var=_patient value=$_sejour->_ref_patient}}
                      {{assign var=praticien value=$_sejour->_ref_praticien}}
                      {{math equation=x*(y+4.6) x=$_affectation->_entree_offset y=$td_width assign=offset}}
                      {{math equation=x*(y+4.6) x=$_affectation->_width y=$td_width assign=width}} 
                      
                      <div id="affectation_temporel_{{$_affectation->_id}}" data-affectation_id="{{$_affectation->_id}}" data-lit_id="{{$_affectation->lit_id}}"
                       class="affectation{{if $mode_vue_tempo == "compacte"}}_compact{{/if}} opacity-90 draggable
                        {{if !$_sejour->_id}}clit_bloque{{else}}clit{{/if}}
                        {{if $_affectation->confirme}}affectation_sortie_autorisee{{/if}}
                        {{if $_affectation->entree == $_sejour->entree && $_affectation->entree >= $date_min}}debut_sejour{{/if}}
                        {{if $_affectation->sortie == $_sejour->sortie && $_affectation->sortie <= $date_max}}fin_sejour{{/if}}
                        {{if $_affectation->entree > $date_min && $_sejour->_id}}affect_left{{/if}}
                        {{if $_affectation->sortie < $date_max && $_sejour->_id}}affect_right{{/if}}"
                        data-width="{{$_affectation->_width}}" data-offset="{{$_affectation->_entree_offset}}"
                        style="left: {{$offset}}px; width: {{$width}}px; border: 1px solid #{{$praticien->_ref_function->color}};"
                        onmouseover="ObjectTooltip.createEx(this, '{{$_affectation->_guid}}');">
                        {{if $mode_vue_tempo == "classique" && $_affectation->_width > 3 && !$readonly}}
                          <button type="button" class="trash notext opacity-40" style="float: right"
                            onmouseover="this.toggleClassName('opacity-40')" onmouseout="this.toggleClassName('opacity-40')"
                            onclick="delAffectation('{{$_affectation->_id}}')"></button>
                        {{/if}}
                        {{if $_sejour->_id && $mode_vue_tempo == "classique"}}
                          <span style="float: left; padding-right: 1px;">
                            {{mb_include module=dPpatients template=inc_vw_photo_identite mode=read patient=$_patient size=22}}
                          </span>
                        {{/if}}
                        
                        <div id="wrapper_op">
                          <span>
                            {{if $_sejour->_id}}
                              {{if ($_affectation->entree == $_sejour->entree && !$_sejour->entree_reelle) ||
                                ($_affectation->entree != $_sejour->entree && !$_affectation->_ref_prev->effectue)}}
                                <span style="color: #A33">
                              {{elseif $_affectation->effectue}}
                                <span style="text-decoration: line-through">
                              {{/if}}
                                {{$_patient->nom}} {{$_patient->prenom}} {{if $show_age_patient}}({{$_patient->_age}} ans){{/if}}
                              {{if ($_affectation->entree == $_sejour->entree && !$_sejour->entree_reelle) ||
                                ($_affectation->entree != $_sejour->entree && !$_affectation->_ref_prev->effectue) ||
                                $_affectation->effectue}}
                                </span>
                              {{/if}}
                              {{if $mode_vue_tempo == "classique"}}
                                <br />
                                <span class="compact">
                                  {{$_sejour->_motif_complet}}
                                </span>
                              {{/if}}
                            {{else}}
                              BLOQUE
                            {{/if}}
                          </span>
                          {{foreach from=$_sejour->_ref_operations item=_operation}}
                            {{math equation=x*(y+4.6) x=$_operation->_debut_offset y=$td_width assign=offset_op}}
                            {{math equation=x*(y+4.6) x=$_operation->_width y=$td_width assign=width_op}}
                            <div class="operation_in_mouv{{if $mode_vue_tempo == "compacte"}}_compact{{/if}} opacity-40"
                              style="left: {{$offset_op}}px; width: {{$width_op}}px;"></div>
                          {{/foreach}}
                        </div>
                       
                      </div>
                      {{if !$readonly}}
                        <script type="text/javascript">
                          var container = $('affectation_temporel_{{$_affectation->_id}}');
                          new Draggable(container, {
                            constraint: "vertical",
                            starteffect: function(element) {
                              new Effect.Opacity(element, { duration:0.2, from:1.0, to:0.7 });
                            },
                            revert: true
                            });
                        </script>
                      {{/if}}
                    {{/foreach}}
                  {{/foreach}}
                  </div>
                {{/foreach}}
              {{/if}}
            </td>
          {{/foreach}}
        </tr>
      {{/foreach}}
    {{/foreach}}
  {{/foreach}}
</table>