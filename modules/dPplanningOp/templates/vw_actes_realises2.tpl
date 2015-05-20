<script>
function addReglement(object_guid, emetteur, montant, mode){
  var url = new Url('cabinet', 'edit_reglement');
  url.addParam('reglement_id', '0');
  url.addParam('object_guid', object_guid);
  url.addParam('emetteur', emetteur);
  url.addParam('montant', montant);
  url.addParam('mode', mode);
  url.requestModal(400);
}
function editReglement(reglement_id){
  var url = new Url('cabinet', 'edit_reglement');
  url.addParam('reglement_id', reglement_id);
  url.requestModal(400);
}
</script>

<table class="main">
  <tr>
    <th colspan="2">
      <a href="#" onclick="window.print()">
        Rapport des actes codés
      </a>
    </th>
  </tr>
  <tr>
    <td>
      <table class="main">
        {{if $bloc->_id}}
          <tr>
            <td><strong>{{tr}}CBlocOperatoire{{/tr}}: {{$bloc}}</strong></td>
          </tr>
        {{/if}}
        <tr>
          <td>
            {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$praticien}}
          </td>
        </tr>
        <tr>
          <td>du {{$_date_min|date_format:$conf.longdate}}</td>
        </tr>
        <tr>
          <td>au {{$_date_max|date_format:$conf.longdate}}</td>
        </tr>
      </table>
    </td>
    <td>
      <table class="tbl">
        <tr>
          <th>Nombre de séjours</th>
          <td>{{$nbActes|@count}}</td>
        </tr>
        <tr>
          <th>Nombre d'actes</th>
          <td>{{$totalActes}}</td>
        </tr>
        <tr>
          <th>Total Base</th>
          <td style="text-align: right;">{{$montantTotalActes.base|currency}}</td>
        </tr>
        <tr>
          <th>Total DH</th>
          <td style="text-align: right;">{{$montantTotalActes.dh|currency}}</td>
        </tr>
        <tr>
          <th>Total</th>
          <td style="text-align: right;">{{$montantTotalActes.total|currency}}</td>
        </tr>
      </table>
    </td>
  </tr>
  
  {{foreach from=$sejours key="key" item="jour"}}
  <tr>
    <td colspan="2">
      <table>
        <tr> 
          <td>
            <strong>Sortie réelle le {{$key|date_format:$conf.longdate}}</strong>
          </td>
        </tr>
      </table>
      <table class="tbl">
        <tr>
          <th>Patient</th>
          <th>Type</th>
          <th>Montant</th>
          <th>Remise</th>
          <th>Facturé</th>
          <th>{{mb_title class=CFactureEtablissement field=du_patient}}</th>
          <th>{{mb_title class=CFactureEtablissement field=patient_date_reglement}}</th>
        </tr>
        
        <!-- Parcours des sejours -->
        {{foreach from=$jour item="sejour"}}
        {{assign var="sejour_id" value=$sejour->_id}}
        <tbody class="hoverable">
        <tr>
          <td rowspan="{{$nbActes.$sejour_id}}">{{$sejour->_ref_patient->_view}} {{if $sejour->_ref_patient->_age}}({{$sejour->_ref_patient->_age}}){{/if}}</td>
          <td>
            Sejour du {{mb_value object=$sejour field=_entree}}
            au {{mb_value object=$sejour field=_sortie}}
            
            {{foreach from=$sejour->_ref_operations item=operation}}
              <br/>Intervention du {{mb_value object=$operation field=_datetime_best}}
              {{if $operation->libelle}}<br /> {{$operation->libelle}}{{/if}}
            {{/foreach}}
            {{foreach from=$sejour->_ref_consultations item=consult}}
              <br/>Consultation du {{$consult->_datetime|date_format:"%d %B %Y"}}
              {{if $consult->motif}}: {{$consult->motif}}{{/if}}
            {{/foreach}}
          </td>
            
          {{assign var=_facture value=$sejour->_ref_last_facture}}
          <td style="text-align:right;">{{$montantSejour.$sejour_id}} CHF</td>
          <td style="text-align:right;">
            {{if $_facture && $_facture->_id}}
              {{mb_value object=$_facture field="remise"}}
            {{/if}}
          </td>
          <td style="text-align:right;">{{$montantSejour.$sejour_id - $_facture->remise}} CHF</td>
           
          {{if $sejour->_ref_last_facture->_id}}
            <td>
              <table class="layout">
                {{foreach from=$_facture->_ref_reglements_patient item=_reglement}}
                <tr>
                  <td class="narrow">
                    <button class="edit notext" type="button" onclick="editReglement('{{$_reglement->_id}}');">
                      {{tr}}Edit{{/tr}}
                    </button>
                  </td>
                  <td class="narrow" style="text-align: right;"><strong>{{mb_value object=$_reglement field=montant}}</strong></td>
                  <td>{{mb_value object=$_reglement field=mode}}</td>
                  <td class="narrow">{{mb_value object=$_reglement field=date}}</td>
                </tr>
                {{/foreach}}

                {{if abs($_facture->_du_restant_patient) > 0.001}}
                  <tr>
                    <td colspan="4" class="button">
                      {{assign var=montant value=$_facture->_du_restant_patient}}
                      <button class="add" type="button" onclick="addReglement('{{$_facture->_guid}}', 'patient', '{{$montant}}', 'virement');">
                        {{tr}}Add{{/tr}} <strong>{{$montant}}</strong>
                      </button>
                    </td>
                  </tr>
                {{/if}}
              </table>
              
            </td>
            <td>
              <form name="edit-date-aquittement-{{$_facture->_guid}}" action="#" method="post">
                {{mb_class object=$_facture}}
                {{mb_key   object=$_facture}}
                <input type="hidden" name="patient_date_reglement" class="date" value="{{$_facture->patient_date_reglement}}" />
                <button type="button" class="submit notext" onclick="onSubmitFormAjax(this.form);"></button>
                <script>
                  Main.add(function(){
                    Calendar.regField(getForm("edit-date-aquittement-{{$_facture->_guid}}").patient_date_reglement);
                  });
                </script>
              </form>
            </td>
          {{else}}
            <td></td>
            <td></td>
          {{/if}}
        </tr>
        </tbody>
        {{/foreach}}
      </table>
    </td>
  </tr>
  {{/foreach}}
</table>