{{if $prescription->_id}}
<table class="tbl">
  <tr>
    <th class="title" colspan="3">
      <a style="float:right;" href="#nothing" onclick="view_log('{{$prescription->_class_name}}', {{$prescription->_id}})">
        <img src="images/icons/history.gif" alt="historique" title="Voir l'historique" />
      </a>
      {{$prescription->_view}}
    </th>
  </tr>
  <tr>
    <th>Examen</th>
    <th>Type</th>
    <th>Resultat</th>
  </tr>
  {{foreach from=$prescription->_ref_prescription_labo_examens item="curr_item"}}
  {{assign var="curr_examen" value=$curr_item->_ref_examen_labo}}
  <tr id="PrescriptionItem-{{$curr_item->_id}}">
    <td>
      <form name="delPrescriptionExamen-{{$curr_item->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="m" value="dPlabo" />
        <input type="hidden" name="dosql" value="do_prescription_examen_aed" />
        <input type="hidden" name="prescription_labo_id" value="{{$prescription->_id}}" />
        <input type="hidden" name="prescription_labo_examen_id" value="{{$curr_item->_id}}" />
        <input type="hidden" name="del" value="1" />
        <button type="button" class="trash notext" style="float: right;" onclick="Prescription.Examen.del(this.form)" >{{tr}}Delete{{/tr}}</button>
      </form>
       
      <button class="search notext" style="float: right;" onclick="ObjectTooltip.create(this, 'CExamenLabo', {{$curr_examen->_id}}, { popup: true })">
        button
      </button>

      <a href="#nothing" onclick="Prescription.Examen.edit({{$curr_item->_id}})">
        {{$curr_examen->_view}}
      </a>
    </td>
    <td>
      {{mb_value object=$curr_examen field="type"}}
      {{if $curr_examen->_reference_values}} ({{$curr_examen->_reference_values}}) {{/if}}
    </td>
    <td>
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
          <button class="search notext" style="float: right;" onclick="ObjectTooltip.create(this, 'CPrescriptionLaboExamen', {{$curr_item->_id}}, { popup: true })">
            button
          </button>
          {{mb_value object=$curr_item field=resultat}} 
          {{mb_value object=$curr_examen field=unite}}
        </div>
      {{else}}
        <em>Aucun résultat</em>
      {{/if}}
    </td>
  </tr>
  {{/foreach}}
</table>
{{/if}}