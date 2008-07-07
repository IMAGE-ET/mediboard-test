<script type="text/javascript">

delCibleTransmission = function() {
  oDiv = $('cibleTrans');
  if(!oDiv) {
    return;
  }
  oForm = document.forms['editTrans'];
  $V(oForm.object_class, "");
  $V(oForm.object_id, "");
  oDiv.innerHTML = "";
}

</script>

<table class="tbl">
  <tr>
    <th class="title" style="width: 50%">
      Observations
    </th>
    <th class="title" style="width: 50%">
      Transmissions
    </th>
  </tr>
  <tr>
    <td>
      {{if $isPraticien}}
      <form name="editObs" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_observation_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="m" value="dPhospi" />
      <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
      <input type="hidden" name="user_id" value="{{$user->_id}}" />
      <input type="hidden" name="date" value="now" />
      {{mb_label object=$observation field="text"}}
      {{mb_field object=$observation field="degre"}}
      <br />
      {{mb_field object=$observation field="text"}}
      <br />
      <button type="button" class="add" onclick="submitSuivi(this.form)">{{tr}}Add{{/tr}}</button>
      </form>
      {{/if}}
    </td>
    <td>
      <div id="cibleTrans" style="font-style: italic;" onclick="delCibleTransmission()">
      </div>
      <form name="editTrans" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_transmission_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="m" value="dPhospi" />
      <input type="hidden" name="object_class" value="" />
      <input type="hidden" name="object_id" value="" />
      <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
      <input type="hidden" name="user_id" value="{{$user->_id}}" />
      <input type="hidden" name="date" value="now" />
      {{mb_label object=$transmission field="text"}}
      {{mb_field object=$transmission field="degre"}}
      <br />
      {{mb_field object=$transmission field="text"}}
      <br />
      <button type="button" class="add" onclick="submitSuivi(this.form)">{{tr}}Add{{/tr}}</button>
      </form>
    </td>
  </tr>
  {{foreach from=$sejour->_ref_suivi_medical item=curr_suivi}}
  <tr>
  {{if $curr_suivi->_class_name == "CObservationMedicale"}}
    <td class="text">
      <div {{if $curr_suivi->degre == "high"}}style="background-color: #faa"{{/if}}>
      {{if $curr_suivi->user_id == $user->_id}}
      <form name="delObs{{$curr_suivi->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_observation_aed" />
      <input type="hidden" name="del" value="1" />
      <input type="hidden" name="m" value="dPhospi" />
      <input type="hidden" name="observation_medicale_id" value="{{$curr_suivi->_id}}" />
      <input type="hidden" name="sejour_id" value="{{$curr_suivi->sejour_id}}" />
      <button type="button" class="trash notext" onclick="submitSuivi(this.form)">{{tr}}Delete{{/tr}}</button>
      </form>
      {{/if}}
      <strong>{{$curr_suivi->date|date_format:"%d/%m/%Y à %Hh%M"}} - {{$curr_suivi->_view}}</strong>
      </div>
      {{$curr_suivi->text|nl2br}}
    </td>
  {{else}}
    <td />
  {{/if}}
  {{if $curr_suivi->_class_name == "CTransmissionMedicale"}}
    <td class="text">
      <div {{if $curr_suivi->degre == "high"}}style="background-color: #faa"{{/if}}>
      {{if $curr_suivi->object_id}}
      <em>Cible : {{$curr_suivi->_ref_object->_view}}</em>
      <br />
      {{/if}}
      {{if $curr_suivi->user_id == $user->_id}}
      <form name="delTrans{{$curr_suivi->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_transmission_aed" />
      <input type="hidden" name="del" value="1" />
      <input type="hidden" name="m" value="dPhospi" />
      <input type="hidden" name="transmission_medicale_id" value="{{$curr_suivi->_id}}" />
      <input type="hidden" name="sejour_id" value="{{$curr_suivi->sejour_id}}" />
      <button type="button" class="trash notext" onclick="submitSuivi(this.form)">{{tr}}Delete{{/tr}}</button>
      </form>
      {{/if}}
      <strong>{{$curr_suivi->date|date_format:"%d/%m/%Y à %Hh%M"}} - {{$curr_suivi->_view}}</strong>
      </div>
      {{$curr_suivi->text|nl2br}}
    </td>
  {{else}}
    <td />
  {{/if}}
  </tr>
  {{/foreach}}
</table>