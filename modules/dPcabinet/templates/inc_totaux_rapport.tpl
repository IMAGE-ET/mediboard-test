<!-- $Id: print_rapport.tpl 16694 2012-09-21 10:13:33Z mytto $ -->

<div id="obsolete-totals" style="background-color: #888; display: none">
  <div class="big-warning">
    <p>Les totaux sont obsolètes suite à la saisie de règlements</p>
    <a class="button change" onclick="location.reload()">{{tr}}Refresh{{/tr}} {{tr}}Now{{/tr}}</a>
  </div>
</div>

<table id="totals" class="tbl" style="text-align: center;">
  {{foreach from=$reglement->_specs.emetteur->_list item=emetteur}}
    <tr>
      <th class="title" colspan="9">Réglements {{tr}}CReglement.emetteur.{{$emetteur}}{{/tr}}</th>
    </tr>

    <tr>
      <th>{{mb_label object=$reglement field=mode}}</th>
      <th>{{tr}}Total{{/tr}}</th>
      {{foreach from=$reglement->_specs.mode->_list item=_mode}}
      <th>{{tr}}CReglement.mode.{{$_mode}}{{/tr}}</th>
      {{/foreach}}
      <th>Impayé</th>
    </tr>

    <tr>
      <th class="category">Nb règlements</th>
      {{assign var=nb_reglement_name value="nb_reglement_$emetteur"}}
      <td>{{$recapReglement.total.$nb_reglement_name}}</td>
      {{foreach from=$reglement->_specs.mode->_list item=_mode}}
      <td>{{$recapReglement.$_mode.$nb_reglement_name}}</td>
      {{/foreach}}
      {{assign var=impaye_name value="nb_impayes_$emetteur"}}
      <td>{{$recapReglement.total.$impaye_name}}</td>
    </tr>

    <tr>
      <th class="category">Total règlements</th>
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
    <th class="title" colspan="9">Récapitulatif des consultations concernées</th>
  </tr>
  <tr>
    <th>Nb de {{tr}}CConsultation{{/tr}}</th>
    <td colspan="4">{{$recapReglement.total.nb_consultations}}</td>
  </tr>
  
  {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam == "1"}}
    <tr>
      <th>
        {{tr}}Total{{/tr}}
        {{mb_label class=CConsultation field=secteur1}}
      </th>
      <td colspan="4">{{$recapReglement.total.secteur1|currency}}</td>
      <th colspan="4">{{mb_label class=CConsultation field=_somme}}</th>
    </tr>
    <tr>
      <th>
        {{tr}}Total{{/tr}}
        {{mb_label class=CConsultation field=secteur2}}
      </th>
      <td colspan="4">{{$recapReglement.total.secteur2|currency}}</td>
      <td colspan="4" class="button">
        {{$recapReglement.total.secteur1+$recapReglement.total.secteur2|currency}}
      </td>
    </tr>
    <tr>
      <th>Total réglé patient</th>
      <td colspan="4">{{$recapReglement.total.du_patient|currency}}</td>
      <th colspan="4">Total réglé</th>
    </tr>
    <tr>
      <th>Total réglé tiers</th>
      <td colspan="4">{{$recapReglement.total.du_tiers|currency}}</td>
      <td colspan="4" class="button">
        {{$recapReglement.total.du_patient+$recapReglement.total.du_tiers|currency}}
      </td>
    </tr>
    <tr>
      <th>Total non réglé patient</th>
      <td colspan="4">{{$recapReglement.total.reste_patient|currency}}</td>
      <th colspan="4">Total non réglé</th>
    </tr>
    <tr>
      <th>Total non réglé tiers</th>
      <td colspan="4">{{$recapReglement.total.reste_tiers|currency}}</td>
      <td colspan="4" class="button">
        {{$recapReglement.total.reste_patient+$recapReglement.total.reste_tiers|currency}}
      </td>
    </tr>
  {{elseif @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed == "1"}}
    <tr>
      <th>{{mb_label class=CConsultation field=_somme}}</th>
      {{assign var=total_du value=$recapReglement.total.secteur1+$recapReglement.total.secteur2}}
      <td colspan="8">{{$total_du|currency}}</td>
    </tr>
    <tr>
      <th>Total réglé</th>
      {{assign var=regle value=$recapReglement.total.du_patient+$recapReglement.total.du_tiers}}
      <td colspan="8">{{$regle|currency}}</td>
    </tr>
    <tr>
      <th>Total non réglé</th>
      <td colspan="8">{{$total_du-$regle|currency}}</td>
    </tr>
  {{/if}}
</table>
