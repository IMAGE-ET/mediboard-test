<table class="tbl">
    <tr>
      <th>{{mb_label object=$acte_ngap field="quantite"}}</th>
      <th>{{mb_label object=$acte_ngap field="code"}}</th>
      <th>{{mb_label object=$acte_ngap field="coefficient"}}</th>
      <th>Action</th>
    </tr>
    
    <tr>
      <td>
        {{mb_field object=$acte_ngap field="quantite"}}
      </td>
      <td>
        {{mb_field object=$acte_ngap field="code"}}
      </td>
      <td>
        {{mb_field object=$acte_ngap field="coefficient"}}  
      </td>
      <td>
        <button type="button" class="submit" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: ActesNGAP.refreshList } )">Valider</button>
      </td>     
    </tr>
</table>