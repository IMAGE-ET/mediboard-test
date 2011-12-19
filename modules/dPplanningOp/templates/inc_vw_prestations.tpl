{{math equation=x+2 x=$dates|@count assign="colspan"}}

<form name="add_prestation_ponctuelle" method="post" action="?"
  onsubmit="return onSubmitFormAjax(this, {onComplete: function() {
    getForm('add_prestation_ponctuelle').up('div').up('div').select('button.change')[0].onclick(); }})">
  <input type="hidden" name="m" value="dPhospi" />
  <input type="hidden" name="dosql" value="do_fictive_ponctuelle_aed" />
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
  <input type="hidden" name="prestation_id" value="" />
</form>

<div style="overflow-x: auto;">
  <form name="edit_prestations" method="post" action="?" onsubmit="return onSubmitFormAjax(this, {onComplete: function() {
    getForm('add_prestation_ponctuelle').up('div').up('div').select('button.change')[0].onclick(); }})">
    <input type="hidden" name="m" value="dPhospi"/>
    <input type="hidden" name="dosql" value="do_items_liaisons_aed"/>
    
    <table class="tbl">
      <tr>
        <th class="title" colspan="{{$colspan}}">{{tr}}CPrestationJournaliere.all{{/tr}}</th>
      </tr>
      <tr>
        <td colspan="2"></td>
        <td colspan="{{$colspan}}">
          {{foreach from=$affectations item=_affectation}}
            <div style="width: {{$_affectation->_width}}%; display: inline-block; background: #afa;">
              <span onmouseover="ObjectTooltip.createEx(this, '{{$_affectation->_guid}}')">
                Affectation du {{$_affectation->entree|date_format:$conf.date}} au {{$_affectation->sortie|date_format:$conf.date}}
              </span>
            </div>
          {{/foreach}}
        </td>
      </tr>
      <tr>
        <th colspan="2"></th>
        {{foreach from=$dates item=_affectation_id key=_date}}
          <th>{{$_date|date_format:$conf.date}}</th>
        {{/foreach}}
      </tr>
      {{foreach from=$prestations_j item=_prestation}}
        {{assign var=prestation_id value=$_prestation->_id}}
        <tr>
          <th rowspan="2">{{$_prestation->nom}}</th>
        </tr>
        <tr>
          <th>Souhait</th>
          {{foreach from=$dates item=_affectation_id key=_date}}
            <td rowspan="2">
              <select name="prestations_j[{{$prestation_id}}][{{$_affectation_id}}][{{$_date}}][item_prestation_id]" style="width: 7em;">
                <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                {{foreach from=$_prestation->_ref_items item=_item}}
                  <option value="{{$_item->_id}}"
                    {{if isset($tableau_prestations_j.$_date.$prestation_id.souhait|smarty:nodefaults)}}
                      {{assign var=item value=$tableau_prestations_j.$_date.$prestation_id.souhait}}
                      {{if $item->_id == $_item->_id}}selected="selected"{{/if}}
                    {{/if}}>{{$_item->nom}}</option>
                {{/foreach}}
              </select>
              <br />
              <select name="prestations_j[{{$_prestation->_id}}][{{$_affectation_id}}][{{$_date}}][item_prestation_realise_id]" style="width: 7em;">
                <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                {{foreach from=$_prestation->_ref_items item=_item}}
                  <option value="{{$_item->_id}}"
                  {{if isset($tableau_prestations_j.$_date.$prestation_id.realise|smarty:nodefaults)}}
                    {{assign var=item value=$tableau_prestations_j.$_date.$prestation_id.realise}}
                    {{if $item->_id == $_item->_id}}selected="selected"{{/if}}
                  {{/if}}>{{$_item->nom}}</option>
                {{/foreach}}
              </select>
            </td>
          {{/foreach}}
        </tr>
        <tr>
          <th></th>
          <th>Réel</th>
        </tr>
      {{/foreach}}
      </table>
      <br />
      <table class="tbl">
      <tr>
        <th class="title" colspan="{{$colspan}}">
          <span style="float: left;">
          <input type="text" class="autocomplete" name="prestation_ponctuelle_view"/>
          <div class="autocomplete" id="prestation_autocomplete" style="display: none; color: #000; text-align: left;"></div>
          </span>
          {{tr}}CPrestationPonctuelle.all{{/tr}}
          <script type="text/javascript">
            Main.add(function() {
              var form = getForm("edit_prestations");
              var url = new Url("system", "httpreq_field_autocomplete");
              url.addParam("class","CPrestationPonctuelle");
              url.addParam("field", "nom");
              url.addParam("limit", 30);
              url.addParam("view_field", "name");
              url.addParam("show_view", true);
              url.addParam("input_field", "prestation_ponctuelle_view");
              url.autoComplete(form.prestation_ponctuelle_view, "prestation_autocomplete", {
                minChars: 3,
                method: "get",
                select: "view",
                dropdown: true,
                afterUpdateElement: function(field,selected){
                  var prestation_id = selected.id.split("-").last();
                  var form_prestation = getForm("add_prestation_ponctuelle");
                  $V(form_prestation.prestation_id, prestation_id);
                  form_prestation.onsubmit();
                }
              });
            });
          </script>
        </th>
      </tr>
      
      <tr>
        <td colspan="2"></td>
        <td colspan="{{$colspan}}">
          {{foreach from=$affectations item=_affectation}}
            <div style="width: {{$_affectation->_width}}%; display: inline-block; background: #afa;">
              <span onmouseover="ObjectTooltip.createEx(this, '{{$_affectation->_guid}}')">
                Affectation du {{$_affectation->entree|date_format:$conf.date}} au {{$_affectation->sortie|date_format:$conf.date}}
              </span>
            </div>
          {{/foreach}}
        </td>
      </tr>
      
      <tr>
        <th colspan="2"></th>
        {{foreach from=$dates item=_affectation_id key=_date}}
          <th>{{$_date|date_format:$conf.date}}</th>
        {{/foreach}}
      </tr>
      
      {{foreach from=$prestations_p item=_items_liaisons_by_prestation_id key=_prestation_id}}
        {{assign var=prestation value=$prestations_p.$_prestation_id}}
        <tr>
          <th rowspan="{{$prestation->_ref_items|@count}}">{{$prestation->nom}}</th>
          {{foreach from=$prestation->_ref_items item=_item name=ref_items}}
            {{assign var=item_id value=$_item->_id}}
            {{if !$smarty.foreach.ref_items.first}}
              <tr>
            {{/if}}
          
            <th>
              {{$_item->nom}}
            </th>
            {{foreach from=$dates item=_affectation_id key=_date}}
              <td>
                {{if isset($tableau_prestations_p.$_date.$_prestation_id.$item_id|smarty:nodefaults)}}
                  {{assign var=quantite value=$tableau_prestations_p.$_date.$_prestation_id.$item_id}}
                {{else}}
                  {{assign var=quantite value=0}}
                {{/if}}
                <input type="text" name="prestations_p[{{$_prestation_id}}][{{$_affectation_id}}][{{$_date}}][{{$item_id}}]"
                  value="{{$quantite}}" style="width: 3em;"/>
                <script type="text/javascript">
                  Main.add(function() {
                     getForm('edit_prestations').elements['prestations_p[{{$_prestation_id}}][{{$_affectation_id}}][{{$_date}}][{{$item_id}}]'].addSpinner(
                     {step: 1, min: 0});
                  });
                </script>
              </td>
            {{/foreach}}
          </tr>
        {{/foreach}}
        </tr>
      {{foreachelse}}
        <tr>
          <td class="empty" colspan="{{$colspan}}">{{tr}}CPrestationPonctuelle.none{{/tr}}</td>
        </tr>
      {{/foreach}}
      
      <tr>
        <td class="button" colspan="{{$colspan}}">
          <button type="button" class="save" onclick="this.form.onsubmit();">{{tr}}Save{{/tr}}</button>
          <button type="button" class="cancel" onclick="Control.Modal.close();">{{tr}}Close{{/tr}}</button>
        </td>
      </tr>
    </table>
  </form>
</div>  

