<!-- $Id$ -->

{{if !$ajax}} 

<script type="text/javascript">	
PlageConsult = {
  refresh: function(plage_id) {
    var url = new Url("dPcabinet", "print_rapport");
    url.addParam("plage_id", plage_id);
    url.requestUpdate("CPlageconsult-" + plage_id);
  },
  onSubmit: function(form, plage_id) {
    return onSubmitFormAjax(form, { 
      onComplete: function() {
        PlageConsult.showObsolete();
        PlageConsult.refresh(plage_id);
      }
    } );
  },
  showObsolete: function() {
	  var oStyle = {
      opacity: 0.9,
      position: 'absolute',
    }
    $("obsolete-totals").setStyle(oStyle).clonePosition("totals").show();
  }
}
</script>

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
          <td>Règlements pris en compte : {{if $filter->_mode_reglement}}{{$filter->_mode_reglement}}{{else}}tous{{/if}}</td>
        </tr>
        {{if $filter->_etat_reglement_patient}}
        <tr>
          <td>
            Paiment patients :
            {{tr}}CConsultation._etat_reglement_tiers.{{$filter->_etat_reglement_tiers}}{{/tr}}
          </td>
        </tr>
        {{/if}}
        {{if $filter->_etat_reglement_tiers}}
        <tr>
          <td>
            Paiment tiers :
            {{tr}}CConsultation._etat_reglement_tiers.{{$filter->_etat_reglement_tiers}}{{/tr}}
          </td> 
        </tr>
        {{/if}}
      </table>
    </td>
    
    <td class="halfPane">
    	<div id="obsolete-totals" style="background-color: #888; display: none">
	      <div class="big-warning">
	        <p>Les totaux sont obsolètes suite à la saisie de règlements</p>
	        <a class="button change" onclick="location.reload()">{{tr}}Refresh{{/tr}} {{tr}}Now{{/tr}}</a>
	      </div>
    	</div>
			
      <table id="totals" class="tbl">
      	{{foreach from=$reglement->_specs.emetteur->_list item=emetteur}}
        <tr>
          <th class="category" colspan="8">Réglement {{tr}}CReglement.emetteur.{{$emetteur}}{{/tr}}</th>
        </tr>

        <tr>
          <th class="category">{{mb_label object=$reglement field=mode}}</th>
          <th class="category">{{tr}}Total{{/tr}}</th>
					{{foreach from=$reglement->_specs.mode->_list item=_mode}}
          <th class="category">{{tr}}CReglement.mode.{{$_mode}}{{/tr}}</th>
					{{/foreach}}
          <th class="category">Impayé</th>
        </tr>

        <tr>
          <th class="category">Nb réglements</th>
					{{assign var=nb_reglement_name value="nb_reglement_$emetteur"}}
          <td>{{$recapReglement.total.$nb_reglement_name}}</td>
          {{foreach from=$reglement->_specs.mode->_list item=_mode}}
          <td>{{$recapReglement.$_mode.$nb_reglement_name}}</td>
          {{/foreach}}
          <td>{{$recapReglement.total.$nb_reglement_name}}</td>
        </tr>

        <tr>
          <th class="category">Total réglements</th>
          {{assign var=du_name value="du_$emetteur"}}
          <td>{{$recapReglement.total.$du_name|currency}}</td>
          {{foreach from=$reglement->_specs.mode->_list item=_mode}}
          <td>{{$recapReglement.$_mode.$du_name|currency}}</td>
          {{/foreach}}
          {{assign var=reste_name value="reste_$emetteur"}}
          <td>{{$recapReglement.total.$reste_name|currency}}</td>
        </tr>
				{{/foreach}}
				 
        <tr>
          <th class="category" colspan="8">Récapitulatif des consultations concernées</th>
        </tr>
        <tr>
          <th class="category">Nb de {{tr}}CConsultation{{/tr}}</th>
          <td colspan="7">{{$recapReglement.total.nb_consultations}}</td>
        </tr>
        <tr>
          <th class="category">
          	{{tr}}Total{{/tr}}
						{{mb_label class=CConsultation field=secteur1}}
					</th>
          <td colspan="3">{{$recapReglement.total.secteur1|currency}}</td>
          <th colspan="4">{{mb_label class=CConsultation field=_somme}}</th>
        </tr>
        <tr>
          <th class="category">
            {{tr}}Total{{/tr}}
            {{mb_label class=CConsultation field=secteur1}}
  				</th>
          <td colspan="3">{{$recapReglement.total.secteur2|currency}}</td>
          <td colspan="4" class="button">
            {{$recapReglement.total.secteur1+$recapReglement.total.secteur2|currency}}
          </td>
        </tr>
        <tr>
          <th class="category">
          	Total réglé patient</th>
          <td colspan="3">{{$recapReglement.total.du_patient|currency}}</td>
          <th colspan="4">Total réglé</th>
        </tr>
        <tr>
          <th class="category">Total réglé tiers</th>
          <td colspan="3">{{$recapReglement.total.du_tiers|currency}}</td>
          <td colspan="4" class="button">
            {{$recapReglement.total.du_patient+$recapReglement.total.du_tiers|currency}}
          </td>
        </tr>
        <tr>
          <th class="category">Total non réglé patient</th>
          <td colspan="3">{{$recapReglement.total.reste_patient|currency}}</td>
          <th colspan="4">Total non réglé</th>
        </tr>
        <tr>
          <th class="category">Total non réglé tiers</th>
          <td colspan="3">{{$recapReglement.total.reste_tiers|currency}}</td>
          <td colspan="4" class="button">
            {{$recapReglement.total.reste_patient+$recapReglement.total.reste_tiers|currency}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
{{/if}}
	
  {{if $filter->_type_affichage}}
  {{foreach from=$listPlages item=_plage}}
	{{if !$ajax}} 
  <tbody id="{{$_plage.plage->_guid}}">
  {{/if}}
		
  <tr>
    <td colspan="2">
      <strong onclick="PlageConsult.refresh('{{$_plage.plage->_id}}')">
          {{$_plage.plage->_ref_chir}}
        &mdash;

        {{$_plage.plage->date|date_format:$dPconfig.longdate}}
        de {{$_plage.plage->debut|date_format:$dPconfig.time}} 
				à  {{$_plage.plage->fin|date_format:$dPconfig.time}} 

				{{if $_plage.plage->libelle}} 
        : {{mb_value object=$_plage.plage field=libelle}}	   
				{{/if}}

      </strong>
    </td>
  </tr>
  
	<tr>
    <td colspan="2">
      <table class="tbl">
        <tr>
          <th style="width: 20%;">{{mb_label class=CConsultation field=patient_id}}</th>
          <th style="width: 20%;">{{mb_label class=CConsultation field=tarif}}</th>
          <th style="width: 01%;">{{mb_title class=CConsultation field=secteur1}}</th>
          <th style="width: 01%;">{{mb_title class=CConsultation field=secteur1}}</th>
          <th style="width: 01%;">{{mb_title class=CConsultation field=_somme}}</th>
          <th style="width: 20%;">{{mb_title class=CConsultation field=du_patient}}</th>
          <th style="width: 20%;">{{mb_title class=CConsultation field=du_tiers}}</th>
        </tr>
        {{foreach from=$_plage.consultations item=_consultation}}
        <tr>
          <td class="text">
          	<a name="consult-{{$_consultation->_id}}">
          		{{assign var=patient value=$_consultation->_ref_patient}}
							<span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
                {{$patient}}
							</span>
						</a>
					</td>
          <td class="text">
          	{{if $_consultation->tarif}} 
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_consultation->_guid}}')">
              {{$_consultation->tarif}}
            </span>
          	{{/if}}
          </td>
          <td>{{mb_value object=$_consultation field=secteur1}}</td>
          <td>{{mb_value object=$_consultation field=secteur2}}</td>
          <td>{{mb_value object=$_consultation field=_somme}}</td>
          <td>
            {{foreach from=$_consultation->_ref_reglements_patient item=_reglement}}
              <form name="reglement-del-{{$_reglement->_id}}" action="?m={{$m}}" method="post" onsubmit="return PlageConsult.onSubmit(this, '{{$_plage.plage->_id}}');">
              <input type="hidden" name="m" value="dPcabinet" />
              <input type="hidden" name="del" value="1" />
              <input type="hidden" name="dosql" value="do_reglement_aed" />
              {{mb_key object=$_reglement}}
              <button class="remove" type="submit">{{mb_value object=$_reglement field=montant}}</button>
							<span style="min-width: 60px; display: inline-block;">{{mb_value object=$_reglement field=mode}}</span>
							{{$_reglement->date|date_format:$dPconfig.date}}
              </form>
              <br />
            {{/foreach}}
						
            {{if $_consultation->_du_patient_restant > 0}}
            <form name="reglement-add-patient-{{$_consultation->_id}}" action="?m={{$m}}" method="post" onsubmit="return PlageConsult.onSubmit(this, '{{$_plage.plage->_id}}');">
            <input type="hidden" name="m" value="dPcabinet" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="dosql" value="do_reglement_aed" />
            <input type="hidden" name="date" value="now" />
            <input type="hidden" name="emetteur" value="patient" />
            {{mb_field object=$_consultation field="consultation_id" hidden=1 prop=""}}
            {{mb_field object=$_consultation->_new_patient_reglement field="montant"}}
            {{mb_field object=$_consultation->_new_patient_reglement field="mode"}}
            <select name="banque_id" style="width: 70px;">
              <option value="">&mdash; {{tr}}CReglement-banque_id{{/tr}}</option> 
               {{foreach from=$banques item=banque}}
               <option value="{{$banque->_id}}">{{$banque}}</option>
               {{/foreach}}
            </select>
            <button class="add notext" type="submit">+</button>
            </form>
            {{/if}}
          </td>
					
          <td>
            {{foreach from=$_consultation->_ref_reglements_tiers item=_reglement}}
              <form name="reglement-del-{{$_reglement->_id}}" action="?m={{$m}}" method="post" onsubmit="return PlageConsult.onSubmit(this, '{{$_plage.plage->_id}}');">
              <input type="hidden" name="m" value="dPcabinet" />
              <input type="hidden" name="del" value="1" />
              <input type="hidden" name="dosql" value="do_reglement_aed" />
              {{mb_key object=$_reglement}}
              <button class="remove" type="submit">{{mb_value object=$_reglement field=montant}}</button>
              <span style="min-width: 60px; display: inline-block;">{{mb_value object=$_reglement field=mode}}</span>
              {{$_reglement->date|date_format:$dPconfig.date}}
              </form>
              <br />
            {{/foreach}}
						
            {{if $_consultation->_du_tiers_restant > 0}}
            <form name="reglement-add-tiers-{{$_consultation->_id}}" action="?m={{$m}}" method="post" onsubmit="return PlageConsult.onSubmit(this, '{{$_plage.plage->_id}}');">
            <input type="hidden" name="m" value="dPcabinet" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="dosql" value="do_reglement_aed" />
            <input type="hidden" name="date" value="now" />
            <input type="hidden" name="emetteur" value="tiers" />
            {{mb_field object=$_consultation field="consultation_id" hidden=1 prop=""}}
            {{mb_field object=$_consultation->_new_tiers_reglement field="montant"}}
            {{mb_field object=$_consultation->_new_tiers_reglement field="mode"}}
            <select name="banque_id" style="width: 70px;">
              <option value="">&mdash; {{tr}}CReglement-banque_id{{/tr}}</option> 
               {{foreach from=$banques item=banque}}
               <option value="{{$banque->_id}}">{{$banque}}</option>
               {{/foreach}}
            </select>
            <button class="add notext" type="submit">+</button>
            </form>
            {{/if}}
          </td>
        </tr>
        {{/foreach}}
        <tr>
          <td colspan="2" style="text-align: right" >
					  <strong>{{tr}}Total{{/tr}}</strong>
					</td>
          <td><strong>{{$_plage.total.secteur1|currency}}</strong></td>
          <td><strong>{{$_plage.total.secteur2|currency}}</strong></td>
          <td><strong>{{$_plage.total.total|currency}}</strong></td>
          <td><strong>{{$_plage.total.patient|currency}}</strong></td>
          <td><strong>{{$_plage.total.tiers|currency}}</strong></td>
        </tr>
      </table>
    </td>
  </tr>
	
	{{if !$ajax}} 
  </tbody>
	{{/if}}
  {{/foreach}}
  {{/if}}
	
	
{{if !$ajax}} 
</table>
{{/if}}
