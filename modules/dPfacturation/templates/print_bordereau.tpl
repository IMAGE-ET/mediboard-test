{{assign var=banque value=$praticien->_ref_banque}}
{{if !$banque->_id}} 
  <div class="small-warning">{{tr}}CMediusers-banque_id-none{{/tr}}</div>
{{/if}}

<table class="tbl">
  <tr>
    <th class="title">
     {{$banque}}
    </th>
    <th class="title" colspan="8">
      Remise de chèques
    </th>
  </tr>
  
  <tr>
    <th>Date</th>
    <th>Code Banque</th>
    <th>Code Guichet</th>
    <th>N° Compte</th>
    <th>Clé RIB</th>
    <th colspan="4">Titulaire</th>
  </tr>
  
  <tr style="text-align: center">
    <td>{{$date|date_format:$conf.date}}</td>
    <td>{{$compte_banque}}</td>
    <td>{{$compte_guichet}}</td>
    <td>{{$compte_numero}}</td>
    <td>{{$compte_cle}}</td>
    <td colspan="3">{{$praticien}}</td>
  </tr>
  
  <tr>
    <th colspan="2" class="title">Tireur</th>
    <th colspan="2" class="title">Référence</th>
    <th colspan="3" class="title">Etablissement payeur</th>
    <th class="title narrow">Montant</th>
  </tr>
  
  {{foreach from=$reglements item=_reglement}}
  <tr>
    <td colspan="2">{{$_reglement->_ref_object->_ref_patient}}</td>
    <td colspan="2">{{$_reglement->reference}}</td>
    <td colspan="3">{{$_reglement->_ref_banque}}</td>
    <td style="text-align: right;">{{mb_value object=$_reglement field=montant}}</td>
  </tr>
  {{/foreach}}
  
  <tr style="text-align: right; font-weight: bold;">
    <td colspan="4"></td>
    <td>Nombre de remises</td>
    <td>{{$nbRemise}}</td>
    <td>Montant total</td>
    <td>{{$montantTotal|currency}}</td>
  </tr>
</table>

<fieldset style="float: left; width: 47%; height: 6em;">
  <legend>Visa de la banque</legend>
</fieldset>

<fieldset style="float: right; width: 47%; height: 6em;">
  <legend>Visa du client</legend>
</fieldset>
