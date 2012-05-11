<script type="text/javascript">
  Main.add(function() {
    var oForm = getForm("editConsultation");
    {{if $consult->date_at}}
      oForm.pec_at.disabled = "";
    {{/if}}
  });

  refreshAt = function() {
    new Url("dPcabinet", "ajax_refresh_accident_travail")
    .addParam("consult_id", "{{$consult->_id}}")
    .requestUpdate("at_area");
  }

  updateDates = function(elt) {
    var oForm = elt.form;
    if (elt.value == '') {
      $V(oForm.fin_at, '', false);
      $V(oForm.pec_at, '', false);
      $V(oForm.reprise_at, '', false);
    }
  }
</script>
    
<form name="editConsultation" method="post" action="?" onsubmit="return onSubmitFormAjax(this, {onComplete: refreshAt})">
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="dosql" value="do_consultation_aed" />
  <input type="hidden" name="ajax" value="1" />
  {{mb_key object=$consult}}
  <table style="width: 100%">
    <tr>
      <td class="halfPane">
        <fieldset>
            <legend>{{tr}}CConsultation-date_at{{/tr}}</legend>
            <table style="width: 100%;">
              <tr>
                <th style="width: 30%">{{mb_label object=$consult field=date_at}}</th>
                <td>{{mb_field object=$consult field=date_at form=editConsultation register=true onchange="updateDates(this); this.form.onsubmit();"}}</td>    
              </tr>
              <tr>
                <th>{{mb_label object=$consult field=fin_at}}</th>
                <td>
                  {{if $consult->date_at}}
                    {{mb_field object=$consult field=fin_at form=editConsultation register=true onchange="this.form.onsubmit()"}}
                  {{else}}
                    {{mb_value object=$consult field=fin_at}}
                  {{/if}}
                </td>
              </tr>
              <tr>
                <th>{{mb_label object=$consult field=pec_at}}</th>
                <td>
                  {{if $consult->date_at}}
                    {{mb_field object=$consult field=pec_at onchange="this.form.onsubmit()" emptyLabel="Choose" typeEnum="radio"}}
                  {{else}}
                    {{mb_field object=$consult field=pec_at emptyLabel="Choose" readonly=readonly typeEnum="radio"}}
                  {{/if}}
                </td>
              </tr>
              <tr>
                <th>{{mb_label object=$consult field=reprise_at}}</th>
                <td>
                  {{if $consult->date_at}}
                    {{mb_field object=$consult field=reprise_at form=editConsultation register=true onchange="this.form.onsubmit()"}}
                  {{else}}
                    {{mb_value object=$consult field=reprise_at}}
                  {{/if}}
                </td>
              </tr>
              <tr>
                <th>{{mb_label object=$consult field=at_sans_arret}}</th>
                <td>
                  {{if $consult->date_at}}
                    {{mb_field object=$consult field=at_sans_arret onchange="this.form.onsubmit()"}}
                  {{else}}
                    {{mb_field object=$consult field=at_sans_arret readonly=readonly}}
                  {{/if}}
                </th>
              </tr>
            </table>
          </fieldset>
      </td>
      <td class="halfPane">
        <fieldset>
          <legend>Arrêt maladie</legend>
          <table style="width: 100%">
            <tr>
              <th style="width: 30%">{{mb_label object=$consult field=arret_maladie}}</th>
              <td>
                {{if $consult->date_at}}
                  {{mb_field object=$consult field=arret_maladie onchange="this.form.onsubmit()"}}
                {{else}}
                  {{mb_field object=$consult field=arret_maladie readonly=readonly}}
                {{/if}}
              </th>
            </tr>
          </table>
        </fieldset>
      </td>
    </tr>
  </table>
</form>