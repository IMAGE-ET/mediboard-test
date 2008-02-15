<!-- Liste d'actes exportés -->
<table class="tbl">
  {{if $filter->_date_sortie}}
  <tr>
    <th class="title" colspan="12">
      Envoi d'actes pour les séjours sortis le : 
      {{$filter->_date_sortie|date_format:"%A %d %B %Y"}}
    </th>
  </tr>
  {{/if}}

  <tr>
    <td>{{mb_title object=$acte_ccam field=executant_id}}</td>
    <td>{{mb_title object=$acte_ccam field=execution}}</td>
    <td>{{mb_title object=$acte_ccam field=code_acte}}</td>
    <td>{{mb_title object=$acte_ccam field=code_activite}}</td>
    <td>{{mb_title object=$acte_ccam field=code_phase}}</td>
    <td>{{mb_title object=$acte_ccam field=modificateurs}}</td>
    <td>{{mb_title object=$acte_ccam field=rembourse}}</td>
    <td>{{mb_title object=$acte_ccam field=code_association}}</td>
    <td>{{mb_title object=$acte_ccam field=montant_base}}</td>
    <td>{{mb_title object=$acte_ccam field=montant_depassement}}</td>
    <td>{{mb_title object=$acte_ccam field=signe}}</td>
    <td>Statut de l'envoi</td>
    
  </tr>

	{{foreach from=$sejours key=sejour_id item=_sejour}}
  <tr>
    <th class="title" colspan="11">
    {{$_sejour->_view}}
    <strong>[{{$_sejour->_num_dossier}}]</strong>
    <br />Dr. {{$_sejour->_ref_praticien->_view}}
    
    </th>
    <td>
      <div class="warning">
        {{tr}}CSpEntCCAM{{/tr}} : {{$delEntCCAM.$sejour_id}} suppressions pour ce séjour 
      </div>
      <div class="warning">
        {{tr}}CSpDetCCAM{{/tr}} : {{$delDetCCAM.$sejour_id}} suppressions pour ce séjour 
      </div>
      <div class="warning">
        {{tr}}CSpDetCIM{{/tr}} : {{$delDetCIM.$sejour_id}} suppressions pour ce séjour 
      </div>
			{{include file="inc_export_entccam.tpl" _codable=$_sejour}}
    </td>
  </tr>
  
  <!-- Actes du séjour -->
  {{include file="inc_export_detcim.tpl" _codable=$_sejour}}
  {{foreach from=$_sejour->_ref_actes_ccam item=_acte_ccam}}
	{{include file="inc_export_acte.tpl" _acte_ccam=$_acte_ccam}}
  {{foreachelse}}
  <tr>
    <td colspan="12"><em>Pas d'acte d'hospitalisation</em></td>
  </tr>
  {{/foreach}}

  {{foreach from=$_sejour->_ref_operations item=_operation}}
  <tr>
    <th colspan="11">
	    Opération du {{$_operation->_datetime}} 
	    {{if $_operation->libelle}}
	    <em>[{{$_operation->libelle}}]</em>
	    {{/if}}
	    <br />Dr. {{$_operation->_ref_chir->_view}}
	    &mdash; [IDINTERV = {{$_operation->_idinterv}}]
    </th>
    <td>
			{{include file="inc_export_entccam.tpl" _codable=$_operation}}
		</td>    
  </tr>

  {{include file="inc_export_detcim.tpl" _codable=$_operation}}
  {{foreach from=$_operation->_ref_actes_ccam item=_acte_ccam}}
	{{include file="inc_export_acte.tpl" _acte_ccam=$_acte_ccam}}
  {{foreachelse}}
  <tr>
    <td colspan="11"><em>Pas d'acte d'intervention</em></td>
  </tr>
  {{/foreach}}
  {{/foreach}}


	{{/foreach}}
</table>