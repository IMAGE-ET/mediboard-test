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
      <strong>Dr. {{$object->_ref_plageconsult->_ref_chir->_view}}</strong>
      &mdash; {{$object->_ref_plageconsult->date|date_format:"%d/%m/%Y"}}
      {{if $object->motif}}
        <br />
        <strong>Motif:</strong>
        <i>{{$object->motif|nl2br}}</i>
      {{/if}}
      {{if $object->rques}}
        <br />
        <strong>Remarques:</strong>
        <i>{{$object->rques|nl2br}}</i>
      {{/if}}
      {{if $object->examen}}
        <br />
        <strong>Examens:</strong>
        <i>{{$object->examen|nl2br}}</i>
      {{/if}}
      {{if $object->traitement}}
        <br />
        <strong>Traitement:</strong>
        <i>{{$object->traitement|nl2br}}</i>
      {{/if}}
      {{if $object->_ref_examaudio->examaudio_id}}
        <br />
        <a href="#" onclick="newExam('exam_audio', {{$object->consultation_id}})">
          <strong>Audiogramme</strong>
        </a>
      {{/if}}
    </td>
  </tr>
</table>