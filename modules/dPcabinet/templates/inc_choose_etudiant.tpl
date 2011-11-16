<table class="tbl">
  <tr>
    <th rowspan="2" class="category">{{mb_label class=CMediusers field=function_id}}</th>
    <th rowspan="2" class="category">Nom / Prénom</th>
    <th colspan="5" class="category">ICR</th>
    <th rowspan="2" class="narrow"></th>
  </tr>
  <tr>
    <th class="category" style="min-width: 7%;">Réalisé</th>
    <th class="category" style="min-width: 7%;">A réaliser</th>
    <th class="category" style="min-width: 7%;">Nb. d'actes</th>
    <th class="category" style="min-width: 7%;">Moyen</th>
    <th class="category" style="min-width: 7%;">Max réalisé</th>
    
  </tr>
  
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
      <td>
        {{$etudiant_icr_calcul.ICR_realise}}
      </td>
      <td>
        {{$etudiant_icr_calcul.ICR_pending}}
      </td>
      <td>
        {{$etudiant_icr_calcul.nombre_actes}}
      </td>
      <td>
        {{$etudiant_icr_calcul.ICR_moyen}}
      </td>
      <td>
        {{$etudiant_icr_calcul.ICR_max}}
      </td>
      <td>
        <button type="button" class="tick" onclick="window.parent.selectEtudiant('{{$_etudiant_id}}')">Choisir</button>
      </td>
    </tr>
  {{/foreach}}
</table>