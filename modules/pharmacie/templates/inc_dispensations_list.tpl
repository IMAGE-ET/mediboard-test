<table class="tbl">
  <tr>
    <th>Quantité de la prise</th>
    <th>Quantité calculée (unité de référence)</th>
    <th>Nombre</th>
    <th>Dispensation</th>
  </tr>
  {{foreach from=$dispensations key=code_cip item=unites}}
    {{assign var=medicament value=$medicaments.$code_cip}}
    <tr>
      <th colspan="5">{{$medicament->libelle}}</th>
    </tr>
    <tbody class="hoverable">
    {{foreach from=$unites key=unite_prise item=quantite name="dispensation"}}
      <tr>
        <td>{{$quantite}} {{$unite_prise}}</td>
        <td>
        {{if array_key_exists($code_cip,$quantites_traduites) && array_key_exists($unite_prise, $quantites_traduites.$code_cip)}}
          {{$quantites_traduites.$code_cip.$unite_prise}} {{$medicament->libelle_unite_presentation}}
        {{/if}}
        </td>
        {{if $smarty.foreach.dispensation.first}}
        <td rowspan="{{$unites|@count}}" style="text-align: center">
          <div id="tooltip-content-{{$code_cip}}" style="display: none;">{{$code_cip}}</div>
          <div class="tooltip-trigger" 
               onmouseover="ObjectTooltip.create(this, {mode: 'dom',  params: {element: 'tooltip-content-{{$code_cip}}'} })">
            {{$quantites.$code_cip}} {{$medicament->libelle_conditionnement}}
          </div>
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