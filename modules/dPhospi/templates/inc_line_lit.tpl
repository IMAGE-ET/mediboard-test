{{mb_default var=in_corridor value=0}}

{{if $mode_vue_tempo == "classique"}}
  {{assign var=height_affectation value=3}}
{{else}}
  {{assign var=height_affectation value=1.6}}
{{/if}}

{{assign var=chambre value=$_lit->_ref_chambre}}

{{if $prestation_id}}
  <th class="text">{{$_lit->_selected_item->nom}}</th>
{{/if}}

<th class="text first_cell"
  onclick="chooseLit('{{$_lit->_id}}'); this.down().checked = 'checked';"
  style="text-align: left; {{if $_lit->_lines|@count}}height: {{math equation=x*y x=$_lit->_lines|@count y=$height_affectation}}em{{/if}}"
  data-rank="{{$_lit->_selected_item->rank}}">
  
  {{if $_lit->_id && !$conf.dPhospi.hide_alertes_temporel}}
    <span style="float: right;">
      {{if $_lit->_lines|@count > 1 && !$suivi_affectation}}
        <img src="modules/dPhospi/images/surb.png" title="Collision">
      {{/if}}
      {{if $chambre->_ecart_age > 15}}
        <img src="modules/dPhospi/images/age.png" alt="warning" title="Ecart d'âge important: {{$chambre->_ecart_age}} ans" />
      {{/if}}
      {{if $chambre->_genres_melanges}}
        <img src="modules/dPhospi/images/sexe.png" alt="warning" title="Sexes opposés" />
      {{/if}}
      {{if $chambre->_chambre_seule}}
        <img src="modules/dPhospi/images/seul.png" alt="warning" title="Chambre seule obligatoire" />
      {{/if}}
      {{if $chambre->_chambre_double}}
        <img src="modules/dPhospi/images/double.png" alt="warning" title="Chambre double possible" />
      {{/if}}
      {{if $chambre->_conflits_chirurgiens}}
        <img src="modules/dPhospi/images/prat.png" alt="warning" title="{{$chambre->_conflits_chirurgiens}} Conflit(s) de praticiens" />
      {{/if}}
      {{if $chambre->_conflits_pathologies}}
        <img src="modules/dPhospi/images/path.png" alt="warning" title="{{$chambre->_conflits_pathologies}} Conflit(s) de pathologies" />
      {{/if}}
      {{if $chambre->annule == 1}}
        <img src="modules/dPhospi/images/annule.png" alt="warning" title="Chambre plus utilisée" />
      {{/if}}
    </span>
  {{/if}}
  {{if !$readonly && !$in_corridor}}
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
  <td class="mouvement_lit {{if $datetime == $current}}current_hour{{/if}}"
    data-date="{{$datetime}}" style="vertical-align: top" {{if $_i == 0 && !$_lit->_id}}id="wrapper_line_{{$_lit->_affectation_id}}"{{/if}}>
    {{if $_i == 0}}
      {{*  Parcours des affectations *}}
      {{foreach from=$_lit->_lines item=_lines_by_level key=_level}}

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
            {{math equation=x*y x=$_affectation->_entree_offset y=$td_width assign=offset}}
            {{math equation=x*y x=$_affectation->_width y=$td_width assign=width}} 
            {{assign var=mode_vue_reelle value=$mode_vue_tempo}}
            {{if $_affectation->parent_affectation_id}}
              {{assign var=mode_vue_reelle value="compacte"}}
            {{/if}}
            <div id="affectation_temporel_{{$_affectation->_id}}" 
              class="affectation {{$mode_vue_reelle}} opacity-90 draggable
                {{$_sejour->_guid}}
                {{if !$_sejour->_id}}clit_bloque{{else}}clit{{/if}}
                {{if $_affectation->_width < 6}}affectation_resize{{/if}}
                {{if $_sejour->confirme}}sejour_sortie_confirmee{{/if}}
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
              style="left: {{$offset}}%; width: {{$width}}%; border: 1px solid #{{$color}}; margin-left: 15.1%;
                margin-top: {{math equation=x*y x=$_level y=$height_affectation}}em"
              onmouseover="
                if ($(this).hasClassName('classique')) {
                  this.select('.toolbar_affectation')[0].setStyle({visibility: 'visible'});
                }
              "
              {{if $mode_vue_reelle == "classique"}}
                {{$onmouseevent}}="this.select('.toolbar_affectation')[0].setStyle({visibility: 'hidden'});"
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
                      
                      <span onmouseover="ObjectTooltip.createEx(this, '{{$_affectation->_guid}}');" class="CPatient-view {{if $_sejour->recuse == "-1"}}opacity-70{{/if}}">
                        {{if $_sejour->recuse == "-1"}}[Att] {{/if}}{{$_patient->nom}} {{if $_patient->nom_jeune_fille}}({{$_patient->nom_jeune_fille}}) {{/if}}{{$_patient->prenom}}
                      </span>
                      
                      {{if $show_age_patient}}({{$_patient->_age}}){{/if}}
                      
                      {{if ($_affectation->entree == $_sejour->entree && !$_sejour->entree_reelle) ||
                        ($_affectation->entree != $_sejour->entree && !$_affectation->_ref_prev->effectue) ||
                        $_affectation->effectue}}
                        </span>
                      {{/if}}
                      {{if $_patient->_overweight}}
                        <img src="images/pictures/overweight.png" />
                      {{/if}}
                      {{if $mode_vue_reelle == "classique"}}
                        <div class="compact">
                          <span onmouseover="ObjectTooltip.createEx(this, '{{$praticien->_guid}}')">({{$praticien->_shortview}})</span>
                          {{$_sejour->_motif_complet}}
                          {{if $prestation_id && $_sejour->_curr_liaison_prestation}}
                            {{assign var=liaison value=$_sejour->_curr_liaison_prestation}}
                            {{assign var=item_presta value=$liaison->_ref_item}}
                            {{assign var=item_presta_realise value=$liaison->_ref_item_realise}}
                            <span
                            {{if $item_presta->_id && $item_presta_realise->_id}}
                              style="color:
                              {{if $item_presta->rank == $item_presta_realise->rank}}
                                #9F8
                              {{elseif $item_presta->rank > $item_presta_realise->rank}}
                                #FD9
                              {{else}}
                                #F89
                              {{/if}}"
                            {{/if}}>
                              {{if $item_presta_realise->_id}}
                                <em>({{$item_presta_realise->nom}})</em>
                              {{else}}
                                <em>({{$item_presta->nom}})</em>
                              {{/if}}
                            </span>
                          {{else}}
                            <em style="color: #f00;" title="Chambre seule">
                              {{if $_sejour->chambre_seule}}CS{{else}}CD{{/if}}
                              {{if $_sejour->prestation_id}}- {{$_sejour->_ref_prestation->code}}{{/if}}
                            </em>
                          {{/if}}
                        </div>
                      {{/if}}
                    {{elseif !$_affectation->function_id}}
                      <span onmouseover="ObjectTooltip.createEx(this, '{{$_affectation->_guid}}');">
                        BLOQUE
                      </span>
                    {{elseif $_affectation->function_id}}
                      <span onmouseover="ObjectTooltip.createEx(this, '{{$_affectation->_guid}}');">
                        BLOQUE POUR {{mb_value object=$_affectation field=function_id}}
                      </span>
                    {{/if}}
                  </td>
                  {{if $mode_vue_reelle != "compacte"}}
                    <td style="vertical-align: middle; width: 1%;">
                      {{if !$_affectation->uf_hebergement_id || !$_affectation->uf_medicale_id || !$_affectation->uf_soins_id}}
                        <a style="margin-top: 3px; display: inline" href="#1"
                          onclick="AffectationUf.affecter('{{$_affectation->_guid}}','{{$_lit->_guid}}', 'refreshMouvements.curry(null, {{$_affectation->lit_id}})')">
                          <img src="images/icons/uf-warning.png" width="16" height="16" title="Affecter les UF" />
                        </a>
                      {{/if}}
                      <span class="toolbar_affectation">
                        {{if $_affectation->sejour_id}}
                          {{if $conf.dPadmissions.show_deficience}}
                            <span style="margin-top: 3px; margin-right: 3px;">
                              {{mb_include module=patients template=inc_vw_antecedents patient=$_patient type=deficience readonly=1}}
                            </span>
                          {{/if}}
                          {{if $_affectation->uf_hebergement_id && $_affectation->uf_medicale_id && $_affectation->uf_soins_id}} 
                            <a style="margin-top: 3px; display: inline" href="#1"
                               onclick="AffectationUf.affecter('{{$_affectation->_guid}}','{{$_lit->_guid}}', 'refreshMouvements.curry(null, \'{{$_affectation->lit_id}}\')')">
                              <img src="images/icons/uf.png" width="16" height="16" title="Affecter les UF" class="opacity-40"
                                onmouseover="this.toggleClassName('opacity-40')" onmouseout="this.toggleClassName('opacity-40')"/></a>
                          {{/if}}
                        {{/if}}
                        {{if !$in_corridor && $_affectation->sejour_id != 0}}
                          <button type="button" class="door-out notext opacity-40"
                            title="Placer dans le couloir"
                            onmouseover="this.toggleClassName('opacity-40')" onmouseout="this.toggleClassName('opacity-40')"
                            onclick="moveAffectation('{{$_affectation->_id}}', '', '', '{{$_affectation->lit_id}}'); loadNonPlaces()"></button>
                        {{/if}}
                        <button type="button" class="edit notext opacity-40"
                          onmouseover="this.toggleClassName('opacity-40')" onmouseout="this.toggleClassName('opacity-40')"
                          onclick="editAffectation('{{$_affectation->_id}}')"></button>
                        <input type="radio" name="affectation_move" onclick="chooseAffectation('{{$_affectation->_id}}');" />
                      </span>
                    </td>
                  {{/if}}
                </tr>
              </table>
            {{/if}}
            {{assign var=$_affectation_id value=$_affectation->_id}}
            {{foreach from=$_sejour->_ref_operations item=_operation}}
              {{math equation=(x/y)*100 x=$_operation->_debut_offset.$_affectation_id y=$_affectation->_width assign=offset_op}}
              {{math equation=(x/y)*100 x=$_operation->_width.$_affectation_id y=$_affectation->_width assign=width_op}}
              <div class="operation_in_mouv{{if $mode_vue_reelle == "compacte"}}_compact{{/if}} opacity-40"
                style="left: {{$offset_op}}%; width: {{$width_op}}%;"></div>
              {{if $_operation->duree_uscpo}}
                {{math equation=x+y x=$offset_op y=$width_op assign=offset_uscpo}}
                {{math equation=x/y*100 x=$_operation->_width_uscpo.$_affectation_id y=$_affectation->_width assign=width_uscpo}}
                
                <div class="soins_uscpo opacity-40"
                  style="left: {{$offset_uscpo}}%; width: {{$width_uscpo}}%; z-index: -1;"></div>
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
                reverteffect: function(element) {
                  element.style.top = "auto";
                  {{if $in_corridor}}
                    element.style.left = element.save_left;
                    element.style.width = element.save_width;
                    element.style.marginLeft = "15.1%";
                  {{/if}}
                },
                {{if $in_corridor}}
                onStart: function(drgObj, mouseEvent){
                  var element = drgObj.element;
                  element.save_left = element.getStyle("left");
                  element.save_width = element.getStyle("width");
                  var table = element.up('table')
                  var left = element.cumulativeOffset().left
                  var width = element.getWidth();
                  var top = element.viewportOffset().top - element.cumulativeScrollOffset().top;
                  
                  $(document.body).insert(element);
                  element.setStyle({
                    left:       left + 'px',
                    marginLeft: '0',
                    width:      width + 'px',
                    top:        '100px'
                  });
                },
                onEnd: function(drbObj, mouseEvent) {
                  var element = drbObj.element;
                  $('wrapper_line_'+element.get('affectation_id')).insert(element);
                },
                {{/if}}
                revert: true
              });
            </script>
          {{/if}}
        {{/foreach}}
      {{/foreach}}
    {{/foreach}}
    {{if !$_lit->_lines|@count}}
      <table style="display: none;">
        <tr>
          <td>
            <span class="CPatient-view" ></span>
          </td>
        </tr>
      </table>
    {{/if}}
  {{/if}}
</td>
{{/foreach}}