{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7951 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
countCodesCsarr = function() {
  var csarr_count = $V(oFormEvenementSSR.code_csarr) ? 1 : 0;
  csarr_count += oFormEvenementSSR.select('input.checkbox-csarrs:checked').length;
  csarr_count += oFormEvenementSSR.select('input.checkbox-other-csarrs').length;

  $$("input[name='type_seance']").each(function(input){
    input.disabled = csarr_count != 0 ? true : false;
  });
};

selectActivite = function(activite) {
  $V(oFormEvenementSSR.prescription_line_element_id, '');
  $V(oFormEvenementSSR._element_id, '');

  $$("button.activite").invoke("removeClassName", "selected");
  $("trigger-"+activite).addClassName("selected");

  $$("div.activite").invoke("hide");
  $("activite-"+activite).show();

  // On masque les techncien et on enleve le technicien selectionné
  $$("div.techniciens").invoke("hide").invoke("removeClassName", "selected");
  $$("button.ressource").invoke("removeClassName", "selected");
  $V(oFormEvenementSSR.therapeute_id, '');  

  // Suppression des valeurs su select de technicien
  $$("select._technicien_id").each(function(select_tech){
    $V(select_tech, '');
  });

  // Affichage des techniciens correspondants à l'activité selectionnée
  $("techniciens-"+activite).show();

  $('div_other_csarr').hide();
  $('other_csarr').hide();
  $V(oFormEvenementSSR.code_csarr, '');

  // Mise en evidence des elements dans les plannings
  addBorderEvent();
  refreshSelectSeances();
};

selectElement = function(line_id){
  $V(oFormEvenementSSR.line_id, line_id);

  $$("button.line").invoke("removeClassName", "selected");
  $("line-"+line_id).addClassName("selected");

  $("csarrs-"+line_id).show();
  $('div_other_csarr').show();

  // Deselection de tous les codes
  removeCodes();

  // Mise en evidence des elements dans les plannings
  addBorderEvent();
  refreshSelectSeances();
};

selectTechnicien = function(kine_id, buttonSelected) {
  $V(oFormEvenementSSR.therapeute_id, kine_id); 

  $$("button.ressource").invoke("removeClassName", "selected");
  if(buttonSelected){
    buttonSelected.addClassName("selected");
  }

  PlanningTechnicien.show(kine_id, null, '{{$bilan->sejour_id}}');
  if($V(oFormEvenementSSR.equipement_id)){
    PlanningEquipement.show($V(oFormEvenementSSR.equipement_id), '{{$bilan->sejour_id}}');
  }
  refreshSelectSeances();
};

selectEquipement = function(equipement_id) {
  $V(oFormEvenementSSR.equipement_id, equipement_id);
  $$("button.equipement").invoke("removeClassName", "selected");
  if ($("equipement-"+equipement_id)){
    $("equipement-"+equipement_id).addClassName("selected");
  }

  if (equipement_id) {
    PlanningEquipement.show(equipement_id,'{{$bilan->sejour_id}}');
  } 
  else {
    PlanningEquipement.hide();
  }
  refreshSelectSeances();
};

refreshSelectSeances = function(){
  if($V(oFormEvenementSSR.therapeute_id) &&
     $V(oFormEvenementSSR.line_id)
  && $V(oFormEvenementSSR.type_seance) == 'collective'){

    var url = new Url("ssr", "ajax_vw_select_seances");
    url.addParam("therapeute_id", $V(oFormEvenementSSR.therapeute_id));
    url.addParam("equipement_id", $V(oFormEvenementSSR.equipement_id));
    url.addParam("prescription_line_element_id", $V(oFormEvenementSSR.line_id));
    url.requestUpdate("select-seances", {
      onComplete: function(){ 
        $('seances').show();
        oFormEvenementSSR.seance_collective_id.show();
      }
    });
  } else {
    $('seances').hide();
    $V(oFormEvenementSSR.seance_collective_id, '');
  }
};

hideCodes = function() {
  // Deselection des codes csarrs
  $V(oFormEvenementSSR._csarr, false);
  $$('#other_csarr span').invoke('remove');
  $('other_csarr').hide();
};

removeCodes = function() {
  oFormEvenementSSR.select('input[name^="csarrs"]').each(function(e){
    e.checked = false;
  });
};

submitSSR = function(){
  // Test de la presence d'au moins un code SSR
  var csarr_count = $V(oFormEvenementSSR.code_csarr) ? 1 : 0;
  csarr_count += oFormEvenementSSR.select('input.checkbox-csarrs:checked').length;
  csarr_count += oFormEvenementSSR.select('input.checkbox-other-csarrs').length;

  if (csarr_count == 0) {
    alert("Veuillez selectionner au moins un code CsARR");
    return false;
  }

  if($V(oFormEvenementSSR.type_seance) != 'collective' || ($V(oFormEvenementSSR.type_seance) == 'collective' && !$V(oFormEvenementSSR.seance_collective_id))){
    if((oFormEvenementSSR.select('input.days:checked').length == 0)){
      alert("Veuillez selectionner au minimum un jour");
      return false;
    }
    if(!$V(oFormEvenementSSR._heure_deb)){
      alert("Veuillez selectionner une heure");
      return false;
    }
    if(!$V(oFormEvenementSSR.duree)){
      alert("Veuillez selectionner une durée");
      return false;
    }
  }

  if (oFormEvenementSSR.equipement_id) {
    if(!oFormEvenementSSR.select("button.equipement.selected").length && !$V(oFormEvenementSSR.equipement_id)){
      alert("Veuillez selectionner un equipement");
      return false;
    }
  }
  $V(oFormEvenementSSR._type_seance, $V(oFormEvenementSSR.type_seance));

  return onSubmitFormAjax(oFormEvenementSSR, { onComplete: function(){
    refreshPlanningsSSR();
    $$(".days").each(function(e){
      $V(e, '');
    });

    // Suppression des actes cdarrs selectionnés
    $V(oFormEvenementSSR._heure_deb, '');
    $V(oFormEvenementSSR._heure_deb_da, '');
    $V(oFormEvenementSSR._heure_fin, '');
    $V(oFormEvenementSSR._heure_fin_da, '');
    $V(oFormEvenementSSR.duree, $V(oFormEvenementSSR._default_duree));
    $V(oFormEvenementSSR.seance_collective_id, '');
    $V(oFormEvenementSSR.type_seance, 'dediee');
    $$("input[name='type_seance']").each(function(input){
      input.disabled = false;
    });
    if(oFormEvenementSSR.seance_collective_id){
      oFormEvenementSSR.seance_collective_id.hide();
    }

    hideCodes();

    selectElement($V(oFormEvenementSSR.line_id));
  }} );
};

refreshPlanningsSSR = function(){
  Planification.refreshSejour('{{$bilan->sejour_id}}', true);
  PlanningTechnicien.show($V(oFormEvenementSSR.therapeute_id), null, '{{$bilan->sejour_id}}');
  if($V(oFormEvenementSSR.equipement_id)){
    PlanningEquipement.show($V(oFormEvenementSSR.equipement_id),'{{$bilan->sejour_id}}');
  }
};

addBorderEvent = function(){
  // Classe des evenements à selectionner
  var category_id = $V(oFormEvenementSSR._category_id);
  var element_id  = $V(oFormEvenementSSR._element_id);
  var eventClass = (element_id) ? ".CElementPrescription-"+element_id : ".CCategoryPrescription-"+category_id;
  var planning = $('planning-sejour');

  // On ne passe pas en selected les evenements qui possedent la classe tag_cat
  if(element_id){ 
    var elements_tag = planning.select(".event.elt_selected"+eventClass+":not(.tag_cat)");
    if (planning.select(".event.elt_selected"+eventClass+".selected:not(.tag_cat)").length) {
      elements_tag.invoke("removeClassName", 'selected');
    }
    else {
      elements_tag.invoke("addClassName", 'selected');
    }
  }
  else {
    var elements = planning.select(".event.elt_selected"+eventClass);
    if (planning.select(".event.elt_selected"+eventClass+".selected").length) {
      elements.invoke("removeClassName", 'selected');
    }
    else {
      elements.invoke("addClassName", 'selected');
    }
  }

  planning.select(".event.elt_selected:not("+eventClass+")").invoke("removeClassName", 'selected');

  // Selection de tous les elements qui ont la classe spécifiée
  planning.select(".event"+eventClass).invoke("addClassName", 'elt_selected');

  // Deselection de tous les elements deja selectionnés qui n'ont pas la bonne classe
  planning.select(".event:not("+eventClass+")").invoke("removeClassName", 'elt_selected');

  // Suppression de la classe tag_cat de tous les evenements selectionnés
  planning.select(".event"+eventClass).invoke("removeClassName", 'tag_cat');

  // Si la selection a eu lieu suite au choix d'une categorie, ajout d'une classe aux evenements
  if(!element_id){
    planning.select(".event.elt_selected"+eventClass).invoke("addClassName", 'tag_cat');
  }

  // Parfois le planning n'est pas prêt

  if (planning.down('div.planning')) {
    window["planning-"+planning.down('div.planning').id].updateNbSelectEvents();
  }
};

updateModalSsr = function(){
  var oFormEvents = getForm("form_list_ssr");
  var url = new Url("ssr", "ajax_update_modal_evts_modif");
  url.addParam("token_evts", $V(oFormEvents.token_evts));
  url.requestModal(420, 400);
  modalWindow = url.modalObject;
};

onchangeSeance = function(seance_id){
  $('date-evenements').setVisible(!seance_id);
};

toggleAllDays = function(){
  var days = oFormEvenementSSR.select('input.days');
  days.slice(0,5).each(function(e){
      e.checked = true;
  });
  days.slice(5,7).each(function(e){
    e.checked = false;
});
};

var oFormEvenementSSR;
Main.add(function(){
  oFormEvenementSSR = getForm("editEvenementSSR");
  window.toCheck = false;

  // CsARR other code autocomplete
  if ($('code_csarr_autocomplete')) {
    var url = new Url("ssr", "httpreq_do_csarr_autocomplete");
    url.autoComplete(oFormEvenementSSR.code_csarr, "code_csarr_autocomplete", {
      dropdown: true,
      minChars: 2,
      select: "value",
      callback: function(input, queryString){
        return (queryString + "&type_seance="+$V(oFormEvenementSSR.type_seance));
      },
      updateElement: updateFieldCodeCsarr
    } );
  }

  // Initialisation du timePicker
  Control.Tabs.create('tabs-activites', true);

  {{if $selected_cat}}
  selectActivite('{{$selected_cat->_guid}}');
  $("technicien-{{$selected_cat->_id}}-{{$user->_id}}").onclick();
  {{/if}}

  {{if !$prescription}}
    $('div_other_csarr').show();
  {{/if}}
});

</script>

{{if !$bilan->technicien_id}} 
<div class="small-warning">
  Le patient n'a pas de 
  {{mb_label object=$bilan field=technicien_id}}
  <a class="button search" href="?&m={{$m}}&amp;tab=vw_idx_repartition">
    Me rendre à la répartition des patients
  </a>
</div>
{{/if}}

<ul id="tabs-activites" class="control_tabs small">
  <li>
    <a href="#add_ssr">{{tr}}Activities{{/tr}}</a>
  </li>
  <li>
    <a href="#outils">{{tr}}Tools{{/tr}}</a>
  </li>
</ul>

<div id="add_ssr" style="display: none;">
  <!-- Modification du bilan SSR, brancardage -->
  <form name="editBilanSSR" method="post" action="?" onsubmit="return onSubmitFormAjax(this);">
    <input type="hidden" name="m" value="ssr" />
    <input type="hidden" name="dosql" value="do_bilan_ssr_aed" />
    <input type="hidden" name="del" value="0" />
    {{mb_key object=$bilan}}

    <table class="form">
      <tr>
        <th>{{mb_label object=$bilan field=hospit_de_jour}}</th>
        <td>
          <div id="demi-journees" style="float: right; {{if !$bilan->hospit_de_jour}}display: none;{{/if}}">
            {{mb_field object=$bilan field=demi_journee_1 onchange="this.form.onsubmit();" typeEnum=checkbox}}
            {{mb_label object=$bilan field=demi_journee_1}} 
            {{mb_field object=$bilan field=demi_journee_2 onchange="this.form.onsubmit();" typeEnum=checkbox}}
            {{mb_label object=$bilan field=demi_journee_2}} 
          </div>
          <script>
            updateDemiJournees = function (input) {
              $('demi-journees').setVisible($V(input) == '1');
              input.form.onsubmit();
            }
          </script>
          {{mb_field object=$bilan field=hospit_de_jour onchange="updateDemiJournees(this)"}}
        </td>
      </tr>

      <tr>
        <th style="width: 94px">{{mb_label object=$bilan field=entree}}</th>
        <td>
          <div style="float: right;">
            {{mb_field object=$bilan field=brancardage onchange="this.form.onsubmit();" typeEnum=checkbox}}
            {{mb_label object=$bilan field=brancardage}} 
          </div>
          {{mb_value object=$bilan field=entree}}
        </td>
      </tr>
      <tr>
        <th>{{mb_label object=$bilan field=technicien_id}}</th>
        <td><strong>{{mb_value object=$bilan field=technicien_id}}</strong></td>
      </tr>
    </table>
  </form>

  <form name="editEvenementSSR" method="post" action="?" onsubmit="return submitSSR();">
    <input type="hidden" name="m" value="ssr" />
    <input type="hidden" name="dosql" value="do_evenement_ssr_multi_aed" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="sejour_id" value="{{$bilan->sejour_id}}">
    {{mb_field hidden=true object=$evenement field=therapeute_id prop="ref notNull"}}
    <input type="hidden" name="line_id" value="" />
    <input type="hidden" name="_element_id" value="" />
    <input type="hidden" name="_category_id" value="" />
    <input type="hidden" name="_type_seance" value="" />

    <table class="form">
      <tr>
        <th>{{mb_label object=$evenement field=type_seance}}</th>
        <td>
          {{mb_field object=$evenement field=type_seance type=checkbox typeEnum=radio onchange="refreshSelectSeances();"}}
        </td>
      </tr>
      {{if $prescription}}
      <tr>
        <th style="width: 94px">Catégories</th>
        <td class="text">
          {{foreach from=$prescription->_ref_prescription_lines_element_by_cat item=_lines_by_chap}}
            {{foreach from=$_lines_by_chap item=_lines_by_cat}}
              {{foreach from=$_lines_by_cat.element item=_line name=category}}
                {{if $smarty.foreach.category.first}}
                  {{assign var=category value=$_line->_ref_element_prescription->_ref_category_prescription}}
                  <button id="trigger-{{$category->_guid}}" class="none activite" type="button" 
                          onclick="$V(this.form._category_id, '{{$category->_id}}'); selectActivite('{{$category->_guid}}')">
                    {{$category}}
                  </button>
                {{/if}}
              {{/foreach}}
            {{/foreach}}
          {{/foreach}}
        </td>
      </tr>

      <tr>
        <th>Eléments</th>
        <td class="text">
          {{foreach from=$lines_by_element item=_lines_by_chap}}
            {{foreach from=$_lines_by_chap item=_lines_by_cat}}
              {{foreach from=$_lines_by_cat item=_lines_by_elt name=category}}
                {{foreach from=$_lines_by_elt item=_line name=elts}}
                  {{assign var=element value=$_line->_ref_element_prescription}}
                  {{if $smarty.foreach.category.first &&  $smarty.foreach.elts.first}}
                    {{assign var=category value=$element->_ref_category_prescription}}
                    <div class="activite" id="activite-{{$category->_guid}}" style="display: none;">
                  {{/if}}

                  {{if $smarty.foreach.elts.first && $_lines_by_elt|@count > 1}}
                   <span class="mediuser" style="font-weight: bold; border-left-color: #{{$element->_color}};" 
                          onmouseover="ObjectTooltip.createEx(this, '{{$element->_guid}}')">
                    {{$element}}
                   </span>
                   <br />
                  {{/if}}

                  <span style="float: right">
                    {{mb_include module=system template=inc_opened_interval_date from=$_line->debut to=$_line->date_arret}}
                  </span>

                  <label>
                    <div>
                      {{mb_include module=ssr template=vw_line_alerte_ssr line=$_line include_form=0 name_form="activite" see_alertes=0}}
                    </div>

                    <input type="radio" name="prescription_line_element_id" id="line-{{$_line->_id}}" class="search line" 
                           onclick="$V(this.form._element_id, '{{$_line->element_prescription_id}}'); selectElement('{{$_line->_id}}'); hideCodes();" />

                    {{if $_lines_by_elt|@count == 1}}
                     <span class="mediuser" style="font-weight: bold; border-left-color: #{{$element->_color}};" 
                            onmouseover="ObjectTooltip.createEx(this, '{{$element->_guid}}')">
                      {{$element}}
                     </span>
                    {{/if}}

                    {{if $_line->commentaire}} 
                      {{$_line->commentaire}}
                    {{/if}}
                  </label>
                  <br style="clear: both;"/>
                  {{if $smarty.foreach.category.last &&  $smarty.foreach.elts.last}}
                    </div>
                  {{/if}}
                {{/foreach}}
              {{/foreach}}
            {{/foreach}}
          {{/foreach}}      
        </td>
      </tr>
      {{/if}}

      <tr id='tr-csarrs'>
        <th>Codes CsARR</th>
        <td class="text">
          <button type="button" class="add" onclick="$('remarque_ssr').toggle(); this.form.remarque.focus();" style="float: right">Remarque</button>

          {{if $prescription}}
            <!-- Affichage des codes csarrs -->
            {{foreach from=$prescription->_ref_prescription_lines_element_by_cat item=_lines_by_chap}}
              {{foreach from=$_lines_by_chap item=_lines_by_cat}}
                {{foreach from=$_lines_by_cat.element item=_line}}

                  <div id="csarrs-{{$_line->_id}}" style="display : none;">
                    {{foreach from=$_line->_ref_element_prescription->_ref_csarrs item=_csarr}}
                      <label>
                        <input type="checkbox" class="checkbox-csarrs nocheck" name="csarrs[{{$_csarr->code}}]" value="{{$_csarr->code}}" onchange="countCodesCsarr();"/>
                        <span onmouseover="ObjectTooltip.createEx(this, '{{$_csarr->_guid}}')">
                          {{$_csarr->code}}
                        </span>
                      </label>
                      {{/foreach}}
                      </div>
                  </div>

                {{/foreach}}
              {{/foreach}}
            {{/foreach}}
          {{/if}} 

          <!-- Autre code CsARR -->
          <div id="div_other_csarr" style="display: none;">
            <label>
              <input type="checkbox" name="_csarr" value="other" onclick="toggleOtherCsarr(this);" /> Autre:
            </label>
            <span id="other_csarr" style="display: block;">
               <input type="text" name="code_csarr" class="autocomplete" canNull=true size="2" />
               <div style="display: none;" class="autocomplete" id="code_csarr_autocomplete"></div>
            </span>
          </div>
        </td>
      </tr> 

      <tr id="remarque_ssr" style="display: none;">
        <th>{{mb_label object=$evenement field=remarque}}</th>
        <td>{{mb_field object=$evenement field=remarque}}</td>
      </tr>

      <tr>
        <th>{{mb_label object=$evenement field=therapeute_id}}</th>
        <td class="text">
          {{if $prescription}}

          {{foreach from=$prescription->_ref_prescription_lines_element_by_cat item=_lines_by_chap}}
            {{foreach from=$_lines_by_chap item=_lines_by_cat}}
              {{foreach from=$_lines_by_cat.element item=_line name=foreach_category}}
                {{assign var=element value=$_line->_ref_element_prescription}}

                {{if $smarty.foreach.foreach_category.first}}
                  {{assign var=category value=$element->_ref_category_prescription}}
                  {{assign var=category_id value=$category->_id}}

                   <div class="techniciens" id="techniciens-{{$category->_guid}}" style="display: none;">
                     {{if array_key_exists($category_id, $executants)}}
                       {{assign var=list_executants value=$executants.$category_id}}
                       {{if array_key_exists($user->_id, $list_executants)}}

                       {{assign var=current_user_id value=$user->_id}}
                       {{assign var=current_user value=$list_executants.$current_user_id}}

                       <button title="{{$current_user->_view}}" id="technicien-{{$category_id}}-{{$user->_id}}" class="none ressource" type="button" onclick="selectTechnicien('{{$current_user->_id}}', this)">
                         {{$current_user->_user_last_name}}
                       </button>     
                     {{/if}}

                     {{if $can->admin}}
                       <select class="_technicien_id" onchange="selectTechnicien(this.value)">
                         <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                         {{mb_include module=mediusers template=inc_options_mediuser list=$executants.$category_id}}
                       </select>
                     {{/if}}

                    {{else}}
                      <div class="small-warning">
                        Aucun exécutant n'est disponible pour cette catégorie
                      </div>
                    {{/if}}
                  </div>
                {{/if}}

              {{/foreach}}
            {{/foreach}}
          {{/foreach}}

          {{else}}
            {{if $can->edit}}
            <select class="_technicien_id" onchange="selectTechnicien(this.value)">
              <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
              {{mb_include module=mediusers template=inc_options_mediuser list=$executants selected=$user->_id}}
            </select>
            {{/if}}
          {{/if}}  
        </td>
      </tr>

      {{if $app->user_prefs.ssr_planification_show_equipement}} 
      <tr>
        <th>
          {{mb_label object=$evenement field=equipement_id}}
          {{mb_field object=$evenement field=equipement_id hidden=true}}
        </th>
        <td class="text">
          {{foreach from=$plateau->_ref_equipements item=_equipement}}
          <button id="equipement-{{$_equipement->_id}}" class="none equipement" type="button" onclick="$V(getForm('editEvenementSSR')._equipement_id, ''); selectEquipement('{{$_equipement->_id}}');">
            {{$_equipement}}
          </button>
          {{/foreach}}
          <button id="equipement-" type="button" class="cancel equipement" onclick="$V(getForm('editEvenementSSR')._equipement_id, ''); selectEquipement(''); ">Aucun</button>

          <select name="_equipement_id" onchange="selectEquipement(this.value);" style="width: 6em;">
            <option value="">&mdash; {{tr}}Other{{/tr}}</option>
            {{foreach from=$plateaux item=_plateau}}
              {{if $_plateau->_id != $plateau->_id}}
                <optgroup label="{{$_plateau->_view}}">
                {{foreach from=$_plateau->_ref_equipements item=_equipement}}
                  <option value="{{$_equipement->_id}}">{{$_equipement->_view}}</option>
                {{/foreach}}
                </optgroup>
              {{/if}}
            {{/foreach}}
          </select>
        </td>
      </tr>
      {{/if}}

      <tr id="seances" style="display: none;">
        <th>{{mb_label object=$evenement field="seance_collective_id"}}</th>
        <td id="select-seances"></td>
      </tr>

      <tbody id="date-evenements">
        <tr>
          <th style="vertical-align: middle;">Jour</th>
          <td style="text-align: center;">
            <table>
              <tr>
                {{foreach from=$week_days key=_number item=_day}}
                  <td>
                    <label>
                      {{$_day}}<br />
                      <input class="days nocheck" type="checkbox" name="_days[{{$_number}}]" value="{{$_number}}" />
                    </label>
                  </td>
                {{/foreach}}
                <td style="padding-left: 3em; text-align: center;">
                  <label style="float: right;">
                    <button type="button" onclick="toggleAllDays();">{{tr}}Week{{/tr}}</button>
                  </label>
                </td>
              </tr>
            </table>
          </td>
        </tr> 
        <tr>
          <th>
            {{mb_label object=$evenement field=_heure_deb}} / 
            {{mb_label object=$evenement field=duree}} /
            {{mb_label object=$evenement field=_heure_fin}}
          </th>
          <td>
            <script type="text/javascript">
              updateDuree = function(form) {
                if ($V(form._heure_deb) && $V(form._heure_fin)) {
                  var timeDeb = Date.fromDATETIME("2001-01-01 " + $V(form._heure_deb));
                  var timeFin = Date.fromDATETIME("2001-01-01 " + $V(form._heure_fin));
                  $V(form.duree, (timeFin-timeDeb) / Date.minute, false);
                }

                if (!$V(form._heure_fin)) {
                  updateHeureFin(form);
                }
              }

              updateHeureFin = function(form) {
                if ($V(form._heure_deb) && $V(form.duree)) {
                  var time = Date.fromDATETIME("2001-01-01 " + $V(form._heure_deb));
                  time.addMinutes($V(form.duree));
                  $V(form._heure_fin   , time.toTIME(), false);
                  $V(form._heure_fin_da, time.toLocaleTime(), false);
                }
              }
            </script>
            <input name="_default_duree" type="hidden" value="{{$evenement->duree}}"/>
            {{mb_field object=$evenement form=editEvenementSSR field=_heure_deb onchange="updateDuree(this.form)"}}
            {{mb_field object=$evenement form=editEvenementSSR field=duree increment=1 size=2 step=10 onchange="updateHeureFin(this.form)"}}
            {{mb_field object=$evenement form=editEvenementSSR field=_heure_fin onchange="updateDuree(this.form)"}}
          </td>
        </tr>
      </tbody>
      <tr>
        <td colspan="2" class="button">
          <button type="submit" class="submit singleclick">{{tr}}Save{{/tr}}</button>
        </td>
      </tr>
    </table>
  </form>
</div>

<div class="activite">
{{foreach from=$lines_by_element item=_lines_by_chap}}
  {{foreach from=$_lines_by_chap item=_lines_by_cat}}
    {{foreach from=$_lines_by_cat item=_lines_by_elt name=category}}
      {{foreach from=$_lines_by_elt item=_line name=elts}}
        {{mb_include module=ssr template=vw_line_alerte_ssr line=$_line include_form=0 see_alertes=1 name_form="activite"}}
      {{/foreach}}
    {{/foreach}}
  {{/foreach}}
{{/foreach}}
</div>

<div id="outils" style="display: none;">

  <script type="text/javascript">

    updateSelectedEvents = function(input_elements){
      $V(input_elements, '');
      var tab_selected = new TokenField(input_elements); 
      $$(".event.selected").each(function(e){
        if(e.className.match(/CEvenementSSR-([0-9]+)/)){
         var evt_id = e.className.match(/CEvenementSSR-([0-9]+)/)[1];
         tab_selected.add(evt_id);
        }
      });
    }

    resetFormSSR = function(){
      var oForm = getForm('editSelectedEvent');
      $V(oForm.del, '0');
      $V(oForm._nb_decalage_min_debut, '');
      $V(oForm._nb_decalage_heure_debut, '');
      $V(oForm._nb_decalage_jour_debut, '');
      $V(oForm._nb_decalage_duree, ''); 
      $V(oForm.kine_id, ''); 
      $V(oForm.equipement_id, ''); 
    }

    toggleOtherCsarr = function(elem) {
      var toggle = $V(elem);

      $('other_csarr').setVisible(toggle); 
      $V(elem.form.code_csarr, '');
      $(elem.form.code_csarr).tryFocus();
    }

    updateFieldCodeCsarr = function(selected, input) {
      var code_selected = selected.childElements()[0];
      $('other_csarr').insert({bottom: 
        DOM.span({}, 
          DOM.input({
            type: 'hidden', 
            id: 'editEvenementSSR__csarrs['+code_selected.innerHTML+']',
            name:'_csarrs['+code_selected.innerHTML+']',
            value: code_selected.innerHTML,
            className: 'checkbox-other-csarrs'
          }),
          DOM.button({
            className: "cancel notext", 
            type: "button",
            onclick: "deleteCode(this)"
          }),
          DOM.label({}, code_selected.innerHTML)
        )
      });

      var input = $('editEvenementSSR_code_csarr');
      input.value = '';
      input.tryFocus();
      countCodesCsarr();
    }


    deleteCode = function(elem) {
      $(elem).up().remove();
      countCodesCsarr();
    }

    onSubmitSelectedEvents = function(form) {
      updateSelectedEvents(form.event_ids);
      var values = new TokenField(form.event_ids).getValues();

      // Sélection vide
      if (!values.length) {
        alert($T('CEvenementSSR-alert-selection_empty'));
        return;
      }

      // Suppression multiple
      if ($V(form.del) == '1' && values.length > 1) {
        if (!confirm($T('CEvenementSSR-msg-confirm-multidelete', values.length) + $T('confirm-ask-continue'))) {
          return;
        }
      }

      // Envoi du formulaire
      return onSubmitFormAjax(form, { onComplete: function() {
        refreshPlanningsSSR(); 
        resetFormSSR(); 
      } } );
    }
  </script> 

  <form name="editSelectedEvent" method="post" action="?" onsubmit="return onSubmitSelectedEvents(this)">
    <input type="hidden" name="m" value="ssr" />
    <input type="hidden" name="dosql" value="do_modify_evenements_aed" />
    <input type="hidden" name="event_ids" value="" />
    <input type="hidden" name="del" value="0" />    
    <input type="hidden" name="sejour_id" value="{{$bilan->sejour_id}}">
    <table class="form">
     <tr>
        <th class="category" colspan="2">
          Modification / Suppression
        </th>
      </tr>
      <tr>
        <td>
          Déplacer de {{mb_field object=$evenement field="_nb_decalage_min_debut" form="editSelectedEvent" increment=1 size=2 step=10}} minutes
        </td>
        <td>
          Modifier la durée de {{mb_field object=$evenement field="_nb_decalage_duree" form="editSelectedEvent" increment=1 size=2 step=10}} minutes
        </td>
      </tr>
      <tr>
        <td>
          Déplacer de {{mb_field object=$evenement field="_nb_decalage_heure_debut" form="editSelectedEvent" increment=1 size=2}} heures
        </td>
        <td>
          Transférer vers 
          <select name="kine_id" style="width: 12em;">
            <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
           {{mb_include module=mediusers template=inc_options_mediuser list=$reeducateurs}}
          </select>
        </td>
      </tr>         
      <tr>
        <td>
          Déplacer de {{mb_field object=$evenement field="_nb_decalage_jour_debut" form="editSelectedEvent" increment=1 size=2}} jours
        </td>
        <td>
          Transférer vers 
          <select name="equipement_id" style="width: 12em;">
            <option value="">&mdash; Equipement</option>
            <option value="none">{{tr}}CEquipement.none{{/tr}}</option>
            {{foreach from=$plateaux item=_plateau}}
              <optgroup label="{{$_plateau->_view}}">
              {{foreach from=$_plateau->_ref_equipements item=_equipement}}
                <option value="{{$_equipement->_id}}">{{$_equipement}}</option>
              {{/foreach}}
              </optgroup>
            {{/foreach}}
          </select>
        </td>
      </tr>
      <tr>
        <td class="button" colspan="2">
          <button type="button" onclick="$V(this.form.del, '0'); this.form.onsubmit();" class="submit">
            {{tr}}Modify{{/tr}}
          </button>
          <button type="button" name="delete" class="trash" onclick="$V(this.form.del, '1'); this.form.onsubmit();">
            {{tr}}Delete{{/tr}}
          </button>
        </td>
      </tr> 
    </table>
  </form>

  <form name="duplicateSelectedEvent" method="post" action="?" onsubmit="return onSubmitSelectedEvents(this)">
    <input type="hidden" name="m" value="ssr" />
    <input type="hidden" name="dosql" value="do_duplicate_evenements_aed" />
    <input type="hidden" name="event_ids" value="" /> 
    <input type="hidden" name="propagate" value="" />
    <table class="form">
      <tr>
        <th colspan="2" class="category">
          Duplication / Propagation
        </th>
      </tr>

      <tr>
        <th>
          <select name="period">
            <option value="+1 WEEK">{{tr}}Week-after{{/tr}}</option>
            <option value="+1 DAY" >{{tr}}Day-after{{/tr}} </option>
            <option value="-1 DAY" >{{tr}}Day-before{{/tr}}</option>
          </select>
        </th>
        <td class="button">
          <button type="button" class="duplicate singleclick" onclick="$V(this.form.propagate, '0'); this.form.onsubmit();">
            {{tr}}Duplicate{{/tr}}
          </button>
        </td>
      </tr> 

      <tr>
        <th>
          <table style="float: right;">
            <tr>
              {{foreach from=$week_days key=_number item=_day}}
                <td>
                  <label>
                    {{$_day}}<br />
                    <input class="days nocheck" type="checkbox" name="_days[{{$_number}}]" value="{{$_number}}" />
                  </label>
                </td>
              {{/foreach}}
            </tr>
          </table>
        </th>
        <td class="button">
          <button type="button" class="new singleclick" onclick="$V(this.form.propagate, '1'); this.form.onsubmit();">
            {{tr}}Propagate{{/tr}}
          </button>
        </td>
      </tr>
    </table>
  </form>

  <!-- TODO: utiliser le meme formulaire pour stocker le token d'evenements pour les differentes actions  -->
  <form name="form_list_ssr" method="post">
    <input type="hidden" name="token_evts" />
  </form>

  <table class="form">
    <tr>
      <th class="category">Codes</th>
    </tr>
    <tr>
      <td class="button">
        <button type="button" class="submit" onclick="updateSelectedEvents(getForm('form_list_ssr').token_evts); updateModalSsr();">Modifier les codes</button>
      </td>
    </tr>
  </table>
</div>