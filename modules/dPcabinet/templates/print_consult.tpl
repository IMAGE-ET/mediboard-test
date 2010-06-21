{{if !@$offline}}
  <script type="text/javascript">
    Main.add(window.print);
  </script>
  <button class="print not-printable" onclick="window.print()">{{tr}}Print{{/tr}}</button>
  </td>
  </tr>
  </table>
  
  {{assign var=tbl_class value="print"}}
{{/if}}

<table class="{{$tbl_class}}">
  <tr>
    <th class="title" colspan="10" style="font-size: 16px">
      Dossier de consultation de <span style="font-size: 20px">{{$patient->_view}}</span> {{mb_include module=dPpatients template=inc_vw_ipp ipp=$patient->_IPP}} <br />
      né(e) le {{mb_value object=$patient field=naissance}} de sexe {{if $patient->sexe == "m"}} masculin {{else}} féminin {{/if}} <br /> <hr />
      <span style="font-size: 14px">par le Dr {{$consult->_ref_praticien}} le {{mb_value object=$consult field=_date}} - Dossier {{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$sejour->_num_dossier}}</span>
    </th>
  </tr>

  {{mb_include module=dPcabinet template=print_inc_dossier_medical}}
</table>
{{include file="../../dPpatients/templates/print_constantes.tpl"}}

<table class="{{$tbl_class}}">
  {{mb_include module=dPcabinet template=print_inc_constantes}}
</table>

{{if !@$offline}}
  <br style="page-break-after: always;" />
{{/if}}

<table class="{{$tbl_class}}">
  <tr>
    <th width="50%">{{mb_label object=$consult field="motif"}}</th>
    <td>{{mb_value object=$consult field="motif"}}</td>
  </tr>
</table>

{{include file="../../dPhospi/templates/inc_list_transmissions.tpl" without_del_form=true}}

<table class="{{$tbl_class}}">
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
  
</table>

<table class="{{$tbl_class}}">
  <tr><th class="category" colspan="10">Actes</th></tr>
</table>

{{include file="../../dPcabinet/templates/print_actes.tpl" without_del_form=true}}

{{if !@$offline}}
<table>
<tr>
<td>
{{/if}}