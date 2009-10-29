<script type="text/javascript">

function newExam(sAction, consultation_id) {
  if (sAction) {
    var url = new Url;
    url.setModuleAction("dPcabinet", sAction);
    url.addParam("consultation_id", consultation_id);
    url.popup(900, 600, "Examen");  
  }
}

</script>

<table class="tbl tooltip">
  <tr>
    <th>
      {{$object->_view}}
    </th>
  </tr>
  <tr>
    <td>
      <strong>Date:</strong>
      <em>
      	le {{$object->_ref_plageconsult->date|date_format:"%d %B %Y"}}
      	à {{mb_value object=$object field=heure}} 
      </em>
      <br />
      <strong>Praticien:</strong>
      <em>Dr {{$object->_ref_plageconsult->_ref_chir->_view}}</em>
      {{if $object->motif}}
        <br />
        <strong>Motif:</strong>
        <em>{{$object->motif|nl2br|truncate}}</em>
      {{/if}}
      {{if $object->rques}}
        <br />
        <strong>Remarques:</strong>
        <em>{{$object->rques|nl2br|truncate}}</em>
      {{/if}}
      {{if $object->examen}}
        <br />
        <strong>Examens:</strong>
        <em>{{$object->examen|nl2br|truncate}}</em>
      {{/if}}
      {{if $object->traitement}}
        <br />
        <strong>Traitement:</strong>
        <em>{{$object->traitement|nl2br|truncate}}</em>
      {{/if}}
      {{if $object->_ref_examaudio->examaudio_id}}
        <br />
        <a href="#" onclick="newExam('exam_audio', {{$object->consultation_id}})">
          <strong>Audiogramme</strong>
        </a>
      {{/if}}
       
			{{mb_include module=dPcabinet template=inc_list_actes_ccam subject=$object vue=view}}
    </td>
  </tr>
</table>