<script>
function changeDate(sDebut, sFin){ 
  var oForm = document.printFrm;
  oForm._date_min.value = sDebut;
  oForm._date_max.value = sFin;
  oForm._date_min_da.value = Date.fromDATE(sDebut).toLocaleDate();
  oForm._date_max_da.value = Date.fromDATE(sFin).toLocaleDate();  
}
function reloadRetro(form) {
  var url = new Url('facturation', 'ajax_vw_retrocessions');
  url.addElement(form.chir);
  url.addElement(form._date_min);
  url.addElement(form._date_max);
  url.requestUpdate("retrocessions");
  return false;
}
</script>

{{if count($listPrat)}}
  <form name="printFrm" action="?" method="get" onSubmit="return reloadRetro(this);">
    <input type="hidden" name="a" value="" />
    <input type="hidden" name="dialog" value="1" />
    <table class="form">
      <tr>
        <th class="category" colspan="3"">Choix de la periode</th>
        <th class="category">{{mb_label object=$filter field="_prat_id"}}</th>
      </tr>
      <tr>
        <th>{{mb_label object=$filter field="_date_min"}}</th>
        <td>{{mb_field object=$filter field="_date_min" form="printFrm" canNull="false" register=true}}</td>
        <td rowspan="2" style="max-width:200px;">
          <table>
            <tr>
              <td>
                <input type="radio" name="select_days" onclick="changeDate('{{$now}}','{{$now}}');"  value="day" checked="checked"/>
                <label for="select_days_day">Jour courant</label>
                <br/>
                <input type="radio" name="select_days" onclick="changeDate('{{$yesterday}}','{{$yesterday}}');"  value="yesterday"/>
                <label for="select_days_yesterday">La veille</label>
                <br/>
                <input type="radio" name="select_days" onclick="changeDate('{{$week_deb}}','{{$week_fin}}');" value="week"/>
                <label for="select_days_week">Semaine courante</label>
                <br/>
              </td>
              <td>
                <input type="radio" name="select_days" onclick="changeDate('{{$month_deb}}','{{$month_fin}}');" value="month"/>
                <label for="select_days_month">Mois courant</label>
                <br/>
                <input type="radio" name="select_days" onclick="changeDate('{{$three_month_deb}}','{{$month_fin}}');" value="three_month"/>
                <label for="select_days_three_month">3 derniers mois</label>
              </td>
            </tr>
          </table>
        </td>
        <td rowspan="2">
          <select name="chir">
            {{if $listPrat|@count > 1}}
            <option value="">&mdash; Tous</option>
            {{/if}}
            {{mb_include module=mediusers template=inc_options_mediuser list=$listPrat selected=$prat->_id}}
          </select>
        </td>
      </tr>
      <tr>
        <th>{{mb_label object=$filter field="_date_max"}}</th>
        <td>{{mb_field object=$filter field="_date_max" form="printFrm" canNull="false" register=true}} </td>
      </tr>
      <tr>
        <td colspan="5" class="button">
          <button type="submit" class="submit">{{tr}}Validate{{/tr}}</button>
        </td>
      </tr>
    </table>
  </form>
  <div id="retrocessions">
    {{mb_include module=facturation template=inc_vw_retrocessions}}
  </div>
{{else}}
  <div class="big-info">
    Vous n'avez accès à la comptabilité d'aucun praticien.<br/>
    Veuillez contacter un administrateur
  </div>
{{/if}}