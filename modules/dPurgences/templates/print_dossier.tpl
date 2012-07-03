{{assign var=tbl_class value="main tbl"}}

{{if !@$offline}}
	<script type="text/javascript">
	  Main.add(window.print);
	</script>

	</td>
	</tr>
  </table>
  {{assign var=tbl_class value="print"}}
{{/if}}

<div class="modal-content" style="text-align: left;">

<table class="{{$tbl_class}}">
  <tr>
    <th class="title" colspan="4" style="font-size: 16px; cursor: pointer;" onclick="window.print()">
      Dossier d'urgence de <span style="font-size: 20px">{{$patient->_view}}</span> {{mb_include module=patients template=inc_vw_ipp ipp=$patient->_IPP}} <br />
      né(e) le {{mb_value object=$patient field=naissance}} de sexe {{if $patient->sexe == "m"}} masculin {{else}} féminin {{/if}} <br /> <hr />
      <span style="font-size: 14px">
        par le Dr {{$consult->_ref_praticien}}
        le {{mb_value object=$consult field=_date}}
        - Dossier {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$sejour}}
      </span>
    </th>
  </tr>

  {{mb_include module=cabinet template=print_inc_dossier_medical}}
</table>

<br />
<table class="{{$tbl_class}}">
  <tr><th class="title">Constantes médicales</th></tr>
</table>
{{mb_include module=patients template=print_constantes}}

<br />
<table class="{{$tbl_class}}">
  <tr><th class="title" colspan="2">Antécédents</th></tr>
  
  <tr>
    <th style="width: 50%;">{{mb_label object=$rpu field="motif"}}</th>
    <td>{{mb_value object=$rpu field="motif"}}</td>
  </tr>
  
	{{mb_include module=cabinet template=print_inc_antecents_traitements}}
</table>

{{if !@$offline}}
  <br style="page-break-after: always;" />
{{else}}
  <br />
{{/if}}

<table class="{{$tbl_class}}">
  <tr><th class="title" colspan="4">Transmissions paramédicales de passage aux urgences</th></tr>
    
  <tr>
    <th style="width: 25%;">{{mb_label object=$sejour field="entree_reelle"}} </th>
    <td style="width: 25%;">{{mb_value object=$sejour field="entree_reelle"}} </td>
    
    <th style="width: 25%;">{{mb_label object=$sejour field="sortie_reelle"}} </th>
    <td style="width: 25%;">{{mb_value object=$sejour field="sortie_reelle"}} </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$rpu field="ccmu"}}</th>
    <td>{{mb_value object=$rpu field="ccmu"}}</td>
    
    <th>{{mb_label object=$rpu field="box_id"}}</th>
    <td>{{mb_value object=$rpu field="box_id"}}</td>
  </tr>
  
  <tr> 
    <th>{{mb_label object=$sejour field="mode_entree"}}</th>
    <td>{{mb_value object=$sejour field="mode_entree"}}</td>
		
		<th>{{mb_label object=$sejour field="provenance"}}</th>
    <td>{{mb_value object=$sejour field="provenance"}}</td>
  </tr>
  
  <tr>   
    <th>{{mb_label object=$sejour field="transport"}}</th>
    <td>{{mb_value object=$sejour field="transport"}}</td>

    <th>{{mb_label object=$rpu field="pec_transport"}}</th>
    <td>{{mb_value object=$rpu field="pec_transport"}}</td>
  </tr>
</table>

<br />
{{mb_include module=hospi template=inc_list_transmissions list_transmissions=$sejour->_ref_suivi_medical readonly=true}}

<table class="{{$tbl_class}}">
  <tr>
    <th style="width: 50%;">Documents</th>
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
</table>

<br />
<table class="{{$tbl_class}}">
  <tr><th colspan="2" class="title">Précisions sur la sortie</th></tr>
  <tr>
    <th style="width: 50%;">{{mb_label object=$rpu field="orientation"}}</th>
    <td>{{mb_value object=$rpu field="orientation"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$sejour field="destination"}}</th>
    <td>{{mb_value object=$sejour field="destination"}}</td>
  </tr>
</table>

<br />
{{mb_include module=cabinet template=print_actes readonly=true}}

</div>

{{if !@$offline}}
<table>
<tr>
<td>
{{/if}}