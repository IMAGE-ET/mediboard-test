{{math equation=x+2 x=$dates|@count assign="colspan"}}

<form name="add_prestation_ponctuelle" method="post" action="?"
  onsubmit="return onSubmitFormAjax(this, {onComplete: function() {
    getForm('add_prestation_ponctuelle').up('div').up('div').select('button.change')[0].onclick(); }})">
  <input type="hidden" name="m" value="dPhospi" />
  <input type="hidden" name="dosql" value="do_fictive_ponctuelle_aed" />
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
  <input type="hidden" name="prestation_id" value="" />
</form>

<div style="height: 100%; overflow-y: auto;">
  <form name="edit_prestations" method="post" action="?" onsubmit="return onSubmitFormAjax(this, {onComplete: function() {
    getForm('add_prestation_ponctuelle').up('div').up('div').select('button.change')[0].onclick(); }})">
    <input type="hidden" name="m" value="dPhospi"/>
    <input type="hidden" name="dosql" value="do_items_liaisons_aed"/>
    
    <table class="tbl">
      <tr>
        <th>
          <button type="button" class="save notext" onclick="this.form.onsubmit();"></button>
        </th>
        <th class="narrow"></th>
        <th style="width: 45%">Ponctuelles</th>
        <th>
          Journalières
          <div style="float: right;">
            <input type="text" class="autocomplete" name="prestation_ponctuelle_view"/>
            <div class="autocomplete" id="prestation_autocomplete" style="display: none; color: #000; text-align: left;"></div>
          </div>
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
      {{assign var=affectation_id value=0}}
      {{foreach from=$dates item=_affectation_id key=_date}}
      <tr>
        {{if $affectation_id != $_affectation_id}}
          {{assign var=affectation value=$affectations.$_affectation_id}}
          <th rowspan="{{$affectation->_rowspan}}" class="narrow">
            <span onmouseover="ObjectTooltip.createEx(this, '{{$affectation->_guid}}')">
               {{vertical}}
                 {{$affectation->_ref_lit}}
               {{/vertical}}
             </span>
          </th>
        {{/if}}
        
          <td>
            {{$_date|date_format:$conf.date}}
          </td>
          <td>
            <table class="tbl">
              <tr>
                <th class="narrow"></th>
                {{foreach from=$prestations_j item=_prestation}}
                  <th>{{$_prestation->nom}}</th>
                {{/foreach}}
              </tr>
              <tr>
                <th class="narrow">
                  Souhait
                </th>
                {{foreach from=$prestations_j item=_prestation key=prestation_id}}
                  <td class="narrow">
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
                  </td>
                {{/foreach}}
              </tr>
              <tr>
                <th class="narrow">
                  Réalisé
                </th>
                {{foreach from=$prestations_j item=_prestation key=_prestation_id}}
                  <td>
                    <select name="prestations_j[{{$_prestation_id}}][{{$_affectation_id}}][{{$_date}}][item_prestation_realise_id]" style="width: 7em;">
                      <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                      {{foreach from=$_prestation->_ref_items item=_item}}
                        <option value="{{$_item->_id}}"
                        {{if isset($tableau_prestations_j.$_date.$_prestation_id.realise|smarty:nodefaults)}}
                          {{assign var=item value=$tableau_prestations_j.$_date.$_prestation_id.realise}}
                          {{if $item->_id == $_item->_id}}selected="selected"{{/if}}
                        {{/if}}>{{$_item->nom}}</option>
                      {{/foreach}}
                    </select> 
                  </td>
                {{/foreach}}
              </tr>
            </table>
          </td>
          <td>
            {{if $prestations_p|@count}}
              <table class="tbl">
                {{foreach from=$prestations_p item=_prestation key=_prestation_id}}
                  <tr>
                    <th>
                      {{$_prestation->nom}}
                    </th>
                    <td>
                    {{foreach from=$_prestation->_ref_items item=_item key=_item_id name=ref_items}}
                      {{if isset($tableau_prestations_p.$_date.$_prestation_id.$_item_id|smarty:nodefaults)}}
                        {{assign var=quantite value=$tableau_prestations_p.$_date.$_prestation_id.$_item_id}}
                      {{else}}
                        {{assign var=quantite value=0}}
                      {{/if}}
                      {{$_item->nom}} :
                      <input type="text" name="prestations_p[{{$_prestation_id}}][{{$_affectation_id}}][{{$_date}}][{{$_item_id}}]"
                        value="{{$quantite}}" style="width: 3em;"/>
                      <script type="text/javascript">
                        Main.add(function() {
                           getForm('edit_prestations').elements['prestations_p[{{$_prestation_id}}][{{$_affectation_id}}][{{$_date}}][{{$_item_id}}]'].addSpinner(
                           {step: 1, min: 0});
                        });
                      </script>
                    {{/foreach}}
                    </td>
                  </tr>
                {{/foreach}}
              </table>
            {{/if}}
          </td>
        </tr>
        {{assign var=affectation_id value=$_affectation_id}}
      {{/foreach}}
    </table>
    <tr>
        <td class="button" colspan="{{$colspan}}">
          <button type="button" class="save" onclick="this.form.onsubmit();">{{tr}}Save{{/tr}}</button>
          <button type="button" class="cancel" onclick="Control.Modal.close();">{{tr}}Close{{/tr}}</button>
        </td>
      </tr>
  </form>
</div>
