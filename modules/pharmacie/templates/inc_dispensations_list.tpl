<table class="tbl">
  <tr>
    <th>Quantité de la prise</th>
    <th>Quantité calculée (unité de référence)</th>
    <th>Calcul</th>
    <th>Quantité</th>
    <th>Déjà effectuées</th>
    <th>Dispensation</th>
  </tr>
  {{foreach from=$dispensations key=code_cip item=unites}}
    {{assign var=medicament value=$medicaments.$code_cip}}
    <tr>
      <th colspan="6">{{$medicament->libelle}}</th>
    </tr>
    <tbody class="hoverable">
    {{foreach from=$unites key=unite_prise item=quantite name="dispensation"}}
      <tr>
        <td>{{$quantite}} {{$unite_prise}}</td>
        <td>
        {{if array_key_exists($code_cip,$quantites_reference) && array_key_exists($unite_prise, $quantites_reference.$code_cip)}}
          {{$quantites_reference.$code_cip.$unite_prise}} {{$medicament->libelle_unite_presentation}}
        {{/if}}
        </td>
        
        {{if $smarty.foreach.dispensation.first}}
        <td rowspan="{{$unites|@count}}" style="text-align: center" />{{$quantites_reference.$code_cip.total}} / {{$medicament->nb_unite_presentation}} ({{$medicament->libelle_unite_presentation}})</td>
        <td rowspan="{{$unites|@count}}" style="text-align: center">{{$quantites.$code_cip}} {{$medicament->libelle_conditionnement}}</td>
        <td rowspan="{{$unites|@count}}" style="text-align: left">
          {{foreach from=$done.$code_cip item=curr_done name="done"}}
            {{if !$smarty.foreach.done.first}}
              {{$curr_done->quantity}} {{$medicament->libelle_conditionnement}} le {{$curr_done->date_dispensation|@date_format:"%d/%m/%Y à %Hh%M"}}
              {{if $curr_done->date_delivery}}(délivré le {{$curr_done->date_delivery|@date_format:"%d/%m/%Y à %Hh%M"}}){{/if}}
              <br />
            {{/if}}
          {{foreachelse}}
            Aucune
          {{/foreach}}
        </td>
        <td rowspan="{{$unites|@count}}" style="text-align: center">
        {{if array_key_exists($code_cip,$delivrances)}}
          {{assign var=delivrance value=$delivrances.$code_cip}}
         
          <script type="text/javascript">prepareForm('form-dispensation-{{$code_cip}}');</script>
          <form name="form-dispensation-{{$code_cip}}" action="?" method="post">
            <input type="hidden" name="m" value="dPstock" />
            <input type="hidden" name="tab" value="{{$tab}}" />
            <input type="hidden" name="dosql" value="do_delivery_aed" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="date_dispensation" value="now" />
            <input type="hidden" name="stock_id" value="{{$delivrance->stock_id}}" />
            <input type="hidden" name="service_id" value="{{$delivrance->service_id}}" />
            {{if $delivrance->quantity==0 && $done.$code_cip.0!=0}}<div style="color: red;">Dispensations déjà effectuées</div>{{/if}}
            {{mb_field object=$delivrance field=quantity form="form-dispensation-$code_cip" increment=1 size=3}}
            <button type="button" class="tick" onclick="submitFormAjax(this.form, 'systemMsg', {onComplete: refreshDeliveriesList})">
              Délivrer
            </button>
          </form>
        {{else}}
        Pas de stock
        {{/if}}
        </td>
        {{/if}}
      </tr>
    {{/foreach}}
    </tbody>
  {{/foreach}}
</table>