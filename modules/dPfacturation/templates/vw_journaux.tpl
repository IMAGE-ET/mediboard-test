<script>
  function changeDate(sDebut, sFin){
    var oForm = document.printFrm;
    oForm._date_min.value = sDebut;
    oForm._date_max.value = sFin;
    oForm._date_min_da.value = Date.fromDATE(sDebut).toLocaleDate();
    oForm._date_max_da.value = Date.fromDATE(sFin).toLocaleDate();
  }
  function viewTotaux(type) {
    var oForm = document.printFrm;
    var url = new Url('facturation', 'vw_journal');
    url.addParam('type'    , type);
    url.addElement(oForm._date_min);
    url.addElement(oForm._date_max);
    url.addParam('prat_id' , oForm.chir.value);
    url.addParam('suppressHeaders' , '1');
    url.popup(1000, 600);
  }
</script>

{{if count($listPrat)}}
  <table class="main">
  <tr>
  <td>
  <form name="printFrm" action="?" method="get" onSubmit="return checkRapport()">
  <input type="hidden" name="a" value="" />
  <input type="hidden" name="dialog" value="1" />
  <table class="form">
  <tr>
    <th class="title" colspan="4">Edition de rapports</th>
  </tr>
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
            <input type="radio" name="select_days" onclick="changeDate('{{$now}}','{{$now}}');"  value="day" checked="checked" />
            <label for="select_days_day">Jour courant</label>
            <br />
            <input type="radio" name="select_days" onclick="changeDate('{{$yesterday}}','{{$yesterday}}');"  value="yesterday" />
            <label for="select_days_yesterday">La veille</label>
            <br />
            <input type="radio" name="select_days" onclick="changeDate('{{$week_deb}}','{{$week_fin}}');" value="week" />
            <label for="select_days_week">Semaine courante</label>
            <br />
          </td>
          <td>
            <input type="radio" name="select_days" onclick="changeDate('{{$month_deb}}','{{$month_fin}}');" value="month" />
            <label for="select_days_month">Mois courant</label>
            <br />
            <input type="radio" name="select_days" onclick="changeDate('{{$three_month_deb}}','{{$month_fin}}');" value="three_month" />
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
        {{mb_include module=mediusers template=inc_options_mediuser list=$listPrat}}
      </select>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$filter field="_date_max"}}</th>
    <td>{{mb_field object=$filter field="_date_max" form="printFrm" canNull="false" register=true}} </td>
  </tr>

  <tr>
    <th class="category" colspan="4">
      Journaux de relances
    </th>
  </tr>
  <tr>
    <td class="button" colspan="2">
      <button type="button" class="print" onclick="viewTotaux('paiement');">Journal des paiements</button>
      <div class="small-info">
        Impression du journal des paiements
      </div>
    </td>
    <td colspan="2" class="button">
      <button type="button" class="print" onclick="viewTotaux('debiteur');">Journal des débiteurs</button>
      <div class="small-info">
        Impression du journal des débiteurs
      </div>
    </td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      <button type="button" class="print" onclick="viewTotaux('rappel');">Journal des rappels (contentieux)</button>
      <div class="small-info">
        Impression du journal des rappels triés par ordre de statut
      </div>
    </td>
  </tr>
  </table>
  </form>
  </td>
  </tr>
  </table>
{{else}}
  <div class="big-info">
    Vous n'avez accès à la comptabilité d'aucun praticien.<br/>
    Veuillez contacter un administrateur
  </div>
{{/if}}