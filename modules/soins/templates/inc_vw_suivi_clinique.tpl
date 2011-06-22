{{assign var=patient value=$sejour->_ref_patient}}

<script type="text/javascript">
  modalViewComplete = function(object_guid, title) {
    var url = new Url("system", "httpreq_vw_complete_object");
    url.addParam("object_guid", object_guid);
    url.requestModal(800, 500, { title: title });
  }

  popEtatSejour = function(sejour_id) {
    var url = new Url("dPhospi", "vw_parcours");
    url.addParam("sejour_id", '{{$sejour->_id}}');
    url.requestModal(700, 550);
  }

  toggleProgressFuture = function() {
    $$(".opacity-40").each(function(elt) {
      elt.toggleClassName("in_progress_future");
    });
  }
  
  {{if "forms"|module_active}}
    Main.add(function(){
      ExObject.loadExObjects("{{$sejour->_class_name}}", "{{$sejour->_id}}", "list-ex_objects", 1);
    });
  {{/if}}
</script>

<style type="text/css">
  .in_progress_future {
    display: none;
  }
</style>

<table style="text-align: left; width: 100%">
  <tr>
    <th class="title" colspan="2" style="background-color: #6688CC">
    {{mb_include module=system template=inc_object_notes object=$patient}}
      <a href="?m=dPpatients&tab=vw_full_patients&patient_id={{$patient->_id}}">
        <span style="float: left;">
          {{include file="../../dPpatients/templates/inc_vw_photo_identite.tpl" mode="read" size=32}}
        </span>
      </a>
      
      {{mb_include module=system template=inc_object_idsante400 object=$patient}}
      {{mb_include module=system template=inc_object_history object=$patient}}
      
      <a style="float:right;" href="#print-{{$patient->_guid}}" onclick="Patient.print('{{$patient->_id}}')">
        <img src="images/icons/print.png" alt="imprimer" title="Imprimer la fiche patient" />
      </a>
      
      {{if $can->edit}}
      <a style="float:right;" href="#edit-{{$patient->_guid}}" onclick="Patient.edit('{{$patient->_id}}')">
        <img src="images/icons/edit.png" alt="modifier" title="Modifier le patient" />
      </a>
      {{/if}}
      
      {{if $app->user_prefs.vCardExport}}
      <a style="float:right;" href="#export-{{$patient->_guid}}" onclick="Patient.exportVcard('{{$patient->_id}}')">
        <img src="images/icons/vcard.png" alt="export" title="Exporter le patient" />
      </a>
      {{/if}}

      {{if $patient->date_lecture_vitale}}
        <div style="float: right;">
          <img src="images/icons/carte_vitale.png" title="{{tr}}CPatient-date-lecture-vitale{{/tr}} : {{mb_value object=$patient field="date_lecture_vitale" format=relative}}" />
        </div>
      {{/if}}
      
      <form name="actionPat" action="?" method="get">
        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="tab" value="vw_idx_patients" />
        <input type="hidden" name="patient_id" value="{{$patient->_id}}" />
        <h2 style="color: #fff; font-weight: bold;">
          {{$patient->_view}}
          <span style="font-size: 0.7em;"> - {{$sejour->_shortview|replace:"Du":"Séjour du"}}</span>
        </h2>
      </form>
    </th>
  </tr>
  <tr>
    <!-- Informations sur le patient -->
    <td style="width: 50%; vertical-align: top">
       <table class="tbl">
        <tr>
          <th colspan="2" class="category">
            <span style="float: right">
              <button type="button" class="search" onclick="modalViewComplete('{{$patient->_guid}}', 'Détail du patient')">Patient</button>
            </span>
            <span style="float: left;">
              <button class="lookup notext" style="margin: 0;" onclick="popEtatSejour();">Etat du séjour</button>
            </span>
            Coordonnées
          </th>
        </tr>
        <tr>
          <td style="width: 50%;">
            <strong>{{mb_label object=$patient field="nom"}}</strong>
            {{mb_value object=$patient field="nom"}}
          </td>
          <td>
            <strong>{{mb_label object=$patient field="prenom"}}</strong>
            {{mb_value object=$patient field="prenom"}}{{if $patient->prenom_2}}, 
            {{mb_value object=$patient field="prenom_2"}}{{/if}}{{if $patient->prenom_3}}, 
            {{mb_value object=$patient field="prenom_3"}}{{/if}}{{if $patient->prenom_4}}, 
            {{mb_value object=$patient field="prenom_4"}} {{/if}}
          </td>
        </tr>
        <tr>
          <td class="text">
            <strong>{{mb_label object=$patient field="nom_jeune_fille"}}</strong>
            {{mb_value object=$patient field="nom_jeune_fille"}}
          </td>
          <td class="text">
            <strong>{{mb_label object=$patient field="tel"}}</strong>
            {{mb_value object=$patient field="tel"}}
          </td>
        </tr>
        <tr>
          <td class="text">
            <strong>{{mb_label object=$patient field="naissance"}}</strong>
            {{mb_value object=$patient field="naissance"}}
          </td>
          <td class="text">
            <strong>{{mb_label object=$patient field="tel2"}}</strong>
            {{mb_value object=$patient field="tel2"}}
          </td>
        </tr>
        <tr>
          <td class="text">
            <strong>{{mb_label object=$patient field="sexe"}}</strong>
            {{mb_value object=$patient field="sexe"}}
          </td>
          <td class="text">
            <strong>{{mb_label object=$patient field="tel_autre"}}</strong>
            {{mb_value object=$patient field="tel_autre"}}
          </td>
        </tr>
        <tr>
          <td>
          </td>
          <td class="text">
            <strong>{{mb_label object=$patient field="email"}}</strong>
            {{mb_value object=$patient field="email"}}
          </td>
        </tr> 
        <tr>
          <td class="text">
            <strong>{{mb_label object=$patient field="profession"}}</strong>
            {{mb_value object=$patient field="profession"}}
          </td>
          <td class="text">
            <strong>{{mb_label object=$patient field="rques"}}</strong>
            {{mb_value object=$patient field="rques"}}
          </td>
        </tr>
      </table>
      
      <!-- Correspondance -->
      <table class="tbl">
        <tr>
          <th style="width: 1%;">
          </th>
          <th>
            {{mb_label object=$patient field="prevenir_nom"}}
          </th>
          <th>
            {{mb_label object=$patient field="prevenir_prenom"}}
          </th>
          <th>
            {{mb_label object=$patient field="prevenir_tel"}}
          </th>
        </tr>
        <tr>
          <td>
            <strong>Personne à prévenir</strong>
          </td>
          <td>
            {{mb_value object=$patient field="prevenir_nom"}}
          </td>
          <td>
            {{mb_value object=$patient field="prevenir_prenom"}}
          </td>
          <td>
            {{mb_value object=$patient field="prevenir_tel"}}
          </td>
        </tr>
        <tr>
          <td>
            <strong>Personne de confiance</strong>
          </td>
          <td>
            {{mb_value object=$patient field="confiance_nom"}}
          </td>
          <td>
            {{mb_value object=$patient field="confiance_prenom"}}
          </td>
          <td>
            {{mb_value object=$patient field="confiance_tel"}}
          </td>
        </tr>
        <tr>
          <td>
            <strong>Employeur</strong>
          </td>
          <td>
            {{mb_value object=$patient field="employeur_nom"}}
          </td>
          <td></td>
          <td>
            {{mb_value object=$patient field="employeur_tel"}}
          </td>
        </tr>
      </table>
      
      <!--  Informations sur le séjour -->
      <table class="tbl">
        <tr>
          <th class="category" colspan="2">
            <span style="float: right">
              <button type="button" class="search" onclick="modalViewComplete('{{$sejour->_guid}}', 'Détail du séjour')">Détail</button>
            </span>
            {{tr}}CSejour{{/tr}}
          </th>
        </tr>
        <tr>
          <td style="width: 50%;">
            <strong>{{mb_label object=$sejour field="libelle"}}</strong>
            {{mb_value object=$sejour field="libelle"}}
          </td>
          <td>
            <strong>{{mb_label object=$sejour field="praticien_id"}}</strong>
            {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$sejour->_ref_praticien}}
          </td>
        </tr>
        <tr>
          <td>
            <strong>{{mb_label object=$sejour field="entree"}}{{if $sejour->entree_reelle}} (effectuée){{/if}}</strong>
            {{mb_value object=$sejour field="entree"}}
          </td>
          <td>
            <strong>{{mb_label object=$sejour field="sortie"}}{{if $sejour->sortie_reelle}} (effectuée){{/if}}</strong>
            {{mb_value object=$sejour field="sortie"}}
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <strong>{{mb_label object=$sejour field="type"}}</strong>
            {{mb_value object=$sejour field="type"}}
          </td>
        </tr>
      </table>
      
      {{mb_include module=dPplanningOp template=inc_infos_operation}}
      
      {{if $sejour->_ref_transmissions|@count}}
        <table class="tbl">
          <tr>
            <th class="title" colspan="3">Transmisssions de synthèse</th>
          </tr>
          <tr>
            <th class="narrow">{{tr}}Date{{/tr}}</th>
            <th class="narrow">{{tr}}Hour{{/tr}}</th>
            <th>{{mb_title class=CTransmissionMedicale field=text}}</th>
          </tr>
          {{foreach from=$sejour->_ref_transmissions item=_transmissions key=_cat_name name=foreach_trans}}
            <tr>
              <th colspan="3">{{$_cat_name}}</th>
            </tr>
            {{foreach from=$_transmissions item=_trans}}
              <tr>
                <td style="text-align: center; height: 22px;">
                  {{mb_ditto name=date value=$_trans->date|date_format:$conf.date}}
                </td>
                <td style="text-align: center;">
                  {{$_trans->date|date_format:$conf.time}}
                </td>
                <td class="text {{if $_trans->type}}trans-{{$_trans->type}}{{/if}} libelle_trans">{{mb_value object=$_trans field=text}}</td>
              </tr>
            {{/foreach}}
          {{foreachelse}}
            <tr>
              <td colspan="3">{{tr}}CTransmissionMedicale.none{{/tr}}</td>
            </tr>
          {{/foreach}}
        </table>
      {{/if}}
    </td>
    <td style="vertical-align: top;" rowspan="2">
      {{assign var=prescription value=$sejour->_ref_prescription_sejour}}
      
        <table class="tbl">
          <tr>
            <th class="title">
              <button type="button" style="float: right;" class="add notext" onclick="toggleProgressFuture();" title="{{tr}}CPrescription.in_progress_more{{/tr}}"></button>
              {{tr}}CPrescription.in_progress{{/tr}}
            </th>
            {{if $prescription->_ref_lines_med_comments.med|@count || $prescription->_ref_lines_med_comments.comment|@count ||
               $prescription->_ref_prescription_line_mixes|@count || $prescription->_ref_lines_elements_comments|@count}}
            {{if $prescription->_ref_lines_med_comments.med|@count || $prescription->_ref_lines_med_comments.comment|@count}}
              <tr>
                <th>{{tr}}CPrescription._chapitres.med{{/tr}}</th>
              </tr>
              {{foreach from=$prescription->_ref_lines_med_comments item=_lines_med_type}}
                {{foreach from=$_lines_med_type item=_line}}
                  <tr class="{{if $_line->_fin_reelle && $_line->_fin_reelle <= $date && $_line->_fin_reelle >= $date_before}}
                          hatching opacity-40 in_progress_future
                        {{elseif $_line->_debut_reel && $_line->_debut_reel >= $date && $_line->_debut_reel <= $date_after}}
                          opacity-40 in_progress_future
                        {{/if}}">
                    {{if !$_line instanceof CPrescriptionLineComment}}
                      <td class="text">
                        {{$_line->_ucd_view}}
                      </td>
                    {{/if}}
                  </tr>
                {{/foreach}}
              {{/foreach}}
            {{/if}}
            
            {{if $prescription->_ref_prescription_line_mixes_by_type|@count}}
              {{foreach from=$prescription->_ref_prescription_line_mixes_by_type item=_lines_by_type key=chap}}
                <tr>
                  <th>{{tr}}CPrescription._chapitres.{{$chap}}{{/tr}}</th>
                </tr>
                {{foreach from=$_lines_by_type item=_line}} 
                  <tr class="{{if $_line->_fin && $_line->_fin <= $date && $_line->_fin >= $date_before}}
                        hatching opacity-40 in_progress_future
                    {{elseif $_line->_debut && $_line->_debut >= $date && $_line->_debut <= $date_after}}
                        opacity-40 in_progress_future
                    {{/if}}">
                    <td class="text">
                      {{$_line->_view}}
                      ({{foreach from=$_line->_ref_lines item=_line_mix_item name=foreach_line_mixes}}
                        {{$_line_mix_item->_ucd_view}} {{if !$smarty.foreach.foreach_line_mixes.last}},{{/if}}
                      {{/foreach}})
                    </td>
                  </tr>
                {{/foreach}}
              {{/foreach}}
            {{/if}}
            
            {{foreach from=$prescription->_ref_lines_elements_comments item=chap_element key=_category_name}}
              <tr>
                <th>{{tr}}CPrescription._chapitres.{{$_category_name}}{{/tr}}</th>
              </tr>
              {{foreach from=$chap_element item=cat_element}}
                {{foreach from=$cat_element item=elements}}
                  {{foreach from=$elements item=element}}
                  {{if !$element instanceof CPrescriptionLineComment}}
                    <tr class="{{if $element->_fin_reelle && $element->_fin_reelle <= $date && $element->_fin_reelle >= $date_before}}
                        hatching opacity-40 in_progress_future
                      {{elseif $element->_debut_reel && $element->_debut_reel >= $date && $element->_debut_reel <= $date_after}}
                        opacity-40 in_progress_future
                      {{/if}}">
                      <td class="text">
                        {{$element->_view}}
                      </td>
                    </tr>
                  {{/if}}
                  {{/foreach}}
                {{/foreach}}
              {{/foreach}}
            {{/foreach}}
          {{else}}
          <tr>
            <td>
              Aucune ligne en cours
            </td>
          </tr>
          {{/if}}
        </table>
      
      {{if "forms"|module_active}}
        <table class="main tbl">
          <tr>
            <th>Formulaires</th>
          </tr>
          <tr>
            <td id="list-ex_objects"></td>
          </tr>
        </table>
        
        {{* {{mb_include module=forms template=inc_widget_ex_class_register object=$sejour event=suivi_clinique}} *}}
      {{/if}}
    </td>
  </tr>
</table>