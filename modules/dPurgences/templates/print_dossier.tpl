<script type="text/javascript">
  Main.add(window.print);
</script>

<button class="print not-printable" onclick="window.print()">{{tr}}Print{{/tr}}</button>

</td>
</tr>
</table>

<table class="form" id="admission">
  <tr>
    <th class="title" colspan="10" style="font-size: 16px">
      Dossier d'urgence de {{$patient->_view}} [{{$patient->_IPP}}] <br />
      né(e) le {{mb_value object=$patient field=naissance}} de sexe {{if $patient->sexe == "m"}} masculin {{else}} féminin {{/if}} <br /> <hr />
      <span style="font-size: 14px">par le Dr {{$consult->_ref_praticien}} le {{mb_value object=$consult field=_date}} - Dossier [{{$sejour->_num_dossier}}]</span>
    </th>
  </tr>
  
  <tr><th class="category" colspan="10">Dossier médical</th></tr>

  <tr>
     <td>
        <table>
          <tr><th colspan="2" style="text-align: center; border: 0">Antécédents</th></tr>
          
	        {{if $dossier_medical->_count_antecedents}}
				    {{foreach from=$dossier_medical->_ref_antecedents key=curr_type item=list_antecedent}}
				    {{foreach from=$list_antecedent item=_antecedent}}
				      <tr>
				        <td colspan="2">
				          {{if $_antecedent->type    }} {{mb_value object=$_antecedent field=type    }} {{/if}}
                  {{if $_antecedent->appareil}} {{mb_value object=$_antecedent field=appareil}} {{/if}}
				          {{if $_antecedent->date}}
				            [{{mb_value object=$_antecedent field=date}}] : 
				          {{/if}}
				          {{$_antecedent->rques}}
				        </td>
				      </tr>
				    {{/foreach}}
				    {{/foreach}}
				  {{else}}
				    <tr>
				      <th></th>
				      <td>{{tr}}CAntecedent.unknown{{/tr}}</td>
				    </tr>
				  {{/if}}
				  
				  <tr><th colspan="2" style="text-align: center; border: 0">Traitements</th></tr>
				  
				  {{if $dossier_medical->_ref_prescription}}
				    {{foreach from=$dossier_medical->_ref_prescription->_ref_prescription_lines item=_line}}    
				      <tr>
				        <td colspan="2">{{$_line->_ucd_view}} ({{$_line->_forme_galenique}})</td>
				      </tr>
				    {{/foreach}}
				    {{foreach from=$dossier_medical->_ref_traitements item=curr_trmt}}
				      <tr>
				        <td colspan="2">{{$curr_trmt->traitement}}</td>
				      </tr>
				    {{foreachelse}}
				    {{if !($dossier_medical->_ref_prescription && $dossier_medical->_ref_prescription->_ref_prescription_lines|@count)}}
				      <tr>
				        <th></th>
				        <td>{{tr}}CTraitement.unknown{{/tr}}</td>
				      </tr>
				    {{/if}}
				    {{/foreach}}
				  {{/if}}
				  
				  <tr><th colspan="2" style="text-align: center; border: 0">Diagnostics CIM</th></tr>
				  
				  {{foreach from=$dossier_medical->_ext_codes_cim item=curr_code}}
				    <tr>
				      <td colspan="2">{{$curr_code->code}} - {{$curr_code->libelle}}</td>
				    </tr>
				  {{foreachelse}}
				  <tr>
				    <th></th>
				    <td>{{tr}}CDossierMedical-codes_cim.unknown{{/tr}}</td>
				  </tr>
				  {{/foreach}}
			  </table>
     </td>
     <td>
        <table>
          <tr><th colspan="2" style="text-align: center; border: 0">Examens</th></tr>
  
				  <tr>
				    <th>{{mb_label object=$consult field="motif"}}</th>
				    <td>{{mb_value object=$consult field="motif"}}</td>
				  </tr>
				  
				  <tr>
				    <th>{{mb_label object=$consult field="rques"}}</th>
				    <td>{{mb_value object=$consult field="rques"}}</td>
				  </tr>
				  
				  <tr>
				    <th>{{mb_label object=$consult field="histoire_maladie"}}</th>
				    <td>{{mb_value object=$consult field="histoire_maladie"}}</td>
				  </tr>
				  
				  <tr>
            <th>{{mb_label object=$consult field="examen"}}</th>
            <td>{{mb_value object=$consult field="examen"}}</td>
          </tr>
          
          <tr>
            <th>{{mb_label object=$consult field="conclusion"}}</th>
            <td>{{mb_value object=$consult field="conclusion"}}</td>
          </tr>
        </table>
     </td>
  </tr>
</table>

{{include file="../../dPpatients/templates/print_constantes.tpl"}}

{{include file="../../dPhospi/templates/inc_list_transmissions.tpl" without_del_form=true}}

<br style="page-break-after: always;" />

<table class="form" id="admission"> 
  <tr><th class="category" colspan="10">RPU [{{$sejour->_num_dossier}}] - Dossier administratif</th></tr>
  
  <tr><th colspan="2" style="text-align: center; border: 0">Patient - {{$patient->_view}} [{{$patient->_IPP}}]</th></tr>
  
  <tr>
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
  
  <tr><th colspan="2" style="text-align: center; border: 0">RPU</th></tr>
  
  <tr>
    <th>{{mb_label object=$rpu field="_entree"}}</th>
    <td>{{$rpu->_entree|date_format:"%d %b %Y à %Hh%M"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$rpu field="ccmu"}}</th>
    <td>{{mb_value object=$rpu field="ccmu"}}</td>
  </tr>
  
  
  <tr> 
    <th>{{mb_label object=$rpu field="mode_entree"}}</th>
    <td>{{mb_value object=$rpu field="mode_entree"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$rpu field="provenance"}}</th>
    <td>{{mb_value object=$rpu field="provenance"}}</td>
  </tr>
  
  <tr>   
    <th>{{mb_label object=$rpu field="transport"}}</th>
    <td>{{mb_value object=$rpu field="transport"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$rpu field="pec_transport"}}</th>
    <td>{{mb_value object=$rpu field="pec_transport"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$rpu field="box_id"}}</th>
    <td>{{mb_value object=$rpu field="box_id"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$sejour field="sortie_reelle"}}</th>
    <td>{{mb_value object=$sejour field="sortie_reelle"}}</td>
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
  
  <tr>
    <th>Documents</th>
    <td>
        {{foreach from=$consult->_ref_documents item=_document}}
          {{$_document->_view}} <br />
        {{/foreach}}
    </td>
  </tr>
<table>

{{include file="../../dPcabinet/templates/print_actes.tpl" without_del_form=true}}

<table>
<tr>
<td>