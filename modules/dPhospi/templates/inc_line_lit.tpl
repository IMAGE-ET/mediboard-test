{{if $prestation_id}}
  <th class="text">{{$_lit->_selected_item->nom}}</th>
{{/if}}
<th class="text first_cell" style="text-align: left;" onclick="chooseLit('{{$_lit->_id}}'); this.down().checked = 'checked';" data-rank="{{$_lit->_selected_item->rank}}">
  {{if isset($_lit->_lines|smarty:nodefaults) && $_lit->_lines|@count > 1 && !$suivi_affectation}}
    <img src="modules/dPhospi/images/surb.png" title="Collision" style="float: right;">
  {{/if}}
  {{if !$readonly}}
    <input type="radio" name="lit_move" style="float: left;" id="lit_move_{{$_lit->_id}}" onclick="chooseLit('{{$_lit->_id}}');" />
  {{/if}}
  {{$_lit}}
</th>

{{assign var=onmouseevent value=onmouseout}}
{{if $smarty.session.browser.name == "msie"}}
  {{assign var=onmouseevent value=onmouseleave}}
{{/if}}

{{foreach from=0|range:$nb_ticks_r item=_i}}
  {{assign var=datetime value=$datetimes.$_i}}
  <td class="mouvement_lit {{if $datetime == $current}}current_hour{{/if}}" style="min-width: {{$td_width}}px;"
    data-date="{{$datetime}}"">
    {{if $_i == 0 && isset($_lit->_lines|smarty:nodefaults)}}
      {{*  Parcours des affectations *}}
      {{foreach from=$_lit->_lines item=_lines_by_level key=_level}}
        
        <div class="wrapper_line {{$mode_vue_tempo}}">
          
        {{foreach from=$_lines_by_level item=_affectations_ids}}
          
          {{foreach from=$_affectations_ids item=_affectation_id}}
            {{assign var=_affectation value=$affectations.$_affectation_id}}
            {{assign var=_sejour value=$_affectation->_ref_sejour}}
            {{assign var=_patient value=$_sejour->_ref_patient}}
            {{assign var=praticien value=$_sejour->_ref_praticien}}
            {{assign var=offset_op value=0}}
            {{assign var=width_op value=0}}
            {{if $praticien->_id}}
              {{assign var=color value=$praticien->_ref_function->color}}
            {{else}}
              {{assign var=color value="688"}}
            {{/if}}
            {{math equation=x*(y+4.6) x=$_affectation->_entree_offset y=$td_width assign=offset}}
            {{math equation=x*(y+4.6) x=$_affectation->_width y=$td_width assign=width}} 
            {{assign var=mode_vue_reelle value=$mode_vue_tempo}}
            {{if $_affectation->parent_affectation_id}}
              {{assign var=mode_vue_reelle value="compacte"}}
            {{/if}}
            <div id="affectation_temporel_{{$_affectation->_id}}" 
              class="affectation {{$mode_vue_reelle}} opacity-90 draggable
                {{if !$_sejour->_id}}clit_bloque{{else}}clit{{/if}}
                {{if $_affectation->_width < 6}}affectation_resize{{/if}}
                {{if $_sejour->confirme}}sejour_sortie_autorisee{{/if}}
                {{if $_affectation->entree == $_sejour->entree && $_affectation->entree >= $date_min}}debut_sejour{{/if}}
                {{if $_affectation->sortie == $_sejour->sortie && $_affectation->sortie <= $date_max}}fin_sejour{{/if}}
                {{if !$_affectation->sejour_id && $_affectation->entree >= $date_min}}debut_blocage{{/if}}
                {{if !$_affectation->sejour_id && $_affectation->sortie <= $date_max}}fin_blocage{{/if}}
                {{if $_affectation->entree > $date_min && $_sejour->_id}}affect_left{{/if}}
                {{if $_affectation->sortie < $date_max && $_sejour->_id}}affect_right{{/if}}
                {{if $_affectation->parent_affectation_id}}child{{/if}}
              "
              data-affectation_id="{{$_affectation->_id}}" 
              data-lit_id="{{$_affectation->lit_id}}"
              data-width="{{$_affectation->_width}}" 
              data-offset="{{$_affectation->_entree_offset}}"
              style="left: {{$offset}}px; width: {{$width}}px; border: 1px solid #{{$color}};"
              onmouseover="
                if ($(this).hasClassName('classique')) {
                  this.select('.affectation_toolbar')[0].show();
                }
              "
              {{if $mode_vue_reelle == "classique"}}
                {{$onmouseevent}}="this.select('.affectation_toolbar')[0].hide();"
              {{/if}}>

              {{if !$readonly}}
              <table class="layout_affectation">
                <tr>
                  {{if $_sejour->_id && $mode_vue_reelle == "classique"}}
                    <td class="narrow" style="vertical-align: top; padding-right: 2px !important;">
                      {{mb_include module=patients template=inc_vw_photo_identite mode=read patient=$_patient size=22}}
                    </td>
                  {{/if}}
                  
                  <td style="vertical-align: top;">
                    {{if $_sejour->_id}}
                      {{if ($_affectation->entree == $_sejour->entree && !$_sejour->entree_reelle) ||
                        ($_affectation->entree != $_sejour->entree && !$_affectation->_ref_prev->effectue)}}
                        <span style="color: #A33">
                      {{elseif $_affectation->effectue}}
                        <span style="text-decoration: line-through">
                      {{/if}}
                      
                      <span onmouseover="ObjectTooltip.createEx(this, '{{$_affectation->_guid}}');">
                        {{$_patient->nom}} {{$_patient->prenom}}
                      </span>
                      
                      {{if $show_age_patient}}({{$_patient->_age}} ans){{/if}}
                      
                      {{if ($_affectation->entree == $_sejour->entree && !$_sejour->entree_reelle) ||
                        ($_affectation->entree != $_sejour->entree && !$_affectation->_ref_prev->effectue) ||
                        $_affectation->effectue}}
                        </span>
                      {{/if}}
                      {{if $mode_vue_reelle == "classique"}}
                        <div class="compact">
                          {{$_sejour->_motif_complet}}
                          <em style="color: #f00;" title="Chambre seule">
                            {{if $_sejour->chambre_seule}}CS{{else}}CD{{/if}}
                            {{if $_sejour->prestation_id}}- {{$_sejour->_ref_prestation->code}}{{/if}}
                          </em>
                        </div>
                      {{/if}}
                    {{else}}
                      <span onmouseover="ObjectTooltip.createEx(this, '{{$_affectation->_guid}}');">
                        BLOQUE
                      </span>
                    {{/if}}
                  </td>
                  {{if $mode_vue_reelle != "compacte"}}
                    <td style="vertical-align: middle; width: 1%;">
                      <div style="display: none;" class="affectation_toolbar">
                       {{if $_affectation->sejour_id}}
                         {{if $conf.dPadmissions.show_deficience}}
                          <span style="margin-top: 3px; margin-right: 3px;">
                            {{mb_include module=patients template=inc_vw_antecedents patient=$_patient type=deficience readonly=1}}
                          </span>
                         {{/if}}
                         <a style="margin-top: 3px; display: inline" href="#1"
                            onclick="AffectationUf.affecter('{{$_affectation->_guid}}','{{$_lit->_guid}}')">
                           <img src="images/icons/uf.png" width="16" height="16" title="Affecter les UF" class="opacity-40"
                              onmouseover="this.toggleClassName('opacity-40')" onmouseout="this.toggleClassName('opacity-40')"/>
                         </a>
                       {{/if}}
                       <button type="button" class="trash notext opacity-40"
                          onmouseover="this.toggleClassName('opacity-40')" onmouseout="this.toggleClassName('opacity-40')"
                          onclick="delAffectation('{{$_affectation->_id}}', '{{$_affectation->lit_id}}')"></button>
                       <input type="radio" name="affectation_move" onclick="chooseAffectation('{{$_affectation->_id}}');" />
                      </div>
                    </td>
                  {{/if}}
                </tr>
              </table>
            {{/if}}
            {{assign var=$_affectation_id value=$_affectation->_id}}
            {{foreach from=$_sejour->_ref_operations item=_operation}}
              {{math equation=x*(y+4.6) x=$_operation->_debut_offset.$_affectation_id y=$td_width assign=offset_op}}
              {{math equation=x*(y+4.6) x=$_operation->_width.$_affectation_id y=$td_width assign=width_op}}
              <div class="operation_in_mouv{{if $mode_vue_reelle == "compacte"}}_compact{{/if}} opacity-40"
                style="left: {{$offset_op}}px; width: {{$width_op}}px;"></div>
              {{if $_operation->duree_uscpo}}
                {{math equation=x+y x=$offset_op y=$width_op assign=offset_uscpo}}
                {{math equation=x*(y+4.6) x=$_operation->_width_uscpo.$_affectation_id y=$td_width assign=width_uscpo}}
                
                <div class="soins_uscpo opacity-40"
                  style="left: {{$offset_uscpo}}px; width: {{$width_uscpo}}px; z-index: -1;"></div>
              {{/if}}
            {{/foreach}}
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