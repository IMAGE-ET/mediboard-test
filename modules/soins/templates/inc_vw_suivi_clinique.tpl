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
	
	{{if "forms"|module_active}}
  Main.add(function(){
    var url = new Url("forms", "ajax_list_ex_object");
    url.addParam("reference_class", "{{$sejour->_class_name}}");
    url.addParam("reference_id", "{{$sejour->_id}}");
    url.addParam("detail", 1);
    url.requestUpdate("list-ex_objects");
  });
	{{/if}}
</script>

<table style="text-align: left; width: 100%">
  <tr>
    <th class="title" colspan="2" style="background-color: #6688CC">
    {{mb_include module=system template=inc_object_notes object=$patient}}
      <span style="float: left;">
        {{include file="../../dPpatients/templates/inc_vw_photo_identite.tpl" mode="read" size=32}}
      </span>
      
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
    </td>
    <!-- Correspondance -->
    <td style="vertical-align: top;" rowspan="2">
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
			
			{{if "forms"|module_active}}
				<table class="main tbl">
					<tr>
						<th>Formulaires</th>
					</tr>
					<tr>
						<td id="list-ex_objects"></td>
					</tr>
				</table>
			{{/if}}
    </td>
  </tr>
  <tr>
    <!-- Informations sur le séjour -->
    <td style="width: 50%; vertical-align: top;">
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
        <tr>
          <td colspan="2">
            {{mb_include module=dPplanningOp template=inc_infos_operation}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>