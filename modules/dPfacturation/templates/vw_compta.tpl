{{mb_script module=facturation script=relance}}

<script>
  function changeDate(sDebut, sFin){
    var form = document.printFrm;
    form._date_min.value = sDebut;
    form._date_max.value = sFin;
    form._date_min_da.value = Date.fromDATE(sDebut).toLocaleDate();
    form._date_max_da.value = Date.fromDATE(sFin).toLocaleDate();
  }

  Main.add(function () {
    Control.Tabs.create('tabs-configure', true);

    // Autocomplete des praticiens
    var form = getForm("printFrm");
    var url = new Url("mediusers", "ajax_users_autocomplete");
    url.addParam("edit", '1');
    url.addParam("compta", '1');
    url.addParam("input_field", 'chir_view');
    url.autoComplete(form.chir_view, null, {
      minChars: 0,
      method: "get",
      select: "view",
      dropdown: true,
      afterUpdateElement: function(field, selected) {
        if ($V(form.chir_view) == "") {
          $V(form.chir_view, selected.down('.view').innerHTML);
        }
        var id = selected.getAttribute("id").split("-")[2];
        $V(form.chir, id);
      }
    });
  });
</script>

{{if count($listPrat)}}
  <form name="printFrm" action="?" method="get" onSubmit="return checkRapport()">
    <input type="hidden" name="a" value="" />
    <input type="hidden" name="dialog" value="1" />
    <table class="form main">
      <tr>
        <th class="category" colspan="3">Choix de la periode</th>
        <th class="category">{{mb_label object=$filter field="_prat_id"}}</th>
      </tr>
      <tr>
        <th>{{mb_label object=$filter field="_date_min"}}</th>
        <td>{{mb_field object=$filter field="_date_min" form="printFrm" canNull="false" register=true}}</td>
        <td rowspan="2">
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
        <td class="button" rowspan="2">
          <input type="hidden" name="chir" value=""/>
          <div style="text-align: left;" class="dropdown">
            <input type="text" name="chir_view" class="autocomplete" value="&mdash; Tous" style="text-align: left;"
                   onmousedown="$V(this, '');" onblur="$V(this, '&mdash; Tous');$V(this.form.chir, '');"
                   placeholder="&mdash; Choisir un praticien"/>
          </div>
        </td>
      </tr>

      <tr>
        <th>{{mb_label object=$filter field="_date_max"}}</th>
        <td>{{mb_field object=$filter field="_date_max" form="printFrm" canNull="false" register=true}} </td>
      </tr>
    </table>
  </form>

  {{if $conf.dPfacturation.CRelance.use_relances || $conf.dPfacturation.CJournalBill.use_journaux || $conf.ref_pays == 2}}
    <ul id="tabs-configure" class="control_tabs">
      <li><a href="#compta">Général</a></li>
      {{if $conf.dPfacturation.CRelance.use_relances}}
        <li><a href="#relances">Relances</a></li>
      {{/if}}
      {{if $conf.dPfacturation.CJournalBill.use_journaux}}
        <li><a href="#journaux">Journaux</a></li>
      {{/if}}
      {{if $conf.ref_pays == 2}}
        <li><a href="#impression">{{tr}}CFactureEtablissement.print{{/tr}}</a></li>
      {{/if}}
    </ul>
    <hr class="control_tabs" />

    <div id="compta" style="display: none;">
      {{mb_include module=facturation template=vw_gestion}}
    </div>

    {{if $conf.dPfacturation.CRelance.use_relances}}
      <div id="relances" style="display: none;">
        {{mb_include module=facturation template=vw_relances}}
      </div>
    {{/if}}

    {{if $conf.dPfacturation.CJournalBill.use_journaux}}
      <div id="journaux" style="display: none;">
        {{mb_include module=facturation template=vw_journaux}}
      </div>
    {{/if}}

    {{if $conf.ref_pays == 2}}
      <div id="impression" style="display: none;">
        {{mb_include module=facturation template=vw_print_bill}}
      </div>
    {{/if}}

  {{else}}
    {{mb_include module=facturation template=vw_gestion}}
  {{/if}}
{{else}}
  <div class="big-info">
    Vous n'avez accès à la comptabilité d'aucun praticien.<br/>
    Veuillez contacter un administrateur
  </div>
{{/if}}
