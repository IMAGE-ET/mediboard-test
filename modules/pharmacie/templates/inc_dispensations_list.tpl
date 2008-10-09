<script type="text/javascript">
  $('list-dispensations-count').update({{$dispensations|@count}});
</script>

<table class="tbl">
  {{if $mode_nominatif}}
  <tr>
    <th colspan="10" class="title">
      Dispensation pour {{$prescription->_ref_object->_ref_patient->_view}}
    </th>
  </tr>
  {{/if}}
  <tr>
    <th>Quantité de la prise</th>
    <th>Besoin</th>
    <th>Calcul</th>
    <th>Quantité</th>
    <th>Stock pharmacie</th>
    <th>Déjà effectuées</th>
    <th>Dispensation</th>
    <th>Stock du service</th>
  </tr>
  {{foreach from=$dispensations key=code_cip item=unites}}
    {{assign var=medicament value=$medicaments.$code_cip}}
    {{assign var=_patients value=$patients.$code_cip}}
    <tr>
      <th colspan="10">{{$medicament->libelle}}</th>
    </tr>
    <tbody class="hoverable">
    {{foreach from=$unites key=unite_prise item=quantite name="dispensation"}}
      <tr>
        <td>
          {{$quantite}} {{$unite_prise}} 
          {{if array_key_exists($code_cip, $warning) && array_key_exists($unite_prise, $warning.$code_cip)}}
            <img src="images/icons/warning.png" alt="Poids non renseigné" title="Poids non renseigné" />
          {{/if}}   
        </td>
        <td>
        {{if array_key_exists($code_cip,$quantites_reference) && array_key_exists($unite_prise, $quantites_reference.$code_cip)}}
          {{if $mode_nominatif}}
            {{$quantites_reference.$code_cip.$unite_prise}} {{$medicament->libelle_unite_presentation}}
          {{else}}
            <div onmouseover="ObjectTooltip.create(this, {mode: 'dom',  params: {element: 'tooltip-content-{{$code_cip}}'} })"
                 class="tooltip-trigger">
              <a href="#1">{{$quantites_reference.$code_cip.$unite_prise}} {{$medicament->libelle_unite_presentation}}</a>
            </div>
            <div id="tooltip-content-{{$code_cip}}" style="display: none; text-align: left;">
              <ul>
                {{foreach from=$_patients item=_patient}}
                  <li>{{$_patient->_view}}</li>
                {{/foreach}}
              </ul>
            </div>
          {{/if}}
        {{/if}}
        </td>
        
        {{if $smarty.foreach.dispensation.first}}
        <td rowspan="{{$unites|@count}}" style="text-align: center" class="text">
          {{if $medicament->nb_presentation == 1}}
            {{$quantites_reference.$code_cip.total}} / {{$medicament->nb_unite_presentation}} ({{$medicament->libelle_unite_presentation}})
          {{else}}
            {{assign var=_nb value=$medicament->nb_unite_presentation*$medicament->nb_presentation}}
             {{$quantites_reference.$code_cip.total}} / {{$_nb}} ({{$medicament->libelle_unite_presentation}})
          {{/if}}
        
        </td>
        <td rowspan="{{$unites|@count}}" style="text-align: center">
          {{$quantites.$code_cip}} {{$medicament->libelle_conditionnement}}
        </td>
        <td rowspan="{{$unites|@count}}" style="text-align: center">
          {{if array_key_exists($code_cip,$delivrances)}}
            {{assign var=delivrance value=$delivrances.$code_cip}}
            {{$delivrance->_ref_stock->quantity}}
          {{else}}
            0
          {{/if}}
        </td>
        <td rowspan="{{$unites|@count}}" style="text-align: left">       
          {{foreach from=$done.$code_cip item=curr_done name="done"}}
            {{if !$smarty.foreach.done.first}}
                {{foreach from=$curr_done->_ref_delivery_traces item=trace}}
                <div id="tooltip-content-{{$curr_done->_id}}" style="display: none;">délivré le {{$trace->date_delivery|@date_format:"%d/%m/%Y"}}</div>
                <div class="tooltip-trigger" 
                     onmouseover="ObjectTooltip.create(this, {mode: 'dom',  params: {element: 'tooltip-content-{{$curr_done->_id}}'} })">
                  {{$curr_done->quantity}} {{$medicament->libelle_conditionnement}} le {{$curr_done->date_dispensation|@date_format:"%d/%m/%Y"}}
                  <img src="images/icons/tick.png" alt="Délivré" title="Délivré" />
                </div>
                {{foreachelse}}
                  {{$curr_done->quantity}} {{$medicament->libelle_conditionnement}} le {{$curr_done->date_dispensation|@date_format:"%d/%m/%Y"}}
                  <form name="form-dispensation-del-{{$curr_done->_id}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: refreshLists})">
                    <input type="hidden" name="m" value="dPstock" />
                    <input type="hidden" name="dosql" value="do_delivery_aed" />
                    <input type="hidden" name="del" value="1" />
                    <input type="hidden" name="delivery_id" value="{{$curr_done->_id}}" />
                    <button type="submit" class="cancel notext" title="Annuler">Annuler</button>
                  </form>
                  <br />
              {{/foreach}}
            {{/if}}
          {{foreachelse}}
            Aucune
          {{/foreach}}
        </td>
        <td rowspan="{{$unites|@count}}" style="text-align: center">
        {{if array_key_exists($code_cip,$delivrances)}}
          {{assign var=delivrance value=$delivrances.$code_cip}}
          {{if $delivrance->_ref_stock->quantity>0}}
          <script type="text/javascript">prepareForm('form-dispensation-{{$code_cip}}');</script>
          <form name="form-dispensation-{{$code_cip}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: refreshLists})">
            <input type="hidden" name="m" value="dPstock" />
            <input type="hidden" name="tab" value="{{$tab}}" />
            <input type="hidden" name="dosql" value="do_delivery_aed" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="date_dispensation" value="now" />
            <input type="hidden" name="stock_id" value="{{$delivrance->stock_id}}" />
            <input type="hidden" name="service_id" value="{{$delivrance->service_id}}" />
            {{if $mode_nominatif}}
            <input type="hidden" name="patient_id" value="{{$prescription->_ref_object->_ref_patient->_id}}" />
            {{/if}}
            {{mb_field object=$delivrance field=quantity form="form-dispensation-$code_cip" increment=1 size=3 min=0}}
            <button type="submit" class="tick notext" title="Dispenser">Dispenser</button>
          </form>
          {{else}}
          Stock épuisé à la pharmacie
          {{/if}}
        {{else}}
        Pas de stock à la pharmacie
        {{/if}}
        </td>
        <td rowspan="{{$unites|@count}}" style="text-align: center">
        {{if $stocks_service.$code_cip}}
          {{assign var=stock_service value=$stocks_service.$code_cip}}
          {{if $stock_service->quantity>0}}
            {{$stock_service->quantity}}
          {{else}}
            0
          {{/if}}
          {{$medicament->libelle_conditionnement}}
        {{/if}}
        </td>
        {{/if}}
      </tr>
    {{/foreach}}
    </tbody>
  {{foreachelse}}
    <tr>
      <td colspan="10">{{tr}}CProductDelivery.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>