<script type="text/javascript">
Main.add(function () {
  Calendar.regRedirectPopup("{{$date}}", "?m={{$m}}&tab={{$tab}}&date="); 
});

function devalidationRepas(validation_id){
  oForm = document.validRepas;
  oForm.del.value = 1;
  oForm.validationrepas_id.value = validation_id;
  oForm.submit();
}
function validationRepas(typerepas_id){
  oForm = document.validRepas;
  oForm.typerepas_id.value = typerepas_id;
  oForm.submit();
}
</script>

<form name="validRepas" action="?m={{$m}}" method="post">
<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="dosql" value="do_validationrepas_aed" />
<input type="hidden" name="date" value="{{$date}}" />
<input type="hidden" name="service_id" value="{{$service_id}}" />
<input type="hidden" name="typerepas_id" value="" />
<input type="hidden" name="validationrepas_id" value="" />
</form>

<table class="main">
  <tr>
    <td>
      <form name="FrmSelectService" action="?m={{$m}}" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <label for="service_id" title="Veuillez sélectionner un service">Service</label>
      <select name="service_id" onchange="this.form.submit();">
        <option value="">&mdash; Veuillez sélectionner un service</option>
        {{foreach from=$services item=curr_service}}
        <option value="{{$curr_service->service_id}}" {{if $curr_service->service_id == $service_id}}selected="selected"{{/if}}>
          {{$curr_service->nom}}
        </option>
        {{/foreach}}
      </select>
      pour le {{$date|date_format:"%A %d %b %Y"}}
      <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
      </form><br />
    </td>
  </tr>
  {{if $service_id}}
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <td colspan="2"></td>
          {{foreach from=$listTypeRepas item=curr_type}}
          <th class="category">
            {{assign var="type_id" value=$curr_type->_id}}
            {{assign var="validation" value=$service->_ref_validrepas.$date.$type_id}}
            {{if $validation->validationrepas_id}}
            <button type="button" class="cancel notext" onclick="devalidationRepas({{$validation->validationrepas_id}})" style="float:right;">{{tr}}Cancel{{/tr}}</button>            
            {{else}}
            <button type="button" class="tick notext" onclick="validationRepas({{$curr_type->_id}})" style="float:right;">{{tr}}Modify{{/tr}}</button>
            {{/if}}
            {{$curr_type->nom}}
          </th>
          {{/foreach}}
        </tr>
        
        {{foreach from=$service->_ref_chambres item=curr_chambre}}
          {{foreach from=$curr_chambre->_ref_lits item=curr_lit}}
            {{foreach from=$curr_lit->_ref_affectations item=curr_affect}}
              <tr>
                <td>{{$curr_chambre->nom}} - {{$curr_lit->_view}}</td>
                <td>{{$curr_affect->_ref_sejour->_ref_patient->_view}}</td>
                {{foreach from=$listTypeRepas key=keyType item=curr_type}}
                <td class="button">
                  {{if ($date == $curr_affect->entree|date_format:"%Y-%m-%d" 
                       && $curr_affect->entree|date_format:$dPconfig.time > $curr_type->fin)
                       ||
                       ($date == $curr_affect->sortie|date_format:"%Y-%m-%d"  
                       && $curr_type->debut > $curr_affect->sortie|date_format:$dPconfig.time)
                  }}
                  -
                  {{elseif $curr_affect->_list_repas.$date.$keyType->repas_id && $curr_affect->_list_repas.$date.$keyType->menu_id}}
                  <a href="?m={{$m}}&amp;tab=vw_edit_repas&amp;affectation_id={{$curr_affect->affectation_id}}&amp;typerepas_id={{$keyType}}">
                    <img src="images/icons/tick-dPrepas.png" width="20" height="20" alt="Repas commandé" />
                  </a>
                  {{elseif $curr_affect->_list_repas.$date.$keyType->repas_id}}
                  <a href="?m={{$m}}&amp;tab=vw_edit_repas&amp;affectation_id={{$curr_affect->affectation_id}}&amp;typerepas_id={{$keyType}}">
                    <img src="images/icons/no.png" width="20" height="20" alt="" />
                  </a>                  
                  {{else}}
                  <a href="?m={{$m}}&amp;tab=vw_edit_repas&amp;affectation_id={{$curr_affect->affectation_id}}&amp;typerepas_id={{$keyType}}">
                    <img src="images/icons/flag.png" width="20" height="20" alt="Repas à commander" />
                  </a>
                  {{/if}}
                </td>
                {{/foreach}}
              </tr>
            {{/foreach}}
          {{/foreach}}
        {{/foreach}}
      </table>
    </td>
  </tr>
  {{/if}}
</table>