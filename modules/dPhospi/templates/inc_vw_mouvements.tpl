{{if !$readonly}}
  <script type="text/javascript">
    Main.add(function() {
      var time_line_temporelle = $("time_line_temporelle");
      var tableau_vue_temporelle = $("tableau_vue_temporel");
      var view_affectations = $("view_affectations");
      view_affectations.scrollTop = 0;
     
      if (Prototype.Browser.Gecko) {
        time_line_temporelle.setStyle({top: window.top_tempo});
      }
      
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
            "filter": style,
          });
        });
      }
      time_line_temporelle.setStyle({width: tableau_vue_temporelle.getWidth()+"px" }); 
      
      Event.observe(window, "resize", function(e){
        time_line_temporelle.setStyle({width: tableau_vue_temporelle.getWidth()+"px" });
      });
      
      view_affectations.select(".droppable").each(function(tr) {
         Droppables.add(tr, {
          onDrop: function(div, tr, event) {
            
            if ( !tr.isVisible(view_affectations)) return;
            
            var lit_id = tr.get("lit_id");
            
            // Création d'une affectation pour bloquer un lit
            if (div.id == "lit_bloque") {
              editAffectation(null, lit_id, 0);
            }
            else {
              if (div.id == "lit_urgence") {
                editAffectation(null, lit_id, 1);
              }
              else{
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
                  moveAffectation(affectation_id, lit_id, sejour_id, div.get("lit_id"));
                }
              }
            }
            if (div.get("lit_id") && lit_id != div.get("lit_id") && !event.ctrlKey) {
              div.remove();
            }
                       
          },
          hoverclass: "lit_hover",
          accept:'draggable'
          });
          // Bug de firefox
          tr.setStyle({position: "static"});
       });
       
       $("view_affectations").scrollTop = Placement.scrollAffectations;
       
       
       {{if "reservation"|module_active}}
         tableau_vue_temporelle.select(".mouvement_lit").each(function(elt) {
           elt.observe("dblclick", function() {
             var datetime = elt.get("date").split(" ");
             window.save_date = datetime[0];
             window.save_hour = datetime[1].split(":")[0];
             window.save_lit_guid = elt.up("tr").id;
             chooseIntervSejour();
           });
         });
       {{/if}}
    });
  </script>
{{/if}}

{{if $prestation_id}}
  {{math equation=x+2 x=$nb_ticks assign=colspan}}
{{else}}
  {{math equation=x+1 x=$nb_ticks assign=colspan}}
{{/if}}
{{math equation=x-1 x=$nb_ticks assign=nb_ticks_r}}

<div style="height: 5.4em; width: 100%;">
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
    <table class="tbl" style="table-layout: fixed;">
      {{if $prestation_id}}
        <col style="width: 5%;" />
        <col style="width: 10%;" />
      {{else}}
        <col style="width: 15%;" />
      {{/if}}
      <tr>
      {{if $granularite == "day"}}
        <th class="title" colspan="{{$colspan}}">
          {{$date|date_format:$conf.longdate}}
        </th>
      {{else}}
        <th class="title" {{if $prestation_id}}colspan="2"{{/if}}></th>
        {{foreach from=$days item=_day key=_datetime}}
        
          <th class="title" colspan="{{if $granularite == "week"}}4{{else}}7{{/if}}">
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
      <th style="text-align: left;" {{if $prestation_id}}colspan="2"{{/if}}>
        <input type="text" style="width: 7em;" onkeyup="filter(this, 'tableau_vue_temporel')" class="search" />
      </th>
      {{foreach from=$datetimes item=_date}}
        <th>
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

<div class="small-info" id="alerte_tableau_vue_temporel" style="display: none">
  {{tr}}CSejour-partial_view{{/tr}}
</div>

<table class="tbl layout_temporel" id="tableau_vue_temporel" style="table-layout: fixed; position: relative;">
  {{if $prestation_id}}
    <col style="width: 5%;" />
    <col style="width: 10%;" />
  {{else}}
    <col style="width: 15%;" />
  {{/if}}
  {{foreach from=$services item=_service}}
    <tr>
      <th class="section {{if $_service->externe}}service_externe{{/if}}" colspan="{{$colspan}}">{{$_service}}</th>
    </tr>
    {{assign var=show_age_patient value=$conf.dPhospi.show_age_patient}}
    {{foreach from=$_service->_ref_chambres item=_chambre}}
      {{foreach from=$_chambre->_ref_lits item=_lit}}
        <tr data-lit_id="{{$_lit->_id}}" id="{{$_lit->_guid}}" class="droppable line">
          {{mb_include module=hospi template=inc_line_lit}}
        </tr>
      {{/foreach}}
    {{/foreach}}
  {{/foreach}}
</table>