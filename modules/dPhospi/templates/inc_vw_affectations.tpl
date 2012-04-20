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
    var first_th = $("tableau_vue_temporel").down("tr", 1).down("th");
    var width_th = parseInt(first_th.getStyle("width"));
    if (first_th.next().hasClassName("first_cell")) {
       width_th += parseInt(first_th.next().getStyle("width"))+5;
    }
    list_affectations.scrollTop = 0;
    $$(".first_th").each(function(th) {
      th.setStyle({minWidth: width_th+"px"});
    });
    
    if (Prototype.Browser.Gecko) {
      var top_tempo = time_line_temporelle_non_affectes.getStyle("top");
      time_line_temporelle_non_affectes.setStyle({top: top_tempo});
    }
    
    if (!Prototype.Browser.IE) {
      //time_line_temporelle_non_affectes.setStyle({width: (parseInt(list_affectations.getStyle("width")) - 15)+'px'});
      list_affectations.on('scroll', function() {
        time_line_temporelle_non_affectes.setClassName('scroll_shadow', list_affectations.scrollTop);
      });
      list_affectations.fire('scroll');
    }
    $("list_affectations").scrollTop = Placement.scrollNonPlaces;
  });
</script>

{{if $prestation_id}}
  {{if $granularite == "day"}}
    {{assign var=td_width value=36}}
  {{else}}
    {{assign var=td_width value=29}}
  {{/if}}
{{else}}
  {{if $granularite == "day"}}
    {{assign var=td_width value=37}}
  {{else}}
    {{assign var=td_width value=30}}
  {{/if}}
{{/if}}
{{if $prestation_id}}
  {{math equation=x+2 x=$nb_ticks assign=colspan}}
{{else}}
  {{math equation=x+1 x=$nb_ticks assign=colspan}}
{{/if}}
{{math equation=x-1 x=$nb_ticks assign=nb_ticks_r}}

<div style="height: 5em; width: 100%">
  <div id="time_line_temporelle_non_affectes" style="background: #fff; position: absolute; z-index: 200;">
    <form name="chgFilter" action="?" method="get" onsubmit="return onSubmitFormAjax(this,null, 'list_affectations');">
      <input type="hidden" name="m" value="dPhospi" />
      <input type="hidden" name="a" value="ajax_vw_affectations" />
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

{{if $sejours_non_affectes|@count}}
  <table class="tbl" style="width: auto; table-layout: fixed;">
    <tr>
      <th class="title" colspan="{{$colspan}}">Non placés</th>
    </tr>
    {{assign var=show_age_patient value=$conf.dPhospi.show_age_patient}}
    {{foreach from=$sejours_non_affectes item=_sejour}}
      <tr>
        {{assign var=patient value=$_sejour->_ref_patient}}
        {{assign var=praticien value=$_sejour->_ref_praticien}}
        {{math equation=x*(y+4.6) x=$_sejour->_width y=$td_width assign=width}}
        {{math equation=x*(y+4.6) x=$_sejour->_entree_offset y=$td_width assign=offset}}
        <th style="height: 3em;" class="first_th"></th>
        
        {{foreach from=0|range:$nb_ticks_r item=_i}}
          {{assign var=datetime value=$datetimes.$_i}}
          <td class="mouvement_lit {{if $datetime == $current}}current_hour{{/if}}" style="min-width: {{$td_width}}px;">
            {{if $_i == 0}}
              <div class="wrapper_line" id="wrapper_line_{{$_sejour->_id}}">
                <div class="affectation clit draggable text sejour_non_affecte {{if $_sejour->entree >= $date_min}}debut_sejour{{/if}}
                  {{if $_sejour->sortie <= $date_max}}fin_sejour{{/if}} {{$_sejour->_guid}}"
                  style="border: 1px solid #{{$praticien->_ref_function->color}}; width: {{$width}}px; left: {{$offset}}px;"
                  id="sejour_temporel_{{$_sejour->_id}}" data-patient_id="{{$patient->_id}}" data-sejour_id="{{$_sejour->_id}}"
                  onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">
                  <span style="float: left; padding-left: 1px; padding-right: 1px;">
                    {{mb_include module=patients template=inc_vw_photo_identite mode=read patient=$patient size=22}}
                  </span>
                  <div class="wrapper_op">
                    {{if !$readonly}}
                      <span style="float: right;">
                        <input type="radio" name="sejour_move" id="sejour_move_{{$_sejour->_id}}" onclick="chooseSejour('{{$_sejour->_id}}');"/>
                      </span>
                    {{/if}}
                    <span {{if !$_sejour->entree_reelle}}class="patient-not-arrived"{{/if}} {{if $_sejour->septique}}class="septique"{{/if}}
                    {{if $_sejour->type == "ambu"}}style="font-style: italic;"{{/if}}>
                      {{$patient->nom}} {{$patient->prenom}}
                    </span> {{if $show_age_patient}}({{$patient->_age}} ans){{/if}}
                    {{if $_sejour->type != "ambu" && $_sejour->type != "exte"}}
                      ({{$_sejour->_duree}}j - {{$_sejour->_ref_praticien->_shortview}})
                    {{else}}
                      ({{$_sejour->type|truncate:1:""|capitalize}} - {{$_sejour->_ref_praticien->_shortview}})
                    {{/if}}
                    <span style="float: right; margin-top: 3px; margin-right: 3px;">
                      {{mb_include module=patients template=inc_vw_antecedents type=deficience readonly=1}}
                    </span>
                    <br />
                    <div>
                      <span class="compact">
                        {{$_sejour->_motif_complet}}
                      </span>
                      <span class="compact" style="float: right;">
                      <em style="color: #f00;" title="Chambre seule">
                        {{if $_sejour->chambre_seule}}CS{{else}}CD{{/if}}
                        {{if $_sejour->prestation_id}}- {{$_sejour->_ref_prestation->code}}{{/if}}
                      </em>
                      </span>
                    </div>
                    {{foreach from=$_sejour->_ref_operations item=_operation}}
                      {{math equation=x*(y+4.6) x=$_operation->_debut_offset y=$td_width assign=offset_op}}
                      {{math equation=x*(y+4.6) x=$_operation->_width y=$td_width assign=width_op}}
                      <div class="operation_in_mouv opacity-40"
                        style="left: {{$offset_op}}px; width: {{$width_op}}px;"></div>
                      
                      {{if $_operation->duree_uscpo}}
                        {{math equation=x+y x=$offset_op y=$width_op assign=offset_uscpo}}
                        {{math equation=x*(y+4.6) x=$_operation->_width_uscpo y=$td_width assign=width_uscpo}}
                        
                        <div class="soins_uscpo opacity-40"
                          style="left: {{$offset_uscpo}}px; width: {{$width_uscpo}}px;"></div>
                      {{/if}}
                    {{/foreach}}
                  </div>
                </div>
              </div>
              {{if !$readonly}}
                <script type="text/javascript">
                  var container = $('sejour_temporel_{{$_sejour->_id}}');
                  new Draggable(container, {
                    constraint: 'vertical',
                    starteffect: function(element) {
                      new Effect.Opacity(element, { duration:0.2, from:1.0, to:0.7 });
                    },
                    onStart: function(drgObj, mouseEvent){
                      window.save_height_list = $("list_affectations").getStyle('height');
                      var element = drgObj.element;
                      element.setStyle({
                        left: element.getOffsetParent().cumulativeOffset().left+parseInt(element.style.left)+'px',
                        top: element.getOffsetParent().cumulativeOffset().top+parseInt(element.style.top)+'px'
                        });
                      $(document.body).insert(element);
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
                    revert: true});
                </script>
              {{/if}}
            {{/if}}
          </td>
        {{/foreach}}
      </tr>
    {{/foreach}}
  </table>
{{else}}
  <div class="empty">{{tr}}CSejour.none{{/tr}}</div>
{{/if}}