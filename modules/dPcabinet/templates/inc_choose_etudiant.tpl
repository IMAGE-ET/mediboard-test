<table class="tbl">
  <tr>
    <td style="font-size: 1.3em; text-align: center;" colspan="10">
      ICR du projet : {{$devenir_dentaire->_total_ICR}} &mdash; ICR max du projet : {{$devenir_dentaire->_max_ICR}}
    </td>
  </tr>
  <tr>
    <th rowspan="2" class="category">{{mb_label class=CMediusers field=function_id}}</th>
    <th rowspan="2" class="category">Nom / Prénom</th>
    <th colspan="4" class="category">ICR</th>
    <th rowspan="2" class="narrow"></th>
  </tr>
  <tr>
    <th class="category" style="width: 12%;">Avancement</th>
    <th class="category" style="width: 12%;">Nb. d'actes</th>
    <th class="category" style="width: 12%;">Moyen</th>
    <th class="category" style="width: 12%;">Max réalisé</th>
  </tr>
  {{assign var=icr_base value=$conf.aphmOdonto.icr_base}}
  {{assign var=icr_max_possible value=$conf.aphmOdonto.icr_max_possible}}
  {{assign var=percent_ratio_min value=$conf.aphmOdonto.percent_ratio_min}}
  {{assign var=percent_ratio_max value=$conf.aphmOdonto.percent_ratio_max}}
  
  {{foreach from=$etudiants item=_etudiant}}
    {{assign var=_etudiant_id value=$_etudiant->_id}}
    {{assign var=etudiant_icr_calcul value=$etudiants_calcul_icr.$_etudiant_id}}
    <tr>
      <td>
        {{mb_include module=mediusers template=inc_vw_function function=$_etudiant->_ref_function}}
      </td>
      <td>
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_etudiant}}
      </td>
      {{math equation=x-y x=$icr_base y=$etudiant_icr_calcul.ICR_realise|ternary:$etudiant_icr_calcul.ICR_realise:0 assign=restant}}
      <td {{if $restant < $devenir_dentaire->_total_ICR}}style="background: #fb0{{/if}}">
        {{math equation=(x/y)*100 x=$etudiant_icr_calcul.ICR_realise|ternary:$etudiant_icr_calcul.ICR_realise:0 y=$icr_base assign=percent_realise}}
        {{if $percent_realise lt 50}}
          {{assign var="backgroundClass" value="empty"}}
        {{elseif $percent_realise lt 90}}
          {{assign var="backgroundClass" value="normal"}}
        {{elseif $percent_realise lt 100}}
          {{assign var="backgroundClass" value="booked"}}
        {{else}}
          {{assign var="backgroundClass" value="full"}}
        {{/if}} 
        <div class="progressBar">
          <div class="bar {{$backgroundClass}}" style="width: {{$percent_realise}}%;"></div>
          <div class="text">{{$etudiant_icr_calcul.ICR_realise|ternary:$etudiant_icr_calcul.ICR_realise:0}} / {{$icr_base}}</div>
        </div>
      </td>
      <td>
        {{$etudiant_icr_calcul.nombre_actes}}
      </td>
      <td>
        {{math equation=(x/y)*100 x=$etudiant_icr_calcul.ICR_moyen|ternary:$etudiant_icr_calcul.ICR_moyen:0 y=$icr_max_possible assign=percent_icr_moyen}}
        {{if $percent_icr_moyen lt 50}}
          {{assign var="backgroundClass" value="empty"}}
        {{elseif $percent_icr_moyen lt 90}}
          {{assign var="backgroundClass" value="normal"}}
        {{elseif $percent_icr_moyen lt 100}}
          {{assign var="backgroundClass" value="booked"}}
        {{else}}
          {{assign var="backgroundClass" value="full"}}
        {{/if}} 
        <div class="progressBar">
          <div class="bar {{$backgroundClass}}" style="width: {{$percent_icr_moyen}}%;"></div>
          <div class="text">{{$etudiant_icr_calcul.ICR_moyen|ternary:$etudiant_icr_calcul.ICR_moyen:0}} / {{$icr_max_possible}}</div>
        </div>
      </td>
      {{math equation=(x/y)*100 x=$etudiant_icr_calcul.ICR_max|ternary:$etudiant_icr_calcul.ICR_max:0 y=$icr_max_possible assign=percent_icr_max_etudiant}}
      {{if $percent_icr_max_etudiant lt 50}}
          {{assign var="backgroundClass" value="empty"}}
        {{elseif $percent_icr_max_etudiant lt 90}}
          {{assign var="backgroundClass" value="normal"}}
        {{elseif $percent_icr_max_etudiant lt 100}}
          {{assign var="backgroundClass" value="booked"}}
        {{else}}
          {{assign var="backgroundClass" value="full"}}
        {{/if}} 
      <td {{if $percent_icr_max_etudiant >= $percent_ratio_min && $percent_icr_max_etudiant <= $percent_ratio_max}}style="background: #0e0"{{/if}}>
        <div class="progressBar">
          <div class="bar {{$backgroundClass}}" style="width: {{$percent_icr_max_etudiant}}%;"></div>
          <div class="text">{{$etudiant_icr_calcul.ICR_max|ternary:$etudiant_icr_calcul.ICR_max:0}} / {{$icr_max_possible}}</div>
        </div>
      </td>
      <td>
        <button type="button" class="tick" onclick="window.parent.selectEtudiant('{{$_etudiant_id}}')">Choisir</button>
      </td>
    </tr>
  {{/foreach}}
</table>