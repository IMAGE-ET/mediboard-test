<!-- Facture -->
{{if $facture && $facture->_id && $conf.dPcabinet.CConsultation.consult_facture}}
  <table class="main tbl">
    <th class="title" colspan="10">
      {{$facture->_view}}
    </th>
    {{if $facture->cloture}}
      <tr>
        <td colspan="10">
          <div class="small-info">
          La facture est terminée.<br />
          Pour pouvoir ajouter des éléments, veuillez réouvrir la facture.
          </div>
        </td>
      </tr>
    {{/if}}
    {{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed}}
      {{if $facture->cloture && isset($factures|smarty:nodefaults) && count($factures)}}
        <tr>
          <td colspan="10">
            <button class="printPDF" onclick="printFacture('{{$facture->_id}}', 0, 1);">Edition des BVR</button>
            <button class="cut" onclick="Facture.cutFacture('{{$facture->_id}}');"
              {{if $facture->_nb_factures != 1 || $facture->_reglements_total_patient}}disabled="disabled"{{/if}}> Eclatement</button>
            <button class="print" onclick="printFacture('{{$facture->_id}}', 1, 0);">Justificatif de remboursement</button>
          </td>
        </tr>
      {{/if}}
      <tr>
        <td colspan="2"> Type de la facture:
          <form name="type_facture" method="post" action=""> 
            {{mb_class object=$facture}}
            {{mb_key   object=$facture}}
            <input type="hidden" name="not_load_banque" value="{{if isset($factures|smarty:nodefaults) && count($factures)}}0{{else}}1{{/if}}" />
            <input type="radio" name="type_facture" value="maladie" {{if $facture->type_facture == 'maladie'}}checked{{/if}} onchange="Facture.modifCloture(this.form);" />
            <label for="maladie">Maladie</label>
            <input type="radio" name="type_facture" value="accident" {{if $facture->type_facture == 'accident'}}checked{{/if}} onchange="Facture.modifCloture(this.form);" />
            <label for="accident">Accident</label>
          </form>
        </td>
        <td colspan="8">
          <form name="cession_facture" method="post" action=""> 
            {{mb_class object=$facture}}
            {{mb_key   object=$facture}}
            <input type="hidden" name="not_load_banque" value="{{if isset($factures|smarty:nodefaults) && count($factures)}}0{{else}}1{{/if}}" />
            <input type="hidden" name="cession_creance" value="{{if $facture->cession_creance == 1}}0{{else}}1{{/if}}" />
            <input type="checkbox" name="cession_tmp" value="{{$facture->cession_creance}}" {{if $facture->cession_creance}}checked="checked"{{/if}} onchange="Facture.modifCloture(this.form);" />
            {{mb_label object=$facture field=cession_creance}}
          </form>
          <form name="npq_facture" method="post" action=""> 
            {{mb_class object=$facture}}
            {{mb_key   object=$facture}}
            <input type="hidden" name="not_load_banque" value="{{if isset($factures|smarty:nodefaults) && count($factures)}}0{{else}}1{{/if}}" />
            <input type="hidden" name="npq" value="{{if $facture->npq == 1}}0{{else}}1{{/if}}" />
            <input type="checkbox" name="npq_tmp" value="{{$facture->npq}}" {{if $facture->npq}}checked="checked"{{/if}} onchange="Facture.modifCloture(this.form);" />
            {{mb_label object=$facture field=npq}}
          </form>
        </td>
      </tr>
    {{/if}}
    <tr>
      <th class="category">Date</th>
      <th class="category">Code</th>
      <th class="category">Libelle</th>
      <th class="category">{{if $conf.dPccam.CCodeCCAM.use_cotation_ccam}}Base{{else}}Coût{{/if}}</th>
      {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam}}
        <th class="category">DH</th>
      {{else}}
        <th class="category">Qte</th>
        <th class="category">Coeff</th>        
      {{/if}}
      <th class="category">Montant</th>
    </tr>
    {{foreach from=$facture->_ref_consults item=_consultation}}
      {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam}}
        {{foreach from=$_consultation->_ref_actes_ccam item=_acte_ccam key="_key" name="tab"}}
          {{assign var=key value=$smarty.foreach.tab.index}}
          <tr>
            <td>{{$_consultation->_date|date_format:"%d/%m/%Y"}}</td>
            <td style="background-color:#FF69B4;">{{$_acte_ccam->code_acte}}</td>
            
            <td>{{$_consultation->_ext_codes_ccam.$key->libelleLong|truncate:70:"...":true}}</td>
            <td style="text-align:right;">{{mb_value object=$_acte_ccam field="montant_base"}}</td>
            <td style="text-align:right;">{{mb_value object=$_acte_ccam field="montant_depassement"}}</td>
            <td style="text-align:right;">{{$_acte_ccam->montant_base+$_acte_ccam->montant_depassement}}</td>
          </tr>
        {{/foreach}}
        {{foreach from=$_consultation->_ref_actes_ngap item=_acte_ngap}}
          <tr>
            <td>{{$_consultation->_date|date_format:"%d/%m/%Y"}}</td>
            <td  style="background-color:#32CD32;">{{$_acte_ngap->code}}</td>
            <td>{{$_acte_ngap->_libelle}}</td>
            <td style="text-align:right;">{{mb_value object=$_acte_ngap field="montant_base"}}</td>
            <td style="text-align:right;">{{mb_value object=$_acte_ngap field="montant_depassement"}}</td>
            <td style="text-align:right;">{{$_acte_ngap->montant_base+$_acte_ngap->montant_depassement}}</td>
          </tr>
        {{/foreach}}
      {{elseif @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed}}
        {{foreach from=$_consultation->_ref_actes_tarmed item=_acte_tarmed}}
          <tr>
            <td style="text-align:center;width:100px;">{{if $_acte_tarmed->date}}{{mb_value object=$_acte_tarmed field="date"}} {{else}}{{$_consultation->_date|date_format:"%d/%m/%Y"}}{{/if}}</td>
            <td  {{if $_acte_tarmed->code}} style="background-color:#BA55D3;width:140px;">{{mb_value object=$_acte_tarmed field="code"}}{{else}}>{{/if}}</td>
            <td style="white-space: pre-wrap;">{{if !isset($_acte_tarmed->libelle|smarty:nodefaults)}}{{$_acte_tarmed->_ref_tarmed->libelle}}{{else}}{{$_acte_tarmed->libelle}}{{/if}}</td>
            <td style="text-align:right;">
              {{if $_acte_tarmed->quantite}}
                {{$_acte_tarmed->montant_base/$_acte_tarmed->quantite|string_format:"%0.2f"}}
              {{else}}
                {{$_acte_tarmed->montant_base|string_format:"%0.2f"}}
              {{/if}}
            </td>
            <td style="text-align:right;">{{mb_value object=$_acte_tarmed field="quantite"}}</td>
            <td style="text-align:right;">{{$facture->_coeff}}</td>
            <td style="text-align:right;">{{$_acte_tarmed->montant_base*$facture->_coeff|string_format:"%0.2f"}}</td>
          </tr>
        {{/foreach}}
        {{foreach from=$_consultation->_ref_actes_caisse item=_acte_caisse}}
          <tr>
            <td style="text-align:center;width:100px;">{{$_consultation->_date|date_format:"%d/%m/%Y"}}</td>
            <td  {{if $_acte_caisse->code}} style="background-color:#DA70D6;width:140px;">{{mb_value object=$_acte_caisse field="code"}}{{else}}>{{/if}}</td>
            <td style="white-space: pre-wrap;">{{$_acte_caisse->_ref_prestation_caisse->libelle}}</td>
            <td style="text-align:right;">
              {{if $_acte_caisse->quantite}}
                {{$_acte_caisse->montant_base/$_acte_caisse->quantite|string_format:"%0.2f"}}
              {{else}}
                {{$_acte_caisse->montant_base|string_format:"%0.2f"}}
              {{/if}}
            </td>
            <td style="text-align:right;">{{mb_value object=$_acte_caisse field="quantite"}}</td>
            <td style="text-align:right;">1.00</td>
            <td style="text-align:right;">{{$_acte_caisse->montant_base}}</td>
          </tr>
        {{/foreach}}
      {{/if}}
    {{/foreach}}
    
    <tbody class="over">{{*@todo*}}
      {{assign var="nb_montants" value=$facture->_montant_factures|@count }}
      {{if $nb_montants>1}}
        {{foreach from=$facture->_montant_factures item=_montant key=key }}
          <tr>
            {{if $key==0}}<td colspan="{{if $conf.dPccam.CCodeCCAM.use_cotation_ccam}}3{{else}}4{{/if}}" rowspan="{{$nb_montants+2}}"></td>{{/if}}
            <td colspan="2">Montant n°{{$key+1}}</td>
            <td style="text-align:right;">{{$_montant|string_format:"%0.2f"}}</td>
          </tr>
        {{/foreach}}
      {{elseif !$conf.dPccam.CCodeCCAM.use_cotation_ccam}}
        <tr>
          <td colspan="4" rowspan="4"></td>
          <td colspan="2">Montant</td>
          <td style="text-align:right;">{{mb_value object=$facture field="_montant_sans_remise"}}</td>
        </tr>
      {{/if}}
      {{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed}}
        <tr>
          <td colspan="2"><b>{{mb_label object=$facture field="remise"}}</b></td>
          <td style="text-align:right;"> 
            <form name="modif_remise" method="post" onsubmit="Facture.modifCloture(this.form);">
              {{mb_class object=$facture}}
              {{mb_key   object=$facture}}
              <input type="hidden" name="patient_id" value="{{$facture->patient_id}}" />
              <input type="hidden" name="not_load_banque" value="{{if isset($factures|smarty:nodefaults) && count($factures)}}0{{else}}1{{/if}}" />                
              {{if $facture->cloture}}
                {{mb_value object=$facture field="remise"}} 
              {{else}}
                <input name="remise" type="text" value="{{$facture->remise}}" onchange="Facture.modifCloture(this.form);" size="4" />
              {{/if}}
              <br/>soit <b>{{if $facture->_montant_sans_remise!=0 && $facture->remise}}{{math equation="(y/x)*100" x=$facture->_montant_sans_remise y=$facture->remise format="%.2f"}}{{else}}0{{/if}}%</b>
            </form>
          </td>
        </tr>
      {{/if}}
      <tr>
        {{if !@$modules.tarmed->_can->read || !$conf.tarmed.CCodeTarmed.use_cotation_tarmed}}
          <td colspan="3"></td>
        {{/if}}
        <td colspan="2"><b>Montant Total</b></td>
        <td style="text-align:right;"><b>{{mb_value object=$facture field="_montant_avec_remise"}}</b></td>
      <tr>
    </tbody>
    {{if !$facture->_reglements_total_patient}}
      <tr>
        <td colspan="{{if $conf.dPccam.CCodeCCAM.use_cotation_ccam}}6{{else}}7{{/if}}">
          {{if $facture->_nb_factures ==1}}
            <form name="change_type_facture" method="post">
              {{mb_class object=$facture}}
              {{mb_key   object=$facture}}
              <input type="hidden" name="cloture" value="{{if !$facture->cloture}}{{$date}}{{/if}}" />
              <input type="hidden" name="not_load_banque" value="{{if isset($factures|smarty:nodefaults) && count($factures)}}0{{else}}1{{/if}}" />
              {{if !$facture->cloture}}
                <button class="submit" type="button" onclick="Facture.modifCloture(this.form);" >Cloturer la facture</button>
              {{elseif !isset($reglement|smarty:nodefaults) || ($facture->_ref_reglements|@count == 0)}}
                <button class="submit" type="button" onclick="Facture.modifCloture(this.form);" >Réouvrir la facture</button>
              {{/if}}
            </form>
          {{else}}
            <form name="fusionner_eclatements" method="post">
              <input type="hidden" name="dosql" value="do_fusion_facture_aed" />
              <input type="hidden" name="m" value="dPcabinet" />
              <input type="hidden" name="del" value="0" />
              {{mb_key   object=$facture}}
              <input type="hidden" name="not_load_banque" value="{{if isset($factures|smarty:nodefaults) && count($factures)}}0{{else}}1{{/if}}" />
              <button class="submit" type="button" onclick="Facture.modifCloture(this.form);" > Fusionner les éclats de facture </button>
            </form>
          {{/if}}
        </td>
      </tr>
    {{/if}}
  </table>        
{{elseif $conf.dPcabinet.CConsultation.consult_facture}}
  <fieldset>
    <b style="margin-left:300px;color:red;">Aucune facture</b>
  </fieldset>
{{/if}}


<!-- Reglement -->
{{if ($facture && $facture->cloture) || (!isset($facture|smarty:nodefaults) && isset($consult|smarty:nodefaults) && $consult->tarif && $consult->valide) }}
  <div id="reglements_facture">
    {{mb_include module=dPcabinet template="inc_vw_reglements"}}
  </div>
{{/if}}