<script type="text/javascript">

Object.extend(Droppables, {
  addPrescription: function(prescription_id) {
    var oDragOptions = {
      onDrop: function(element) {
        Prescription.Examen.drop(element.id, prescription_id)
      }, 
      hoverclass:'selected'
    }
    
    this.add('drop-listprescriptions-' + prescription_id,  oDragOptions);
  }
} );
  
</script>

{{if $prescription->_id}}
<table class="tbl" id="drop-listprescriptions-{{$prescription->_id}}">
  <tr>
    <th class="title" colspan="100">
      <a style="float:right;" href="#nothing" onclick="view_log('{{$prescription->_class_name}}', {{$prescription->_id}})">
        <img src="images/icons/history.gif" alt="historique" title="Voir l'historique" />
      </a>
      {{$prescription->_view}}
      <script type="text/javascript">
        Droppables.addPrescription({{$prescription->_id}});
      </script>
    </th>
  </tr>
  <tr>
    <th>Examen</th>
    <th>Unité</th>
    <th>Références</th>
    <th>Resultat</th>
  </tr>
  {{foreach from=$prescription->_ref_prescription_items item="curr_item"}}
  {{assign var="curr_examen" value=$curr_item->_ref_examen_labo}}
  <tr id="PrescriptionItem-{{$curr_item->_id}}">
    <td>
      <a href="#nothing" onclick="Prescription.Examen.edit({{$curr_item->_id}})">
        {{$curr_examen->_view}}
      </a>
      <form name="delPrescriptionExamen-{{$curr_item->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="m" value="dPlabo" />
        <input type="hidden" name="dosql" value="do_prescription_examen_aed" />
        <input type="hidden" name="prescription_labo_id" value="{{$prescription->_id}}" />
        <input type="hidden" name="prescription_labo_examen_id" value="{{$curr_item->_id}}" />
        <input type="hidden" name="del" value="1" />
        <button type="button" class="trash notext" onclick="Prescription.Examen.del(this.form)" >{{tr}}Delete{{/tr}}</button>
        <button type="button" class="search notext" onclick="ObjectTooltip.create(this, 'CExamenLabo', {{$curr_examen->_id}}, { mode: 'complete', popup: true })">
          view
        </button>
      </form>
    </td>
    {{if $curr_examen->type == "num"}}
    <td>{{$curr_examen->unite}}</td>
    <td>{{$curr_examen->min}} &ndash; {{$curr_examen->max}}</td>
    {{else}}
    <td colspan="2">{{mb_value object=$curr_examen field="type"}}</td>
    {{/if}}
    <td>
      {{if $curr_examen->_external}}
      {{if $curr_item->date}}
        {{assign var=msgClass value=""}}
        {{if $curr_examen->type == "num"}}
          {{if $curr_item->_hors_limite}}
          {{assign var=msgClass value="warning"}}
          {{else}}
          {{assign var=msgClass value="message"}}
          {{/if}}
        {{/if}}
        
        <div class="{{$msgClass}}">
          {{mb_value object=$curr_item field=resultat}}
        </div>
      {{else}}
        <em>En attente</em>
      {{/if}}
      {{else}}
        <em>Examen externe</em>
      {{/if}}
    </td>
  </tr>
  {{/foreach}}
</table>
{{/if}}