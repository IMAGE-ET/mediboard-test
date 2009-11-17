<!-- $Id$ -->

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
          <th class="category" colspan="7">Réglement Patients</th>
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
        <tr>
          <th class="category" colspan="7">Réglement Tiers</th>
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
        <tr>
          <th class="category" colspan="7">Récapitulatif des consultations concernées</th>
        </tr>
        <tr>
          <th class="category">Nb de consultations</th>
          <td colspan="6">{{$listConsults|@count}}</td>
        </tr>
        <tr>
          <th class="category">Total secteur 1</th>
          <td colspan="6">{{$recapReglement.total.secteur1|currency}}</td>
        </tr>
        <tr>
          <th class="category">Total secteur 2</th>
          <td colspan="6">{{$recapReglement.total.secteur2|currency}}</td>
        </tr>
        <tr>
          <th class="category">Total facturé</th>
          <td colspan="6">{{$recapReglement.total.secteur1+$recapReglement.total.secteur2|currency}}</td>
        </tr>
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
    <td colspan="2"><strong>Règlements du {{$key_date|date_format:$dPconfig.longdate}}</strong></td>
  </tr>
  <tr>
    <td colspan="2">
      <table class="tbl">
        <tr>
          <th rowspan="2" style="width: 30%;">{{mb_label class=CConsultation field=_prat_id}}</th>
          <th rowspan="2" style="width: 30%;">{{mb_label class=CConsultation field=patient_id}}</th>
          <th rowspan="2" style="width: 30%;">{{mb_label class=CConsultation field=tarif}}</th>
          <th rowspan="2" style="width:  1%;">{{mb_title class=CConsultation field=secteur1}}</th>
          <th rowspan="2" style="width:  1%;">{{mb_title class=CConsultation field=secteur2}}</th>
          <th rowspan="2" style="width:  1%;">{{mb_title class=CConsultation field=_somme}}</th>
          <th rowspan="2" style="width:  1%;">{{mb_label class=CReglement field=mode}}</th>
          <th colspan="2" style="width:  1%;">{{mb_title class=CReglement field=emetteur}}</th>
				</tr>
				
				<tr>
          <th style="width:  1%;">{{tr}}CReglement.emetteur.patient{{/tr}}</th>
          <th style="width:  1%;">{{tr}}CReglement.emetteur.tiers{{/tr}}</th>
        </tr>
        
				{{foreach from=$_date.reglements item=_reglement}}
				{{assign var=consultation value=$_reglement->_ref_consultation}}
        <tr>
          <td class="text">
            {{assign var=prat_id value=$consultation->_ref_chir->_id}}
          	{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$listPrat.$prat_id}}
					</td>

          <td class="text">
          	{{assign var=patient value=$consultation->_ref_patient}}
          	<span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">{{$patient}}</span>
					</td>

          <td class="text">
            <span onmouseover="ObjectTooltip.createEx(this, '{{$consultation->_guid}}')">
              {{mb_value object=$consultation field=tarif}}
						</span>
					</td>
					
          <td>{{mb_value object=$consultation field=secteur1}}</td>
          <td>{{mb_value object=$consultation field=secteur2}}</td>
          <td>{{mb_value object=$consultation field=_somme}}</td>

          <td>{{$_reglement->mode}}</td>
          <td>
            {{if $_reglement->emetteur == "patient"}}
						  {{mb_value object=$_reglement field=montant}}
            {{/if}}
          </td>
          <td>
            {{if $_reglement->emetteur == "tiers"}}
              {{mb_value object=$_reglement field=montant}}
            {{/if}}
          </td>
        </tr>
        {{/foreach}}
        <tr>
          <td colspan="6" />
          <td><strong>{{tr}}Total{{/tr}}</strong></td>
          <td><strong>{{$_date.total.patient|currency}} </strong></td>
          <td><strong>{{$_date.total.tiers|currency}}</strong></td>
        </tr>
      </table>
    </td>
  </tr>
  {{/foreach}}
  {{/if}}
</table>
      