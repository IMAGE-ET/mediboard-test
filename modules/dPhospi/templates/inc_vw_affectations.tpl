<script type="text/javascript">
  {{if !$readonly}}
    var container = $('lit_bloque');
    new Draggable(container, {
                  ghosting: "true",
                  starteffect: function(element) {
                    new Effect.Opacity(element, { duration:0.2, from:1.0, to:0.7 });
                  },
                  revert: true});
  {{/if}}
  
  Main.add(function() {
  
    var time_line_temporelle_non_affectes = $("time_line_temporelle_non_affectes");
    var list_affectations = $("list_affectations");
    var width_th = $("tableau_vue_temporel").down("tr", 1).down("th").getStyle("width");
    list_affectations.scrollTop = 0;
    $$(".first_th").each(function(th) {
      th.setStyle({width: width_th});
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
  });
</script>

{{if $granularite == "day"}}
  {{assign var=td_width value=37}}
{{else}}
  {{assign var=td_width value=30}}
{{/if}}
{{math equation=x+1 x=$nb_ticks assign=colspan}}
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
        Durée uscpo &mdash;
      </label>
    </form>
    <div id="lit_bloque" class="clit_bloque draggable" style="display: inline-block;">
    <strong>[BLOQUER UN LIT]</strong>
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
                  {{if $_sejour->sortie <= $date_max}}fin_sejour{{/if}}"
                  style="border: 1px solid #{{$praticien->_ref_function->color}}; width: {{$width}}px; left: {{$offset}}px;"
                  id="sejour_temporel_{{$_sejour->_id}}" data-patient_id="{{$patient->_id}}" data-sejour_id="{{$_sejour->_id}}"
                  onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">
                  <span style="float: left; padding-left: 1px; padding-right: 1px;">
                    {{mb_include module=dPpatients template=inc_vw_photo_identite mode=read patient=$patient size=22}}
                  </span>
                  <div class="wrapper_op">
                    {{if !$readonly}}
                      <span style="float: right;">
                        <input type="radio" name="sejour_move" id="sejour_move_{{$_sejour->_id}}" onchange="chooseSejour('{{$_sejour->_id}}');"/>
                      </span>
                    {{/if}}
                    {{$patient->nom}} {{$patient->prenom}} {{if $show_age_patient}}({{$patient->_age}} ans){{/if}}
                    {{if $_sejour->type != "ambu" && $_sejour->type != "exte"}}
                      ({{$_sejour->_duree}}j - {{$_sejour->_ref_praticien->_shortview}})
                    {{else}}
                      ({{$_sejour->type|truncate:1:""|capitalize}} - {{$_sejour->_ref_praticien->_shortview}})
                    {{/if}}
                    <br />
                    <span class="compact">
                      {{$_sejour->_motif_complet}}
                    </span>
                    {{foreach from=$_sejour->_ref_operations item=_operation}}
                      {{math equation=x*(y+4.6) x=$_operation->_debut_offset y=$td_width assign=offset_op}}
                      {{math equation=x*(y+4.6) x=$_operation->_width y=$td_width assign=width_op}}
                      <div class="operation_in_mouv opacity-40"
                        style="left: {{$offset_op}}px; width: {{$width_op}}px;"></div>
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
                    },
                    onEnd: function(drbObj, mouseEvent) {
                      var element = drbObj.element;
                      $('wrapper_line_'+element.get('sejour_id')).insert(element);
                    },
                    revert: true,
                    ghosting: true});
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