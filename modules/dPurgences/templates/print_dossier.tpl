<script type="text/javascript">
  Main.add(window.print);
</script>

<button class="print not-printable" onclick="window.print()">{{tr}}Print{{/tr}}</button>

</td>
</tr>
</table>

<table class="print">
  <tr>
    <th class="title" colspan="10" style="font-size: 16px">
      Dossier d'urgence de {{$patient->_view}} {{mb_include module=dPpatients template=inc_vw_ipp ipp=$patient->_IPP}} <br />
      né(e) le {{mb_value object=$patient field=naissance}} de sexe {{if $patient->sexe == "m"}} masculin {{else}} féminin {{/if}} <br /> <hr />
      <span style="font-size: 14px">par le Dr {{$consult->_ref_praticien}} le {{mb_value object=$consult field=_date}} - Dossier {{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$sejour->_num_dossier}}</span>
    </th>
  </tr>
  
  <tr><th class="category" colspan="10">Dossier médical</th></tr>
	
	<tr>
    <th>{{mb_label object=$consult field="_date"}}</th>
    <td>{{mb_value object=$consult field="_date"}}</td>
		
		<th>{{mb_label object=$sejour field="_num_dossier"}}</th>
    <td style="font-size: 20px">{{mb_value object=$sejour field="_num_dossier"}}</td>
  </tr>
	
	<tr>
    <th>{{mb_label object=$consult field="heure"}}</th>
    <td>{{mb_value object=$consult field="heure"}}</td>
  </tr>
	  
	<tr>
    <th>Patient</th>
    <td style="font-size: 20px">{{mb_value object=$patient field="_view"}} {{mb_include module=dPpatients template=inc_vw_ipp ipp=$patient->_IPP}}</td>
		
		<th>Né(e) le </th>
    <td>{{mb_value object=$patient field=naissance}} </td>
  </tr>
	
  <tr>
  	<th>{{mb_label object=$patient field="sexe"}}</th>
		<td>{{if $patient->sexe == "m"}} M {{else}} F {{/if}}</td>
		
    <th>{{mb_label object=$patient field="_age"}}</th>
    <td>{{mb_value object=$patient field="_age"}} ans</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="adresse"}}</th>
    <td>{{mb_value object=$patient field="adresse"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="cp"}} - {{mb_label object=$patient field="ville"}}</th>
    <td>{{mb_value object=$patient field="cp"}} {{mb_value object=$patient field="ville"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="tel"}}</th>
    <td>{{mb_value object=$patient field="tel"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="tel2"}}</th>
    <td>{{mb_value object=$patient field="tel2"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="medecin_traitant"}}</th>
    <td>{{mb_value object=$patient field="medecin_traitant"}}</td>
  </tr>
</table>

<hr />

<table class="print">
  <tr>
  	<th width="50%">{{mb_label object=$consult field="motif"}}</th>
    <td>{{mb_value object=$consult field="motif"}}</td>
	</tr>
	
	<tr>
		<th>Paramètres à l'arrivée</th>
		<td></td>
	</tr>
</table>

{{include file="../../dPpatients/templates/print_constantes.tpl"}}

<table class="print">
  <tr>
    <th>{{mb_label object=$rpu field="motif"}}</th>
    <td>{{mb_value object=$rpu field="motif"}}</td>
  </tr>
  
	<tr>
    <th>Antécédents</th>
    <td>
    	{{if $dossier_medical->_count_antecedents}}
        {{foreach from=$dossier_medical->_ref_antecedents key=curr_type item=list_antecedent}}
        {{foreach from=$list_antecedent item=_antecedent}}
          {{if $_antecedent->type    }} {{mb_value object=$_antecedent field=type    }} {{/if}}
          {{if $_antecedent->appareil}} {{mb_value object=$_antecedent field=appareil}} {{/if}}
          {{if $_antecedent->date}}
            [{{mb_value object=$_antecedent field=date}}] : 
          {{/if}}
          {{$_antecedent->rques}}
          <br />
        {{/foreach}}
        {{/foreach}}
      {{else}}
        <tr>
          <th></th>
          <td>{{tr}}CAntecedent.unknown{{/tr}}</td>
        </tr>
      {{/if}}
    </td>
  </tr>
  
	<tr>
    <th>Traitements</th>
    <td>
      {{if $dossier_medical->_ref_prescription}}
        {{foreach from=$dossier_medical->_ref_prescription->_ref_prescription_lines item=_line}}    
         {{$_line->_ucd_view}} ({{$_line->_forme_galenique}})
				 <br />
        {{/foreach}}
        {{foreach from=$dossier_medical->_ref_traitements item=curr_trmt}}
          {{$curr_trmt->traitement}}
          <br />
        {{foreachelse}}
        {{if !($dossier_medical->_ref_prescription && $dossier_medical->_ref_prescription->_ref_prescription_lines|@count)}}
          {{tr}}CTraitement.unknown{{/tr}}
        {{/if}}
        {{/foreach}}
      {{/if}}
    </td>
  </tr>  
	
	<tr>
    <th>Diagnostics CIM</th>
    <td>
      {{foreach from=$dossier_medical->_ext_codes_cim item=curr_code}}
        {{$curr_code->code}} - {{$curr_code->libelle}}<br />
      {{foreachelse}}
        {{tr}}CDossierMedical-codes_cim.unknown{{/tr}} <br />
      {{/foreach}}
    </td>
  </tr>     
	
	<tr>
    <th>{{mb_label object=$consult field="histoire_maladie"}}</th>
    <td>{{mb_value object=$consult field="histoire_maladie"}}</td>
  </tr>
					
  <tr>
    <th>{{mb_label object=$consult field="rques"}}</th>
    <td>{{mb_value object=$consult field="rques"}}</td>
  </tr>
	
	<tr>
    <th>{{mb_label object=$consult field="examen"}}</th>
    <td>{{mb_value object=$consult field="examen"}}</td>
  </tr>
	
	<tr>
    <th>{{mb_label object=$consult field="traitement"}}</th>
    <td>{{mb_value object=$consult field="traitement"}}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$consult field="conclusion"}}</th>
    <td>{{mb_value object=$consult field="conclusion"}}</td>
  </tr>		
</table>

<br style="page-break-after: always;" />

<table class="print">
  <tr><th class="category" colspan="10">Transmissions paramedicales de passage aux urgences</th></tr>
	
	<tr>
    <th>IPP</th>
    <td>{{$patient->_IPP}}</td>
    
    <th></th>
    <td></td>
  </tr>
    
  <tr>
    <th>{{mb_label object=$patient field="nom"}}</th>
    <td>{{mb_value object=$patient field="nom"}}</td>
    
    <th>{{mb_label object=$sejour field="entree_reelle"}} </th>
    <td>{{mb_value object=$sejour field="entree_reelle"}} </td>
  </tr>
  
	<tr>
    <th>{{mb_label object=$patient field="prenom"}}</th>
    <td>{{mb_value object=$patient field="prenom"}}</td>
    
    <th>{{mb_label object=$sejour field="sortie_reelle"}} </th>
    <td>{{mb_value object=$sejour field="sortie_reelle"}} </td>
  </tr>
	
	<tr>
    <th>Né(e) le </th>
    <td>{{mb_value object=$patient field=naissance}} </td>
    
    <th></th>
    <td></td>
  </tr>
	
  <tr>
    <th>{{mb_label object=$patient field="adresse"}}</th>
    <td>{{mb_value object=$patient field="adresse"}}</td>
		
		<th></th>
    <td></td>
  </tr>
  
  <tr>
    <th></th>
    <td>{{mb_value object=$patient field="cp"}} {{mb_value object=$patient field="ville"}}</td>
		
		<th>Personne à prévenir</th>
    <td>{{mb_value object=$patient field="prevenir_nom"}} {{mb_value object=$patient field="prevenir_prenom"}} <br /> 
		    {{mb_value object=$patient field="tel"}} 
		</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$rpu field="ccmu"}}</th>
    <td>{{mb_value object=$rpu field="ccmu"}}</td>
		
		<th></th>
    <td></td>
  </tr>
  
  
  <tr> 
    <th>{{mb_label object=$rpu field="mode_entree"}}</th>
    <td>{{mb_value object=$rpu field="mode_entree"}}</td>
		
		<th>{{mb_label object=$rpu field="provenance"}}</th>
    <td>{{mb_value object=$rpu field="provenance"}}</td>
  </tr>
  
  <tr>   
    <th>{{mb_label object=$rpu field="transport"}}</th>
    <td>{{mb_value object=$rpu field="transport"}}</td>

    <th>{{mb_label object=$rpu field="pec_transport"}}</th>
    <td>{{mb_value object=$rpu field="pec_transport"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$rpu field="box_id"}}</th>
    <td>{{mb_value object=$rpu field="box_id"}}</td>
		
		<th></th>
    <td></td>
  </tr>
</table>

<hr />

<table class="print">
  <tr>
    <th width="50%">{{mb_label object=$consult field="motif"}}</th>
    <td>{{mb_value object=$consult field="motif"}}</td>
  </tr>
</table>

{{include file="../../dPhospi/templates/inc_list_transmissions.tpl" without_del_form=true}}

<table class="print">
  <tr>
    <th>Documents</th>
    <td>
        {{foreach from=$consult->_ref_documents item=_document}}
          {{$_document->_view}} <br />
        {{/foreach}}
    </td>
  </tr>
	
  <tr>
    <th>{{mb_label object=$sejour field="mode_sortie"}}</th>
    <td>{{mb_value object=$sejour field="mode_sortie"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$rpu field="gemsa"}}</th>
    <td>{{mb_value object=$rpu field="gemsa"}}</td>
  </tr>
  
  <tr><th colspan="2" style="text-align: center; border: 0"><br />Précisions sur la sortie</th></tr>
  
  <tr>
    <th>{{mb_label object=$rpu field="orientation"}}</th>
    <td>{{mb_value object=$rpu field="orientation"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$rpu field="destination"}}</th>
    <td>{{mb_value object=$rpu field="destination"}}</td>
  </tr>
  
<table>

<table class="print">
  <tr><th class="category" colspan="10">Actes</th></tr>
</table>

{{include file="../../dPcabinet/templates/print_actes.tpl" without_del_form=true}}

<table>
<tr>
<td>