<script type="text/javascript">
  {{if !$readonly}}
    var container = $('lit_bloque');
    new Draggable(container, {
                  ghosting: "true",
                  starteffect: function(element) {
                    new Effect.Opacity(element, { duration:0.2, from:1.0, to:0.7 });
                  },
                  revert: true});
    var container2 = $('lit_urgence');
    new Draggable(container2, {
                  ghosting: "true",
                  starteffect: function(element) {
                    new Effect.Opacity(element, { duration:0.2, from:1.0, to:0.7 });
                  },
                  revert: true});
  {{/if}}
  
  Main.add(function() {
  
    var time_line_temporelle_non_affectes = $("time_line_temporelle_non_affectes");
    var list_affectations = $("list_affectations");
    
    if (Prototype.Browser.Gecko) {
      var top_tempo = time_line_temporelle_non_affectes.getStyle("top");
      time_line_temporelle_non_affectes.setStyle({top: top_tempo});
    }
    
    if (!Prototype.Browser.IE) {
      list_affectations.on('scroll', function() {
        time_line_temporelle_non_affectes.setClassName('scroll_shadow', list_affectations.scrollTop);
      });
      list_affectations.fire('scroll');
    }
    $("list_affectations").scrollTop = Placement.scrollNonPlaces;
  });
</script>

{{math equation=x+1 x=$nb_ticks assign=colspan}}
{{math equation=x-1 x=$nb_ticks assign=nb_ticks_r}}

<div style="height: 2em; width: 100%">
  <div id="time_line_temporelle_non_affectes" style="background: #fff; position: absolute; z-index: 200;">
    <div style="display: inline-block;">
      <input type="text" style="width: 7em;" onkeyup="filter(this, 'non_places_temporel')" class="search" />
    </div>
    <form name="chgFilter" action="?" method="get" onsubmit="return onSubmitFormAjax(this,null, 'list_affectations');">
      <input type="hidden" name="m" value="dPhospi" />
      <input type="hidden" name="a" value="ajax_vw_non_places" />
      {{mb_field object=$sejour field="_type_admission" style="width: 16em;" onchange="this.form.onsubmit()"}}
    
      <select name="triAdm" style="width: 16em;" onchange="this.form.onsubmit()">
        <option value="praticien"   {{if $triAdm == "praticien"}}  selected="selected"{{/if}}>Tri par praticien</option>
        <option value="date_entree" {{if $triAdm == "date_entree"}}selected="selected"{{/if}}>Tri par heure d'entrée</option>
        <option value="patient"     {{if $triAdm == "patient"}}    selected="selected"{{/if}}>Tri par patient</option>
      </select>
      <select name="filter_function" style="width: 16em;" onchange="this.form.onsubmit()">
        <option value=""> &mdash; Toutes les fonctions</option>
        {{foreach from=$functions_filter item=_function}}
          <option value="{{$_function->_id}}" {{if $_function->_id == $filter_function}}selected="selected"{{/if}} class="mediuser" style="border-color: #{{$_function->color}};">{{$_function}}</option>
        {{/foreach}}
      </select>
      <label>
        <input type="checkbox" name="duree_uscpo_view" {{if $duree_uscpo}}checked="checked"{{/if}} onchange="$V(this.form.duree_uscpo, this.checked ? 1 : 0);"/>
        <input type="hidden" name="duree_uscpo" value="{{$duree_uscpo}}" onchange="this.form.onsubmit();"/>
        Durée uscpo
      </label>
      {{if $items_prestation|@count}}
        &mdash;
        Niveau de prestation :
        <select name="item_prestation_id" onchange="this.form.onsubmit();">
          <option value="">&mdash; Tous les niveaux</option>
          {{foreach from=$items_prestation item=_item}}
            <option value="{{$_item->_id}}" {{if $_item->_id == $item_prestation_id}}selected="selected"{{/if}}>{{$_item->rank}} - {{$_item}}</option>
          {{/foreach}}
        </select>
      {{/if}}
    </form>
    <div id="lit_bloque" class="clit_bloque draggable" style="display: inline-block;">
      <strong>[BLOQUER UN LIT]</strong>
    </div>
    <div id="lit_urgence" class="clit_bloque draggable" style="display: inline-block;">
      <strong>[LIT EN URGENCE]</strong>
    </div>
  </div>
</div>

<div class="small-info" id="alerte_non_places_temporel" style="display: none;">
  {{tr}}CSejour-partial_view{{/tr}}
</div>

{{if $sejours_non_affectes|@count}}
  <table class="tbl layout_temporel" style="table-layout: fixed; position: relative;" id="non_places_temporel">
    <col style="width: 15%;" />
    {{assign var=show_age_patient value=$conf.dPhospi.show_age_patient}}
    {{foreach from=$sejours_non_affectes item=_sejours_by_service key=_service_id}}
      <tr>
        <th class="title" colspan="{{$colspan}}">
          {{if $_service_id == "np"}}
            Non placés
          {{else}}
            {{$services.$_service_id}} - Couloir
          {{/if}}
        </th>
      </tr>
      {{foreach from=$_sejours_by_service item=_object}}
        {{if $_object instanceof CLit}}
          <tr class="droppable line">
            {{mb_include module=hospi template=inc_line_lit in_corridor=1 _lit=$_object}}
          </tr>
        {{else}}
          <tr class="line">
            {{assign var=patient value=$_object->_ref_patient}}
            {{assign var=praticien value=$_object->_ref_praticien}}
            {{math equation=x*y x=$_object->_width y=$td_width assign=width}}
            {{math equation=x*y x=$_object->_entree_offset y=$td_width assign=offset}}
            <th style="height: 3em;"></th>
            
            {{foreach from=0|range:$nb_ticks_r item=_i}}
              {{assign var=datetime value=$datetimes.$_i}}
              <td class="mouvement_lit {{if $datetime == $current}}current_hour{{/if}}" {{if $_i == 0}}id="wrapper_line_{{$_object->_id}}"{{/if}} style="vertical-align: top">
                {{if $_i == 0}}
                  <div class="affectation clit draggable text sejour_non_affecte {{if $_object->entree >= $date_min}}debut_sejour{{/if}}
                    {{if $_object->sortie <= $date_max}}fin_sejour{{/if}} {{$_object->_guid}}"
                    style="border: 1px solid #{{$praticien->_ref_function->color}}; width: {{$width}}%; left: {{$offset}}%; margin-left: 15.1%;"
                    id="sejour_temporel_{{$_object->_id}}"
                    data-patient_id="{{$patient->_id}}"
                    data-sejour_id="{{$_object->_id}}">
                    <span style="float: left; padding-left: 1px; padding-right: 1px;">
                      {{mb_include module=patients template=inc_vw_photo_identite mode=read patient=$patient size=22}}
                    </span>
                    <div class="wrapper_op">
                      {{if !$readonly}}
                        <span style="float: right;">
                          <input type="radio" name="sejour_move" id="sejour_move_{{$_object->_id}}" onclick="chooseSejour('{{$_object->_id}}');"/>
                        </span>
                      {{/if}}
                      
                      <span onmouseover="ObjectTooltip.createEx(this, '{{$_object->_guid}}');"
                        class="CPatient-view {{if !$_object->entree_reelle}}patient-not-arrived{{/if}} {{if $_object->septique}}septique{{/if}}"
                      {{if $_object->type == "ambu"}}style="font-style: italic;"{{/if}}>
                        
                        {{$patient->nom}} {{if $patient->nom_jeune_fille}}({{$patient->nom_jeune_fille}}) {{/if}}{{$patient->prenom}}
                        
                      </span>{{if $show_age_patient}}({{$patient->_age}}){{/if}}
                      {{if $_object->type != "ambu" && $_object->type != "exte"}}
                        ({{$_object->_duree}}j - {{$_object->_ref_praticien->_shortview}})
                      {{else}}
                        ({{$_object->type|truncate:1:""|capitalize}} - {{$_object->_ref_praticien->_shortview}})
                      {{/if}}
                      {{if $patient->_overweight}}
                        <img src="images/pictures/overweight.png" />
                      {{/if}}
                      <span style="float: right; margin-top: 3px; margin-right: 3px;">
                        {{mb_include module=patients template=inc_vw_antecedents type=deficience readonly=1}}
                      </span>
                      <br />
                      <div>
                        <span class="compact">
                          {{$_object->_motif_complet}}
                        </span>
                        <span class="compact" style="float: right;">
                          {{if $prestation_id && $_object->_curr_liaison_prestation}}
                            {{assign var=liaison value=$_object->_curr_liaison_prestation}}
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
                              {{if $_object->chambre_seule}}CS{{else}}CD{{/if}}
                              {{if $_object->prestation_id}}- {{$_object->_ref_prestation->code}}{{/if}}
                            </em>
                          {{/if}}
                        </span>
                      </div>
                      {{foreach from=$_object->_ref_operations item=_operation}}
                        {{math equation=(x/y)*100 x=$_operation->_debut_offset y=$_object->_width assign=offset_op}}
                        {{math equation=(x/y)*100 x=$_operation->_width y=$_object->_width assign=width_op}}
                        
                        <div class="operation_in_mouv opacity-40"
                          style="left: {{$offset_op}}%; width: {{$width_op}}%;"></div>
                        
                        {{if $_operation->duree_uscpo}}
                          {{math equation=x+y x=$offset_op y=$width_op assign=offset_uscpo}}
                          {{math equation=x/y*100 x=$_operation->_width_uscpo y=$_object->_width assign=width_uscpo}}
                          <div class="soins_uscpo opacity-40"
                            style="left: {{$offset_uscpo}}%; width: {{$width_uscpo}}%;"></div>
                        {{/if}}
                      {{/foreach}}
                    </div>
                  </div>
                  {{if !$readonly}}
                    <script type="text/javascript">
                      var container = $('sejour_temporel_{{$_object->_id}}');
                      new Draggable(container, {
                        constraint: 'vertical',
                        starteffect: function(element) {
                          new Effect.Opacity(element, { duration:0.2, from:1.0, to:0.7 });
                        },
                        onStart: function(drgObj, mouseEvent){
                          var element = drgObj.element;
                          element.save_left = element.getStyle("left");
                          element.save_width = element.getStyle("width");
                          var table = element.up('table')
                          var left = element.cumulativeOffset().left
                          var width = element.getWidth();
                          
                          $(document.body).insert(element);
                          element.setStyle({
                            left:       left + 'px',
                            marginLeft: '0',
                            width:      width+'px'
                          });
                            
                          {{if $prestation_id && $item_prestation_id}}
                            {{assign var=item_prestation value=$items_prestation.$item_prestation_id}}
                            $$(".first_cell").each(function(elt) {
                              var rank = {{$item_prestation->rank}};
                              var rank_elt = parseInt(elt.get('rank'));
                              var classItem = "";
                              
                              // Vert
                              if (rank == rank_elt) {
                                classItem = "item_egal";
                              }
                              // Orange
                              else if (rank < rank_elt) {
                                classItem = "item_inferior";
                              }
                              // Rouge
                              else if (rank > rank_elt) {
                                classItem = "item_superior";
                              }
                              
                              elt.addClassName(classItem);
                              elt.writeAttribute("data-classItem", classItem);
                            });
                          {{/if}}
                        },
                        onEnd: function(drbObj, mouseEvent) {
                          $$(".first_cell").each(function(elt) {
                            elt.removeClassName(elt.get('classItem'));
                          });
                          var element = drbObj.element;
                          $('wrapper_line_'+element.get('sejour_id')).insert(element);
                        },
                        reverteffect: function(element) {
                          element.style.left = element.save_left;
                          element.style.width = element.save_width;
                          element.style.marginLeft = "15.1%";
                          element.style.top = "auto";
                        },
                        revert: true});
                    </script>
                  {{/if}}
                {{/if}}
              </td>
            {{/foreach}}
          </tr>
        {{/if}}
      {{/foreach}}
    {{/foreach}}
  </table>
{{else}}
  <div class="empty">{{tr}}CSejour.none{{/tr}}</div>
{{/if}}