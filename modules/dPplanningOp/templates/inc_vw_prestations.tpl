<script type="text/javascript">
  uncheckPrestation = function(elts) {
    elts.each(function(elt) {
      elt.checked = "";
    });
  }
</script>
{{math equation=x+2 x=$dates|@count assign="colspan"}}

<form name="add_prestation_ponctuelle" method="post" action="?"
  onsubmit="return onSubmitFormAjax(this, {onComplete: function() {
    getForm('add_prestation_ponctuelle').up('div').up('div').select('button.change')[0].onclick(); }})">
  <input type="hidden" name="m" value="dPhospi" />
  <input type="hidden" name="dosql" value="do_add_item_ponctuelle_aed" />
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
  <input type="hidden" name="item_prestation_id" value="" />
  <input type="hidden" name="date" value="" />
</form>

<div style="height: 100%; overflow-y: auto;">
  <form name="edit_prestations" method="post" action="?" onsubmit="return onSubmitFormAjax(this, {onComplete: function() {
    getForm('add_prestation_ponctuelle').up('div').up('div').select('button.change')[0].onclick(); }})">
    <input type="hidden" name="m" value="dPhospi"/>
    <input type="hidden" name="dosql" value="do_items_liaisons_aed"/>
    
    <table class="tbl">
      <tr>
        <th class="title">
          <button type="button" class="save notext" onclick="this.form.onsubmit();"></button>
        </th>
        <th class="title narrow"></th>
        <th class="title" style="width: 45%">Journalières</th>
        {{if $vue_prestation == "all"}}
          <th class="title">
            Ponctuelles
            <div>
              <input type="hidden" name="date" class="date" value="{{$sejour->entree|date_format:"%Y-%m-%d"}}"/>
              <input type="text" class="autocomplete" name="prestation_ponctuelle_view"/>
              <div class="autocomplete" id="prestation_autocomplete" style="display: none; color: #000; text-align: left;"></div>
            </div>
            <script type="text/javascript">
              Main.add(function() {
                var form = getForm("edit_prestations");
                var url = new Url("system", "httpreq_field_autocomplete");
                url.addParam("class","CItemPrestation");
                url.addParam("field", "nom");
                url.addParam("limit", 30);
                url.addParam("view_field", "name");
                url.addParam("show_view", true);
                url.addParam("input_field", "prestation_ponctuelle_view");
                url.addParam("where[object_class]", "CPrestationPonctuelle");
                url.autoComplete(form.prestation_ponctuelle_view, "prestation_autocomplete", {
                  minChars: 3,
                  method: "get",
                  select: "view",
                  dropdown: true,
                  afterUpdateElement: function(field,selected){
                    var item_prestation_id = selected.id.split("-").last();
                    var form_prestation = getForm("add_prestation_ponctuelle");
                    $V(form_prestation.item_prestation_id, item_prestation_id);
                    $V(form_prestation.date, $V(form.date));
                    form_prestation.onsubmit();
                  }
                });
                var dates = {
                  limit: {
                    start: "{{$sejour->entree}}",
                    stop:  "{{$sejour->sortie}}"
                  }
                }
                new Calendar.regField(form.date, dates);
              });
            </script>
          </th>
        {{/if}}
      </tr>
      {{assign var=affectation_id value=0}}
      {{assign var=count_prestations value=$prestations_j|@count}}
      {{math equation=100/x x=$count_prestations assign=width_prestation}}
      
      {{foreach from=$dates item=_affectation_id key=_date name=foreach_date}}
        {{assign var=first_date value=$smarty.foreach.foreach_date.first}}
      <tr>
        {{if $affectation_id != $_affectation_id}}
          {{assign var=affectation value=$affectations.$_affectation_id}}
          <th rowspan="{{$affectation->_rowspan}}" class="title narrow">
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
                <th class="title narrow"></th>
                {{foreach from=$prestations_j item=_prestation}}
                  <th style="width: {{$width_prestation}}%" class="title">{{$_prestation->nom}}</th>
                {{/foreach}}
              </tr>
              {{foreach from=$type_j item=type}}
                <tr>
                  <th class="title narrow">
                    {{tr}}CItemLiaison-{{$type}}{{/tr}}
                  </th>
                  {{foreach from=$prestations_j item=_prestation key=prestation_id}}
                    <td>
                      {{if $first_date || isset($liaisons_j.$_date.$prestation_id|smarty:nodefaults)}}
                        {{if $first_date && !isset($liaisons_j.$_date.$prestation_id|smarty:nodefaults)}}
                          {{assign var=liaison value=$empty_liaison}}
                        {{else}}
                          {{assign var=liaison value=$liaisons_j.$_date.$prestation_id}}
                        {{/if}}
                       {{* <button type="button" class="cancel notext" onclick="uncheckPrestation(this.next().select('input'))"></button>  *}}
                        <span>
                          {{foreach from=$_prestation->_ref_items item=_item}}
                            <label>
                              <input type="radio"
                                name="liaisons_j[{{$_affectation_id}}][{{$prestation_id}}][{{$_date}}][{{$type}}][{{$liaison->_id}}]"
                                onclick="var elt_hidden = this.next(); $V(elt_hidden, this.checked ? '{{$_item->_id}}' : 0); this.form.onsubmit();"
                                {{if ($type == 'souhait' && $liaison->item_prestation_id == $_item->_id) ||
                                  ($type == 'realise' && $liaison->item_prestation_realise_id == $_item->_id)}}checked="checked"{{/if}} value="{{$_item->_id}}"/>{{$_item->nom}}
                            </label>
                          {{/foreach}}
                        </span>
                      {{else}}
                        <button type="button" class="tick" onclick="this.next().show(); this.remove();">Faire évoluer</button>
                        <span style="display: none;">
                          {{foreach from=$_prestation->_ref_items item=_item}}
                            <label>
                              <input type="radio"
                                name="liaisons_j[{{$_affectation_id}}][{{$prestation_id}}][{{$_date}}][{{$type}}][new]"
                                onclick="var elt_hidden = this.next(); $V(elt_hidden, this.checked ? '{{$_item->_id}}' : 0); this.form.onsubmit();" value="{{$_item->_id}}"/>{{$_item->nom}}
                            </label>
                          {{/foreach}}
                        </span>
                      {{/if}}
                    </td>
                  {{/foreach}}
                </tr>
              {{/foreach}}
            </table>
          </td>
          {{if $vue_prestation == "all"}}
            <td>
              {{if isset($liaisons_p.$_date|smarty:nodefaults)}}
                <table class="tbl">
                  <tr>
                    {{foreach from=$liaisons_p.$_date item=_liaison key=prestation_id}}
                      {{assign var=prestation value=$prestations_p.$prestation_id}}
                      <th class="title">
                        {{$prestation->nom}}
                      </th>
                    {{/foreach}}
                  </tr>
                  <tr>
                    {{foreach from=$liaisons_p.$_date item=_liaisons_by_prestation key=prestation_id}}
                      <td>
                        {{assign var=prestation value=$prestations_p.$prestation_id}}
                        {{foreach from=$_liaisons_by_prestation item=_liaison}}
                          {{assign var=_item value=$_liaison->_ref_item}}
                          {{$_item->nom}} :
                          <input type="text" name="liaisons_p[{{$_liaison->_id}}]" value="{{$_liaison->quantite}}" style="width: 3em;" onchange="this.form.onsubmit()"/>
                          <script type="text/javascript">
                            Main.add(function() {
                               getForm('edit_prestations').elements['liaisons_p[{{$_liaison->_id}}]'].addSpinner(
                               {step: 1, min: 0});
                            });
                          </script>
                        {{/foreach}}
                      </td>
                    {{/foreach}}
                  </tr>
                </table>
              {{/if}}
            </td>
          {{/if}}
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
