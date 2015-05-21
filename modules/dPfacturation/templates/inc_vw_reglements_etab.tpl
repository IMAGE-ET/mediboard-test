{{mb_default var=reload value=0}}
<table class="layout">
  {{foreach from=$facture->_ref_reglements_patient item=_reglement}}
    <tr>
      <td class="narrow">
        <button class="edit notext" type="button" onclick="Rapport.editReglementEtab('{{$_reglement->_id}}', '{{$facture->_guid}}');">
          {{tr}}Edit{{/tr}}
        </button>
      </td>
      <td class="narrow" style="text-align: right;"><strong>{{mb_value object=$_reglement field=montant}}</strong></td>
      <td>{{mb_value object=$_reglement field=mode}}</td>
      <td class="narrow">{{mb_value object=$_reglement field=date}}</td>
    </tr>
  {{/foreach}}
  {{if abs($facture->_du_restant) > 0.01}}
    <tr>
      <td colspan="4" class="button">
        {{assign var=new_reglement value=$facture->_new_reglement_patient}}
        <button class="add" type="button" onclick="Rapport.addReglementEtab('{{$facture->_guid}}', '{{$new_reglement->montant}}', '{{$new_reglement->mode}}');">
          {{tr}}Add{{/tr}} <strong>{{mb_value object=$new_reglement field=montant}}</strong>
        </button>
      </td>
    </tr>
  {{/if}}
</table>

{{if $reload}}
  <script>
    Main.add(function(){
      {{if $facture->_ref_reglements_patient|@count == 0}}
        $('line_'+'{{$facture->_guid}}').select('button.cancel').each(function(e){
            e.removeClassName('cancel');
            e.addClassName('tick');
        });
      {{elseif abs($facture->_du_restant) <= 0.01}}
        $('line_'+'{{$facture->_guid}}').select('button.tick').each(function(e){
          e.removeClassName('tick');
          e.addClassName('cancel');
        });
      {{/if}}
    });
  </script>
{{/if}}