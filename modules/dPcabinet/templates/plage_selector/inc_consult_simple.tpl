{{*
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
  resetPlage = function(id) {
    var oForm = getForm(window.PlageConsultSelector.sForm);
    $V(oForm["rques_"+id], "");
    $V(oForm["plage_id_"+id], "");
    $V(oForm["date_"+id], "");
    $V(oForm["heure_"+id], "");
    $V(oForm["chir_id_"+id], "");
    $V(oForm["_consult"+id], "");
  };

  Main.add(function () {
    var form = getForm("editFrm");
    var url = new Url("system", "ajax_seek_autocomplete");
    url.addParam("object_class", "CPatient");
    url.addParam("field", "patient_id");
    url.addParam("view_field", "_pat_name");
    url.addParam("input_field", "_seek_patient");
    url.autoComplete(form.elements._seek_patient, null, {
      minChars: 3,
      method: "get",
      select: "view",
      dropdown: false,
      width: "300px",
      afterUpdateElement: function(field,selected){
        $V(field.form.patient_id, selected.getAttribute("id").split("-")[2]);
        $V(field.form.elements._pat_name, selected.down('.view').innerHTML);
        $V(field.form.elements._seek_patient, "");
      }
    });
    Event.observe(form.elements._seek_patient, 'keydown', PatSelector.cancelFastSearch);
  });
</script>

{{if !$dialog}}
  <a class="button new" href="?m={{$m}}&amp;tab={{$tab}}&amp;consultation_id=0">
    {{tr}}CConsultation-title-create{{/tr}}
  </a>
{{/if}}

<form name="editFrm" action="?m={{$m}}" class="watched" method="post" onsubmit="return checkFormRDV(this)">
  <input type="hidden" name="dosql" value="do_consultation_aed" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$consult}}
  {{if $dialog}}
    <input type="hidden" name="postRedirect" value="m=cabinet&a=edit_planning&dialog=1" />
  {{/if}}
  <input type="hidden" name="adresse_par_prat_id" value="{{$consult->adresse_par_prat_id}}" />
  <input type="hidden" name="annule" value="{{$consult->annule|default:"0"}}" />
  <input type="hidden" name="arrivee" value="" />
  <input type="hidden" name="chrono" value="{{$consult|const:'PLANIFIE'}}" />
  <input type="hidden" name="_operation_id" value="" />
  {{mb_field object=$consult field=sejour_id hidden=1}}
  <input type="hidden" name="_force_create_sejour" value="0" />
  <input type="hidden" name="_line_element_id" value="{{$line_element_id}}" />


  {{if $consult->_id}}
    <a class="button search" href="?m={{$m}}&amp;tab=edit_consultation&amp;selConsult={{$consult->_id}}" style="float: right;">
      {{tr}}CConsultation-title-access{{/tr}}
    </a>
  {{/if}}

  {{if !$consult->_id && $line_element_id && !$nb_plages}}
    <div class="small-warning">
      Aucune plage de consultation présente pour l'exécutant sélectionné
    </div>
  {{/if}}

  <table class="form">
  <tr>
    {{if $consult->_id}}
      <th class="title modify" colspan="5">
        {{mb_include module=system template=inc_object_notes      object=$consult}}
        {{mb_include module=system template=inc_object_idsante400 object=$consult}}
        {{mb_include module=system template=inc_object_history    object=$consult}}
        {{tr}}CConsultation-title-modify{{/tr}}
        {{if $pat->_id}}de {{$pat->_view}}{{/if}}
        par le Dr {{$chir}}
      </th>
    {{else}}
      <th class="title" colspan="5">{{tr}}CConsultation-title-create{{/tr}}</th>
    {{/if}}
  </tr>
  {{if $consult->annule == 1}}
    <tr>
      <th class="category cancelled" colspan="3">{{tr}}CConsultation-annule{{/tr}}</th>
    </tr>
  {{/if}}

  {{if $consult->_locks}}
    <tr>
      <td colspan="3">
        {{if $can->admin}}
        <div class="small-warning">
          Attention, vous êtes en train de modifier une consultation ayant :
          {{else}}
          <div class="small-info">
            <input type="hidden" name="_locked" value="1" />
            Vous ne pouvez pas modifier la consultation pour les raisons suivantes  (consulter un administrateur pour plus de renseignements) :
            {{/if}}

            <ul>
              {{if in_array("datetime", $consult->_locks)}}
                <li>le rendez-vous <strong>passé de {{mb_value object=$consult field=_datetime format=relative}}</strong></li>
              {{/if}}

              {{if in_array("termine", $consult->_locks)}}
                <li>la consultation <strong>notée terminée</strong></li>
              {{/if}}

              {{if in_array("valide", $consult->_locks)}}
                <li>la cotation <strong>validée</strong></li>
              {{/if}}

            </ul>
          </div>
      </td>
    </tr>
  {{elseif $consult->_id && $consult->_datetime|iso_date == $today}}
    <tr>
      <td colspan="3">
        <div class="small-warning">
          Attention, vous êtes en train de modifier
          <strong>une consultation du jour</strong>.
        </div>
      </td>
    </tr>
  {{/if}}


  <tr>
  <td style="width: 50%;">
    <fieldset>
      <legend>Informations sur la consultation</legend>
      <table class="form">

        <tr>
          <th class="narrow">
            <label for="chir_id" title="Praticien pour la consultation">Praticien</label>
          </th>
          <td>
            <select name="chir_id" style="width: 15em;" class="notNull"
                    onChange="ClearRDV(); refreshListCategorie(this.value); refreshFunction(this.value);
                          if (this.value != '') {
                            $V(this.form._function_id, '');
                          }">
              <option value="">&mdash; Choisir un praticien</option>
              {{foreach from=$listPraticiens item=curr_praticien}}
                <option class="mediuser" style="border-color: #{{$curr_praticien->_ref_function->color}};" value="{{$curr_praticien->user_id}}"
                  {{if $chir->_id == $curr_praticien->user_id}} selected="selected" {{/if}} data-facturable="{{$curr_praticien->_ref_function->facturable}}">
                  {{$curr_praticien->_view}}
                  {{if $app->user_prefs.viewFunctionPrats}}
                    - {{$curr_praticien->_ref_function->_view}}
                  {{/if}}
                </option>
              {{/foreach}}
            </select>
            <input type="checkbox" name="_pause" value="1" onclick="changePause()" {{if $consult->_id && $consult->patient_id==0}} checked="checked" {{/if}} {{if $attach_consult_sejour && $consult->_id}}disabled="disabled"{{/if}}/>
            <label for="_pause" title="Planification d'une pause">Pause</label>
          </td>
        </tr>
        {{if !$consult->_id && $conf.dPcabinet.CConsultation.create_consult_sejour}}
          <tr>
            <th>
              {{mb_label object=$consult field=_function_secondary_id}}
            </th>
            <td id="secondary_functions">
              {{mb_include module=cabinet template=inc_refresh_secondary_functions}}
            </td>
          </tr>
        {{/if}}
        <tr id="viewPatient" {{if $consult->_id && $consult->patient_id==0}}style="display:none;"{{/if}}>
          <th>
            {{mb_label object=$consult field="patient_id"}}
          </th>
          <td>
            {{mb_field object=$pat field="patient_id" hidden=1 ondblclick="PatSelector.init()" onchange="requestInfoPat(); $('button-edit-patient').setVisible(this.value);"}}
            <input type="text" name="_pat_name" style="width: 15em;" value="{{$pat->_view}}" readonly="readonly" onfocus="PatSelector.init()" onchange="checkCorrespondantMedical()"/>
            <button class="search notext" type="button" onclick="PatSelector.init()">{{tr}}Search{{/tr}}</button>
            <button id="button-edit-patient" type="button"
                    onclick="location.href='?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id='+this.form.patient_id.value"
                    class="edit notext" {{if !$pat->_id}}style="display: none;"{{/if}}>
              {{tr}}Edit{{/tr}}
            </button>
            <br />
            <input type="text" name="_seek_patient" style="width: 13em;" placeholder="{{tr}}fast-search{{/tr}}" "autocomplete" onblur="$V(this, '')" />
          </td>
        </tr>

        <tr>
          <th>{{mb_label object=$consult field="motif"}}</th>
          <td>
            {{mb_field object=$consult field="motif" class="autocomplete" rows=5 form="editFrm"}}
          </td>
        </tr>

        <tr>
          <th>{{mb_label object=$consult field="rques"}}</th>
          <td>
            {{mb_field object=$consult field="rques" class="autocomplete" rows=5 form="editFrm"}}
          </td>
        </tr>

        {{if $consult->sejour_id}}
          <tr>
            <th>{{mb_label object=$consult field="brancardage"}}</th>
            <td>
              {{mb_field object=$consult field="brancardage" class="autocomplete" rows=5 form="editFrm"}}
            </td>
          </tr>
        {{/if}}
      </table>
    </fieldset>
  </td>
  <td style="width: 50%;">
    <fieldset>
      <legend>Rendez-vous</legend>
      <table class="main">
        <tr>
          <td>
            <table class="form">
              <tr>
                <th>{{mb_label object=$consult field="plageconsult_id"}}</th>
                <td>
                  {{if $consult->_id}}
                    <span style="float: right">
                  {{if $consult->sejour_id && $consult->_ref_sejour->type != "consult"}}
                    <button type="button" class="remove" onclick="unlinkSejour()" title="{{$consult->_ref_sejour}}">{{tr}}CConsultation-_unlink_sejour{{/tr}}</button>
                  {{elseif $consult->_count_matching_sejours}}
                    <button type="button" class="add" onclick="linkSejour()">{{tr}}CConsultation-_link_sejour{{/tr}}</button>
                  {{/if}}
                </span>
                  {{/if}}
                  <input type="text" name="_date" style="width: 15em;" value="{{$consult->_date|date_format:"%A %d/%m/%Y"}}" onfocus="PlageConsultSelector.init()" readonly="readonly" onchange="if (this.value != '') $V(this.form._function_id, '')"/>
                  <input type="hidden" name="_date_planning" value="{{$date_planning}}" />
                  {{mb_field object=$consult field="plageconsult_id" hidden=1 ondblclick="PlageConsultSelector.init()"}}
                  <button class="search notext" type="button" onclick="PlageConsultSelector.init()">Choix de l'horaire</button>
                  <button class="multiline notext" type="button" onclick="PlageConsultSelector.init(true)" id="buttonMultiple">Consultation multiple</button>
                </td>
              </tr>

              <tr>
                <th>{{mb_label object=$consult field="heure"}}</th>
                <td>
                  <input type="text" name="heure" value="{{$consult->heure}}" style="width: 15em;" onfocus="PlageConsultSelector.init()" readonly="readonly" />
                  {{if $consult->patient_id}}
                    ({{$consult->_etat}})
                    <br />
                    <a class="button new" href="?m=dPcabinet&tab=edit_planning&pat_id={{$consult->patient_id}}&consultation_id=0&date_planning={{$consult->_date}}&chir_id={{$chir->_id}}">Nouveau RDV pour ce patient</a>
                  {{/if}}
                </td>
              </tr>

              <tr>
                <th>{{mb_label object=$consult field="premiere"}}</th>
                <td>{{mb_field object=$consult field="premiere" typeEnum=checkbox}}</td>
              </tr>

              {{if $conf.dPcabinet.CConsultation.use_last_consult}}
                <tr>
                  <th>{{mb_label object=$consult field="derniere"}}</th>
                  <td>{{mb_field object=$consult field="derniere" typeEnum=checkbox}}</td>
                </tr>
              {{/if}}

              <tr>
                <th>{{mb_label object=$consult field="adresse"}}</th>
                <td>
                  <input type="checkbox" name="_check_adresse" value="1"
                    {{if $consult->_check_adresse}} checked="checked" {{/if}}
                         onchange="$('correspondant_medical').toggle();
              $('_adresse_par_prat').toggle();
              if (this.checked) {
                this.form.adresse.value = 1;
              } else {
                this.form.adresse.value = 0;
                this.form.adresse_par_prat_id.value = '';
              }" />
                  {{mb_field object=$consult field="adresse" hidden="hidden"}}
                </td>
              </tr>

              <tr id="correspondant_medical" {{if !$consult->_check_adresse}}style="display: none;"{{/if}}>
                {{assign var="object" value=$consult}}
                {{mb_include module=planningOp template=inc_check_correspondant_medical}}
              </tr>

              {{if $maternite_active && @$modules.maternite->_can->read && (!$pat->_id || $pat->sexe != "m")}}
                <tr>
                  <th>{{tr}}CGrossesse{{/tr}}</th>
                  <td>
                    {{mb_include module=maternite template=inc_input_grossesse object=$consult patient=$pat}}
                  </td>
                </tr>
              {{/if}}

              <tr>
                <td></td>
                <td colspan="3">
                  <div id="_adresse_par_prat" style="{{if !$medecin_adresse_par}}display:none{{/if}}; width: 300px;">
                    {{if $medecin_adresse_par}}Autres : {{$medecin_adresse_par->_view}}{{/if}}
                  </div>
                </td>
              </tr>

              <tr>
                <th>{{mb_label object=$consult field="si_desistement"}}</th>
                <td>{{mb_field object=$consult field="si_desistement" typeEnum="checkbox"}}</td>
              </tr>

              {{if $attach_consult_sejour}}
                <tr>
                  <th>{{mb_label object=$consult field="_forfait_se"}}</th>
                  <td>{{mb_field object=$consult field="_forfait_se" typeEnum="checkbox"}}</td>
                </tr>
                <tr>
                  <th>{{mb_label object=$consult field="_forfait_sd"}}</th>
                  <td>{{mb_field object=$consult field="_forfait_sd" typeEnum="checkbox"}}</td>
                </tr>
                <tr>
                  <th>{{mb_label object=$consult field="_facturable"}}</th>
                  <td>{{mb_field object=$consult field="_facturable" typeEnum="checkbox"}}</td>
                </tr>
              {{/if}}

              <tr>
                <th>{{mb_label object=$consult field="duree"}}</th>
                <td>
                  <select name="duree">
                    {{foreach from=1|range:15 item=i}}
                      {{if $plageConsult->_id}}
                        {{assign var=freq value=$plageConsult->_freq}}
                        {{math equation=x*y x=$i y=$freq assign=duree_min}}
                        {{math equation=floor(x/60) x=$duree_min assign=duree_hour}}
                        {{math equation=(x-y*60) x=$duree_min y=$duree_hour assign=duree_min}}
                      {{/if}}
                      <option value="{{$i}}" {{if $consult->duree == $i}}selected{{/if}}>
                        x{{$i}} {{if $plageConsult->_id}}({{if $duree_hour}}{{$duree_hour}}h{{/if}}{{if $duree_min}}{{$duree_min}}min{{/if}}){{/if}}</option>
                    {{/foreach}}
                  </select>
                </td>
              </tr>
              <tbody id="listCategorie">
              {{if $consult->_id || $chir->_id}}
                {{mb_include template="httpreq_view_list_categorie"
                categorie_id=$consult->categorie_id
                categories=$categories
                listCat=$listCat}}
              {{/if}}
              </tbody>
              <tr>
                <th>{{tr}}Filter-by-function{{/tr}}</th>
                <td>
                  <select name="_function_id" style="width: 15em;" onchange = "if (this.value != '') { $V(this.form.chir_id, ''); $V(this.form._date, '');}">
                    <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                    {{foreach from=$listFunctions item=_function}}
                      <option value="{{$_function->_id}}" class="mediuser" style="border-color: #{{$_function->color}};" {{if !$consult->_id && $_function_id == $_function->_id}}selected{{/if}}>
                        {{$_function->_view}}
                      </option>
                    {{/foreach}}
                  </select>
                </td>
              </tr>
            </table>
          </td>
          <td id="multiplePlaces" style="display: none;">
            {{foreach from=2|range:$app->user_prefs.NbConsultMultiple item=j}}
              <fieldset id="place_reca_{{$j}}">
                <legend>Rendez-vous {{$j}} <button class="button cancel notext" type="button" onclick="resetPlage('{{$j}}')">{{tr}}Delete{{/tr}}</button></legend>
                <input type="text" name="_consult{{$j}}" value="" readonly="readonly" style="width: 30em;"/>
                <input type="hidden" name="plage_id_{{$j}}" value=""/>
                <input type="hidden" name="date_{{$j}}" value=""/>
                <input type="hidden" name="heure_{{$j}}" value=""/>
                <input type="hidden" name="chir_id_{{$j}}" value=""/>
                <p><input type="text" name="rques_{{$j}}" placeholder="Remarque..." style="width: 30em;"/></p>
              </fieldset>
            {{/foreach}}
        </tr>
      </table>
    </fieldset>
  </td>
  </tr>
  </table>
</form>