<div class="big-info">
  L'import d'actes Sherpa est pour le moment <strong>silencieux</strong>.
  <br/>
  On ne fait qu'analyser le contenu de la requête sans effectuer l'ajout proprement dit.
  <br />Le format exact du token est : 
  <pre>CODPRA|CODACT|ACTIV|PHASE|MOD1|MOD2|MOD3|MOD4|ASSOC|DEPHON|DATEACT|EXTDOC|REMBEX|CODSIG</pre>
</div>

<table class="tbl">
  <tr>
    <th class="category" colspan="10">{{$sejour->_view}}</th>
  </tr>
  
  <!-- Operations -->
	{{foreach from=$sejour->_ref_operations item=_operation}}
  <tr>
    <th class="category" colspan="10">
      Intervention du {{mb_value object=$_operation field=_datetime}}
    </th>
  </tr>

  <!-- Actes -->
	{{foreach from=$_operation->_ref_actes_ccam item=_acte}}
  <tr>
    <td>{{$_acte->code_acte}}</td>
    <td>{{$_acte->code_activite}}</td>
    <td>{{$_acte->code_phase}}</td>
  </tr>
	{{foreachelse}}
  <tr>
    <td class="text" colspan="10">
      <em>Aucun acte importé pour cette intervention</em>
    </td>
  </tr>
  {{/foreach}}

	{{foreachelse}}
  <tr>
    <td class="text" colspan="10">
      <em>Aucune intervention dans ce séjour</em>
    </td>
  </tr>
  {{/foreach}}
  
</table>
