
<div id="listActesNGAP">
  <form name="editNGAP" method="post" action=""> 
    <input type="hidden" name="consultation_id" value="{{$consult->_id}}" />
    <input type="hidden" name="m" value="{{$m}}" />
    <input type="hidden" name="dosql" value="do_acte_ngap_aed" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="acte_ngap_id" value="" />
    <table class="form">
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
          <button type="button" class="submit" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: refreshListActesNGAP } )">Valider</button>
        </td>     
      </tr>
    
      {{foreach from=$listActesNGAP item="_acteNGAP"}}
      <tr>
        <td>
          {{mb_value object=$_acteNGAP field="quantite"}}
        </td>
        <td>
          {{mb_value object=$_acteNGAP field="code"}}
        </td>
        <td>
          {{mb_value object=$_acteNGAP field="coefficient"}}  
        </td>
        <td>
         <button type="button" class="trash" onclick="deleteActeNGAP({{$_acteNGAP->_id}})">Supprimer</button>
        </td>
     </tr>
     {{/foreach}}
   </table>
  </form>
</div>
