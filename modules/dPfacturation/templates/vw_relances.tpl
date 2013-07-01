{{mb_script module=facturation script=relance}}

<script>
function changeDate(sDebut, sFin){ 
  var form = document.printFrm;
  form._date_min.value = sDebut;
  form._date_max.value = sFin;
  form._date_min_da.value = Date.fromDATE(sDebut).toLocaleDate();
  form._date_max_da.value = Date.fromDATE(sFin).toLocaleDate();  
}
function addRelances(facture_class, type_relance){
  var form = getForm("printFrm");
  var relances = getForm("add-relances");
  relances.type_relance.value   = type_relance;
  relances.facture_class.value  = facture_class;
  relances._date_min.value      = form._date_min.value;
  relances._date_max.value      = form._date_max.value;
  relances.chir.value           = form.chir.value;
  submitFormAjax(relances, 'systemMsg');
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
          <th class="title" colspan="4">Gestion de relances</th>
        </tr>
        <tr>
          <th class="category" colspan="3">Choix de la periode de relance</th>
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
          <td class="button" rowspan="2">
            <select name="chir">
              {{if $listPrat|@count > 1}}
              <option value="">&mdash; Tous</option>
              {{/if}}
              {{mb_include module=mediusers template=inc_options_mediuser list=$listPrat selected=$chir_id}}
            </select>
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$filter field="_date_max"}}</th>
          <td>{{mb_field object=$filter field="_date_max" form="printFrm" canNull="false" register=true}} </td>
        </tr>
        {{if $conf.dPfacturation.CFactureCabinet.view_bill}}
          <tr>
            <th class="category" colspan="4">Relance de Cabinet</th>
          </tr>
          <tr>
            <td class="button" rowspan="2">
              <label for="typerelance_CFactureCabinet">Type de relance</label>
              <select name="typerelance_CFactureCabinet">
                <option value="1">Première Relance</option>
                <option value="2">Seconde Relance</option>
                <option value="3">Troisième Relance</option>
              </select>
            </td>
            <td class="button">
              <button type="button" class="search" onclick="ListeFacture.load('CFactureCabinet', this.form.typerelance_CFactureCabinet.value);">Voir les factures à relancer</button>
            </td>
            <td class="button">
              <label for="typereglement_CFactureCabinet">Règlement</label>
              <select name="typereglement_CFactureCabinet">
              <option value="0">&mdash; Tous</option>
              <option value="1">Emises</option>
              <option value="2">Réglées</option>
              <option value="3">Renouvelées</option>
              </select>
            </td>
          </tr>
          <tr>
            <td class="button">
              <button type="button" class="add" onclick="addRelances('CFactureCabinet', this.form.typerelance_CFactureCabinet.value);">Générer les relances</button>
            </td>
            <td class="button">
              <button type="button" class="search" onclick="ListeFacture.view('CFactureCabinet', this.form.typerelance_CFactureCabinet.value, this.form.typereglement_CFactureCabinet.value);">Voir les relances émises</button>
            </td>
          </tr>
        {{/if}}
        {{if $conf.dPfacturation.CFactureEtablissement.view_bill}}
          <tr>
            <th class="category" colspan="4">Relance d'établissement</th>
          </tr>
          <tr>
            <td class="button" rowspan="2">
              <label for="typerelance_CFactureEtablissement">Type de relance</label>
              <select name="typerelance_CFactureEtablissement">
                <option value="1">Première Relance</option>
                <option value="2">Seconde Relance</option>
                <option value="3">Troisième Relance</option>
              </select>
            </td>
            <td class="button">
              <button type="button" class="search" onclick="ListeFacture.load('CFactureEtablissement', this.form.typerelance_CFactureEtablissement.value);">Voir les factures à relancer</button>
            </td>
            <td class="button">
              <label for="typereglement_CFactureEtablissement">Règlement</label>
              <select name="typereglement_CFactureEtablissement">
              <option value="0">&mdash; Tous</option>
              <option value="emise">Emises</option>
              <option value="regle">Réglées</option>
              <option value="renouvelle">Renouvelées</option>
              </select>
            </td>
          </tr>
          <tr>
            <td class="button">
              <button type="button" class="add" onclick="addRelances('CFactureEtablissement', this.form.typerelance_CFactureEtablissement.value);">Générer les relances</button>
            </td>
            <td class="button">
              <button type="button" class="search" onclick="ListeFacture.view('CFactureEtablissement', this.form.typerelance_CFactureEtablissement.value, this.form.typereglement_CFactureEtablissement.value);">Voir les relances émises</button>
            </td>
          </tr>
        {{/if}}
      </table>
      </form>
    </td>
  </tr>
</table>
{{/if}}

<form name="add-relances" action="" method="post">
  <input type="hidden" name="m" value="facturation" />
  <input type="hidden" name="dosql" value="do_relance_aed" />
  <input type="hidden" name="_date_min" value="" />
  <input type="hidden" name="_date_max" value="" />
  <input type="hidden" name="facture_class" value="" />
  <input type="hidden" name="type_relance" value="" />
  <input type="hidden" name="chir" value="" />
</form>