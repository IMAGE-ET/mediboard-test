<!-- $Id: print_compta2.tpl 10999 2011-01-03 13:21:17Z MyttO $ -->

<table class="main">
  <tr>
    <td class="halfPane">
      <table>
        <tr>
          <th>
            <a href="#" onclick="window.print()">
              Rapport 
							{{mb_include module=system template=inc_interval_date from=$filter->_date_min to=$filter->_date_max}}
            </a>
          </th>
        </tr>
				
        <!-- Praticiens concernés -->
        {{foreach from=$listPrat item=_prat}}
        <tr>
          <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_prat}}</td>
        </tr>
        {{/foreach}}

        <tr>
          <td>Paiments pris en compte : {{if $filter->_mode_reglement}}{{$filter->_mode_reglement}}{{else}}tous{{/if}}</td>
        </tr>
      </table>
    </td>
    
    <td class="halfPane">
     
      <table class="tbl">
        <tr>
          <th class="title" colspan="7">Réglement Patients</th>
        </tr>
        <tr>
          <th class="category">Type réglement</th>
          <th class="category">Total</th>
          <th class="category">Chèque</th>
          <th class="category">CB</th>
          <th class="category">Espèces</th>
          <th class="category">Virement</th>
          <th class="category">Autre</th>
        </tr>
        <tr>
          <th class="category">Nb réglements</th>
          <td>{{$recapReglement.total.nb_reglement_patient}}</td>
          <td>{{$recapReglement.cheque.nb_reglement_patient}}</td>
          <td>{{$recapReglement.CB.nb_reglement_patient}}</td>
          <td>{{$recapReglement.especes.nb_reglement_patient}}</td>
          <td>{{$recapReglement.virement.nb_reglement_patient}}</td>
          <td>{{$recapReglement.autre.nb_reglement_patient}}</td>
        </tr>
        <tr>
          <th class="category">Total réglement patient</th>
          <td>{{$recapReglement.total.du_patient|currency}}</td>
          <td>{{$recapReglement.cheque.du_patient|currency}}</td>
          <td>{{$recapReglement.CB.du_patient|currency}}</td>
          <td>{{$recapReglement.especes.du_patient|currency}}</td>
          <td>{{$recapReglement.virement.du_patient|currency}}</td>
          <td>{{$recapReglement.autre.du_patient|currency}}</td>
        </tr>
        {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam == "1"}}
          <tr>
            <th class="title" colspan="7">Réglement Tiers</th>
          </tr>
          <tr>
            <th class="category">Type réglement</th>
            <th class="category">Total</th>
            <th class="category">Chèque</th>
            <th class="category">CB</th>
            <th class="category">Espèces</th>
            <th class="category">Virement</th>
            <th class="category">Autre</th>
          </tr>
          <tr>
            <th class="category">Nb réglements</th>
            <td>{{$recapReglement.total.nb_reglement_tiers}}</td>
            <td>{{$recapReglement.cheque.nb_reglement_tiers}}</td>
            <td>{{$recapReglement.CB.nb_reglement_tiers}}</td>
            <td>{{$recapReglement.especes.nb_reglement_tiers}}</td>
            <td>{{$recapReglement.virement.nb_reglement_tiers}}</td>
            <td>{{$recapReglement.autre.nb_reglement_tiers}}</td>
          </tr>
          <tr>
            <th class="category">Total réglement Tiers</th>
            <td>{{$recapReglement.total.du_tiers|currency}}</td>
            <td>{{$recapReglement.cheque.du_tiers|currency}}</td>
            <td>{{$recapReglement.CB.du_tiers|currency}}</td>
            <td>{{$recapReglement.especes.du_tiers|currency}}</td>
            <td>{{$recapReglement.virement.du_tiers|currency}}</td>
            <td>{{$recapReglement.autre.du_tiers|currency}}</td>
          </tr>
        {{/if}}
        <tr>
          <th class="title" colspan="7">Récapitulatif des consultations concernées</th>
        </tr>
        <tr>
          <th class="category">Nb de consultations</th>
          <td colspan="6">{{$listConsults|@count}}</td>
        </tr>
        {{if isset($recapReglement.total.secteur1|smarty:nodefaults)}}
          <tr>
            <th class="category">{{if $conf.dPccam.CCodeCCAM.use_cotation_ccam == "1"}}Total secteur 1{{else}}Total facturé{{/if}}</th>
            <td colspan="6">{{$recapReglement.total.secteur1|currency}}</td>
          </tr>
          {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam == "1"}}
            <tr>
              <th class="category">Total secteur 2</th>
              <td colspan="6">{{$recapReglement.total.secteur2|currency}}</td>
            </tr>
          {{/if}}
        {{/if}}
        <tr>
          <th class="category">Total réglé</th>
          <td colspan="6">{{$recapReglement.total.du_patient+$recapReglement.total.du_tiers|currency}}</td>
        </tr>
      </table>
    </td>
  </tr>
  {{if $filter->_type_affichage}}
  {{foreach from=$listReglements key=key_date item=_date}}
  <tr>
    <td colspan="2"><strong>Règlements du {{$key_date|date_format:$conf.longdate}}</strong></td>
  </tr>
  <tr>
    <td colspan="2">
      <table class="tbl">
        <tr>
          <th rowspan="2" style="width: 30%;">{{mb_label class=CConsultation field=_prat_id}}</th>
          <th rowspan="2" style="width: 30%;">{{mb_label class=CConsultation field=patient_id}}</th>
          <th rowspan="2" style="width: 30%;">{{mb_label class=CConsultation field=tarif}}</th>
          {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam == "1"}}
            <th rowspan="2" class="narrow">{{mb_title class=CConsultation field=secteur1}}</th>
            <th rowspan="2" class="narrow">{{mb_title class=CConsultation field=secteur2}}</th>
          {{elseif @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed == "1"}}
            <th rowspan="2" class="narrow">Montant</th>
            <th rowspan="2" class="narrow">Remise</th>
          {{/if}}
          <th rowspan="2" class="narrow">{{mb_title class=CConsultation field=_somme}}</th>
          <th rowspan="2" class="narrow">{{mb_label class=CReglement field=mode}}</th>
          <th colspan="2" class="narrow">{{mb_title class=CReglement field=emetteur}}</th>
				</tr>
				{{if $conf.dPccam.CCodeCCAM.use_cotation_ccam == "1"}}
				<tr>
          <th class="narrow">{{tr}}CReglement.emetteur.patient{{/tr}}</th>
          <th class="narrow">{{tr}}CReglement.emetteur.tiers{{/tr}}</th>
        </tr>
        {{elseif @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed == "1"}}
          <th class="narrow" colspan="2">{{tr}}CReglement.emetteur.patient{{/tr}}</th>
        {{/if}}
				{{foreach from=$_date.reglements item=_reglement}}
				{{assign var=_object value=$_reglement->_ref_object}}
        <tr>
          <td class="text">
            {{if isset($_object->_ref_chir|smarty:nodefaults)}}
              {{assign var=prat_id value=$_object->_ref_chir->_id}}
            	{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$listPrat.$prat_id}}
            {{/if}}
					</td>

          <td class="text">
          	{{assign var=patient value=$_object->_ref_patient}}
          	<span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">{{$patient}}</span>
					</td>

          <td class="text">
            {{if isset($_object->tarif|smarty:nodefaults)}}
              <span onmouseover="ObjectTooltip.createEx(this, '{{$_object->_guid}}')">
                {{mb_value object=$_object field=tarif}}
  						</span>
            {{/if}}
					</td>
					{{if isset($_object->secteur1|smarty:nodefaults)}}
            <td>{{mb_value object=$_object field=secteur1}}</td>
            <td>{{mb_value object=$_object field=secteur2}}</td>
            <td>{{mb_value object=$_object field=_somme}}</td>
          {{else}}
            <td>{{mb_value object=$_object field=_montant_sans_remise}}</td>
            <td>{{mb_value object=$_object field=remise}}</td>
            <td>{{mb_value object=$_object field=_montant_avec_remise}}</td>
          {{/if}}

          <td>{{$_reglement->mode}}</td>
          <td>
            {{if $_reglement->emetteur == "patient"}}
						  {{mb_value object=$_reglement field=montant}}
            {{/if}}
          </td>
          {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam == "1"}}
            <td>
              {{if $_reglement->emetteur == "tiers"}}
                {{mb_value object=$_reglement field=montant}}
              {{/if}}
            </td>
          {{/if}}
        </tr>
        {{/foreach}}
        <tr>
          <td colspan="6" />
          <td><strong>{{tr}}Total{{/tr}}</strong></td>
          <td><strong>{{$_date.total.patient|currency}} </strong></td>
          {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam == "1"}}
          <td><strong>{{$_date.total.tiers|currency}}</strong></td>
          {{/if}}
        </tr>
      </table>
    </td>
  </tr>
  {{/foreach}}
  {{/if}}
</table>