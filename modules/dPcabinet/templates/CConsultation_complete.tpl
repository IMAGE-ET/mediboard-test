<script type="text/javascript">

newExam = function(sAction, consultation_id) {
  if (sAction) {
    var url = new Url;
    url.setModuleAction("dPcabinet", sAction);
    url.addParam("consultation_id", consultation_id);
    url.popup(900, 600, "Examen");  
  }
}

</script>

<table class="form">
  <tr>
    <th class="title" colspan="2">
     
      <div class="idsante400" id="{{$object->_class_name}}-{{$object->_id}}"></div>
      
      <a style="float:right;" href="#nothing" onclick="view_log('{{$object->_class_name}}', {{$object->_id}})">
        <img src="images/icons/history.gif" alt="historique" title="Voir l'historique" />
      </a>

	 
	  
      <div style="float:left;" class="noteDiv {{$object->_class_name}}-{{$object->_id}}">
        <img alt="Ecrire une note" src="images/icons/note_grey.png" />
      </div>
      {{$object->_view}}
    </th>
  </tr>
  <tr>
    <td>
      <strong>Date :</strong>
      <i>le {{$object->_ref_plageconsult->date|date_format:"%d %B %Y"}} à {{$object->heure|date_format:"%Hh%M"}}</i>
    </td>
    <td>
      <strong>Praticien :</strong>
      <i>Dr. {{$object->_ref_plageconsult->_ref_chir->_view}}</i>
    </td>
  </tr>
  <tr>
    <td class="text">
      <strong>Motif :</strong>
      <i>{{$object->motif|nl2br}}</i>
    </td>
    <td class="text">
      <strong>Remarques :</strong>
      <i>{{$object->rques|nl2br}}</i>
    </td>
  </tr>
  
  
  
  <tr>
    <td class="text">
      <strong>Examens :</strong>
      <i>{{$object->examen|nl2br}}</i>
    </td>
    <td class="text">
      <strong>Traitement :</strong>
      <i>{{$object->traitement|nl2br}}</i>
    </td>
  </tr>
  {{if $object->_ref_examaudio->examaudio_id}}
  <tr>
    <td>
      <a href="#" onclick="newExam('exam_audio', {{$object->consultation_id}})">
        <strong>Audiogramme</strong>
      </a>
    </td>
  </tr>
  {{/if}}
</table>

<table class="tbl">
  {{assign var="vue" value="complete"}}
  {{assign var="subject" value=$object}}
  {{include file="../../dPcabinet/templates/inc_list_actes.tpl"}}
</table>

<table class="form">
  <tr>
    <th class="category" colspan="2">
      Facturation
    </th>
  </tr>
  <tr>
    <td>
      <strong>Date de paiement :</strong>
      {{if $object->patient_date_reglement}}
      <i>{{$object->patient_date_reglement|date_format:"%d/%m/%Y"}}</i>
      {{else}}
      <i>Non payé</i>
      {{/if}}
    </td>
    <td>
      <strong>Mode de paiement :</strong>
      <i>{{tr}}{{$object->patient_mode_reglement}}{{/tr}}</i>
    </td>
  </tr>
  <tr>
    <td>
      <strong>Partie conventionnée :</strong>
      <i>{{$object->secteur1}} euros</i>
    </td>
    <td>
      <strong>Dépassement d'honoraires :</strong>
      <i>{{$object->secteur2}} euros</i>
    </td>
  </tr>
</table>