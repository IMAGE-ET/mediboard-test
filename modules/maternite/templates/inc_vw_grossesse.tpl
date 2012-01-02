{{mb_script module=maternite script=naissance ajax=1}}
{{assign var=patient value=$operation->_ref_patient}}
{{assign var=sejour value=$operation->_ref_sejour}}
{{assign var=grossesse value=$sejour->_ref_grossesse}}

<script type="text/javascript">
  Main.add(function() {
    Naissance.reloadNaissances('{{$operation->_id}}');
  });
</script>


<form name="closeGrossesse" method="post"
  onsubmit="return onSubmitFormAjax(this, {onComplete: function() { refreshGrossesse('{{$operation->_id}}'); } });">
  <input type="hidden" name="m" value="maternite" />
  <input type="hidden" name="dosql" value="do_grossesse_aed" />
  {{mb_key object=$grossesse}}
  {{if $grossesse->active}}
    <input type="hidden" name="active" value="0" />
    <button type="button" class="tick" onclick="this.form.onsubmit()">{{tr}}CGrossesse-stop_grossesse{{/tr}}</button>
  {{else}}
    <input type="hidden" name="active" value="1" />
    <button type="button" class="cancel" onclick="this.form.onsubmit()">{{tr}}CGrossesse-reactive_grossesse{{/tr}}</button>
  {{/if}}
</form>
<h1 style="text-align: center;">
  Semaine {{$grossesse->_semaine_grossesse}} &mdash; Terme {{$grossesse->_terme_vs_operation}}j
</h1>
<table class="main">
  
  <tr>
    <td style="width: 50%;">
      <table class="tbl">
        <tr>
          <th class="category">
            {{tr}}CSejour.all{{/tr}}
          </th>
        </tr>
        {{foreach from=$grossesse->_ref_sejours item=object}}
          <tr>
            <td>
              {{mb_include module=dPplanningOp template=inc_vw_numdos nda=$object->_NDA _doss_id=$object->_id}}
              <span onmouseover="ObjectTooltip.createEx(this, '{{$object->_guid}}')">
                {{$object->_shortview}}
              </span>
            </td>
          </tr>
        {{foreachelse}}
          <tr>
            <td class="empty">
              {{tr}}CSejour.none{{/tr}}
            </td>
          </tr>
        {{/foreach}}
        <tr>
          <th class="category">
            {{tr}}CConsultation.all{{/tr}}
          </th>
        </tr>
        {{foreach from=$grossesse->_ref_consultations item=object}}
          <tr>
            <td>
              <span onmouseover="ObjectTooltip.createEx(this, '{{$object->_guid}}')">
                Consultation le {{$object->_datetime|date_format:$conf.date}}
              </span>
            </td>
          </tr>
        {{foreachelse}}
          <tr>
            <td class="empty">
              {{tr}}CConsultation.none{{/tr}}
            </td>
          </tr>
        {{/foreach}}
      </table>
    </td>
    <td style="width: 50%;" id="naissance_area"></td>
  </tr>
</table>
