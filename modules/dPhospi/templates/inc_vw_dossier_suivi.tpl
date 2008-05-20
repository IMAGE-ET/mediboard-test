<table class="tbl">
  <tr>
    <th class="halfPane title" class="title">
      Observations
    </th>
    <th class="halfPane title" class="title">
      Transmissions
    </th>
  </tr>
  <tr>
    <td>
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
    </td>
    <td>
      <form name="editTrans" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_transmission_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="m" value="dPhospi" />
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
    <td>
      <strong>{{$curr_suivi->date|date_format:"%d/%m/%Y à %Hh%M"}} - {{$curr_suivi->_view}}</strong>
      <br />
      {{$curr_suivi->text|nl2br}}
    </td>
  {{else}}
    <td />
  {{/if}}
  {{if $curr_suivi->_class_name == "CTransmissionMedicale"}}
    <td>
      <strong>{{$curr_suivi->date|date_format:"%d/%m/%Y à %Hh%M"}} - {{$curr_suivi->_view}}</strong>
      <br />
      {{$curr_suivi->text|nl2br}}
    </td>
  {{else}}
    <td />
  {{/if}}
  </tr>
  {{/foreach}}
</table>