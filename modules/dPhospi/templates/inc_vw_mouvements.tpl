<script type="text/javascript">
  Main.add(function() {
    var view_affectations = $("view_affectations");
    view_affectations.select(".droppable").each(function(th) {
       Droppables.add(th, {
        onDrop: function(div, th, event) {
          var lit_id = th.get("lit_id");
          
          // Création d'une affectation pour bloquer un lit
          if (div.id == "lit_bloque") {
            editAffectation(null, lit_id);
          }
          else {
            var ctrl_pressed = event.ctrlKey;
            // Si la touche ctrl est pressée dans le déplacement et que l'affectation existe,
            // ouverture de la modale pour demander quoi faire
            var affectation_id = div.get("affectation_id");
            var sejour_id = div.get("sejour_id");
            
            if (ctrl_pressed && affectation_id) {
              selectAction(affectation_id, lit_id, sejour_id);
            }
            // Sinon déplacement de l'affectation
            else if (lit_id != div.get("lit_id")) {
              moveAffectation(affectation_id, lit_id, sejour_id);
            }
          }
        },
        hoverclass: "lit_hover"
        });
     });
  });
</script>
<strong>
  <a href="#1" onclick="$V(getForm('filterMouv').date, '{{$date_before}}');">
    &lt;&lt;&lt; {{$date_before|date_format:$conf.date}}
  </a>
  <a href="#1" style="float: right" onclick="$V(getForm('filterMouv').date, '{{$date_after}}');">
    {{$date_after|date_format:$conf.date}} &gt;&gt;&gt;
  </a>
</strong>
<table class="tbl" style="width: auto; table-layout: fixed;">
  {{math equation=x+1 x=$nb_ticks assign=colspan}}
  {{math equation=x-1 x=$nb_ticks assign=nb_ticks_r}}
  <col style="width: 100px;" />
  
  
  {{foreach from=$services item=_service}}
    <tr>
      <th></th>
      {{if $granularite == "day"}}
        <th colspan="{{$nb_ticks}}">
          {{$date|date_format:$conf.longdate}}
        </th>
      {{else}}
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
    <tr>
      <th></th>
      {{foreach from=$datetimes item=_date}}
        <th style="min-width: 40px;">
          {{if $granularite == "day"}}
            {{$_date|date_format:$conf.time}}
          {{elseif $granularite == "week"}}
            {{$_date|date_format:"%H"}}h
          {{else}}
            {{$_date|date_format:"%a %d"}}
          {{/if}}
        </th>
      {{/foreach}}
    </tr>
    <tr>
      <th class="title" colspan="{{$colspan}}">{{$_service}}</th>
    </tr>
    
    {{foreach from=$_service->_ref_chambres item=_chambre}}     
      {{foreach from=$_chambre->_ref_lits item=_lit}}
        <tr id="{{$_lit->_guid}}">
          <th class="text droppable" data-lit_id="{{$_lit->_id}}">{{$_lit}}</th>
        {{foreach from=0|range:$nb_ticks_r item=_i}}
          <td class="mouvement_lit" data-date="{{$datetimes.$_i}}">
            {{if $_i == 0 && isset($_lit->_lines|smarty:nodefaults)}}
             
              {{*  Parcours des affectations *}}
              {{foreach from=$_lit->_lines item=_lines_by_level key=_level}}
                
                <div class="wrapper_line{{if $vue == "compacte"}}_compact{{/if}}">
                  
                {{foreach from=$_lines_by_level item=_affectations_ids}}
                  
                  {{foreach from=$_affectations_ids item=_affectation_id}}
                    {{assign var=_affectation value=$affectations.$_affectation_id}}
                    {{assign var=_sejour value=$_affectation->_ref_sejour}}
                    {{assign var=_patient value=$_sejour->_ref_patient}}
                    <div id="affectation_{{$_affectation->_id}}" data-affectation_id="{{$_affectation->_id}}" data-lit_id="{{$_affectation->lit_id}}"
                     class="affectation{{if $vue == "compacte"}}_compact{{/if}} opacity-90 draggable
                      {{if !$_sejour->_id}}clit_bloque{{else}}clit{{/if}}
                      {{if $_affectation->entree == $_sejour->entree && $_affectation->entree >= $date_min}}debut_sejour{{/if}}
                      {{if $_affectation->sortie == $_sejour->sortie && $_affectation->sortie <= $date_max}}fin_sejour{{/if}}
                      {{if $_affectation->entree > $date_min && $_sejour->_id}}affect_left{{/if}}
                      {{if $_affectation->sortie < $date_max && $_sejour->_id}}affect_right{{/if}}"
                      data-width="{{$_affectation->_width}}" data-offset="{{$_affectation->_entree_offset}}"
                      style="left: {{$_affectation->_entree_offset*45}}px; width: {{$_affectation->_width*45-5}}px;">
                      {{if $_sejour->_id && $vue == "classique"}}
                        <span style="float: left; padding-right: 1px;">
                        {{mb_include module=dPpatients template=inc_vw_photo_identite mode=read patient=$_sejour->_ref_patient size=22}}
                        </span>
                      {{/if}}
                      
                      <span onmouseover="ObjectTooltip.createEx(this, '{{$_affectation->_guid}}');">
                        {{if $_sejour->_id}}
                          {{$_patient->nom}} {{$_patient->prenom}}
                          {{if $vue == "classique"}}
                            <br />
                            <span class="compact">
                              {{$_sejour->libelle}}
                            </span>
                          {{/if}}
                        {{else}}
                          BLOQUE
                        {{/if}}
                      </span>
                    </div>
                    <script type="text/javascript">
                      new Draggable($('affectation_{{$_affectation->_id}}'), dragOptions);
                    </script>
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