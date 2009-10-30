
<table class="tbl tooltip">
  <tr>
    <th class="text">
      {{$object->_view}}
    </th>
  </tr>
  <tr>
    <td>
      <strong>Date:</strong>
      <em>
      	le {{$object->_ref_plageconsult->date|date_format:$dPconfig.longdate}}
      	à {{mb_value object=$object field=heure}} 
      </em>
      <br />
      <strong>Praticien:</strong>
      <em>Dr {{$object->_ref_plageconsult->_ref_chir->_view}}</em>
      {{if $object->motif}}
        <br />
        <strong>Motif:</strong>
        <em>{{$object->motif|truncate}}</em>
      {{/if}}
      {{if $object->rques}}
        <br />
        <strong>Remarques:</strong>
        <em>{{$object->rques|truncate}}</em>
      {{/if}}
      {{if $object->examen}}
        <br />
        <strong>Examens:</strong>
        <em>{{$object->examen|truncate}}</em>
      {{/if}}
      {{if $object->traitement}}
        <br />
        <strong>Traitement:</strong>
        <em>{{$object->traitement|truncate}}</em>
      {{/if}}

      {{assign var=examaudio value=$object->_ref_examaudio}}
      {{if $examaudio->_id}}
			<script type="text/javascript">
			
			newExam = function(sAction, consultation_id) {
				if (sAction) {
					var url = new Url("dPcabinet", sAction);
					url.addParam("consultation_id", consultation_id);
					url.popup(900, 600, "Examen");  
				}
			}
			
			</script>

        <br />
        <a href="#{{$examaudio->_guid}}" onclick="newExam('exam_audio', {{$object->consultation_id}})">
          <strong>Audiogramme</strong>
        </a>
      {{/if}}
       
			{{mb_include module=dPcabinet template=inc_list_actes_ccam subject=$object vue=view}}
    </td>
  </tr>
</table>