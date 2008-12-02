<!-- Liste d'actes exportés -->
<table class="tbl">
  {{if $filter->_date_sortie}}
  <tr>
    <th class="title" colspan="10">
      Envoi d'actes pour les séjours sortis le : 
      {{$filter->_date_sortie|date_format:$dPconfig.longdate}}
    </th>
  </tr>
  {{/if}}

  <tr>
    <th>{{mb_title class=CFile field=file_id}}</th>
    <th>{{mb_title class=CFile field=file_real_filename}}</th>
    <th>{{mb_title class=CFile field=file_size}}</th>
    <th>Statut de l'envoi</th>
  </tr>

	{{foreach from=$sejours key=sejour_id item=_sejour}}
  <tr>
    <th class="title" colspan="10">
	    {{$_sejour->_view}}
	    <strong>[{{$_sejour->_num_dossier}}]</strong>
	    <br />Dr {{$_sejour->_ref_praticien->_view}}
    </th>
  </tr>

 	{{if $_sejour->_num_dossier == "-"}}
		<tr>  
	    <td colspan="10">
	    	<div class="error">
	        Le séjour #{{$_sejour->_id}} n'a pas de numéro de dossier
	        <br />Exécution interrompue. 
	      </div>
	    </td>
	  </tr>
	  
	{{else}}
		{{include file="inc_export_documents.tpl" object=$_sejour}}
	  
	  <!-- Actes du séjour -->
	  {{foreach from=$_sejour->_ref_operations item=_operation}}
	  <tr>
	    <th colspan="10">
		    Intervention du {{$_operation->_datetime}} 
		    {{if $_operation->libelle}}
		    <em>[{{$_operation->libelle}}]</em>
		    {{/if}}
		    <br />Dr {{$_operation->_ref_chir->_view}}
		    &mdash; [IDINTERV = {{$_operation->_idat}}]
	    </th>
	  </tr>
	
		{{include file="inc_export_documents.tpl" object=$_operation}}
		{{foreachelse}}
		<td colspan="10">
		  <em>{{tr}}CSejour.none{{/tr}}</em>
		</td>
		{{/foreach}}
	{{/if}}
	{{foreachelse}}
	<td colspan="10">
	  <em>{{tr}}CSejour.none{{/tr}}</em>
	</td>
  {{/foreach}}
	
</table>