{{mb_default var=editRights value=0}}

<script>
  uncheckPrestation = function(elts) {
    elts.each(function(elt) {
      elt.checked = "";
    });
  };
  
  openModal = function(id_div) {
    var div = $(id_div);
    window.save_checked = div.select("input").pluck("checked");
    Modal.open(id_div).observe("afterClose",
      function() {
        Prestations.urlPresta.refreshModal();
      }
    );
  };
  
  closeModal = function(id_div) {
    var div = $(id_div);
    div.select("input").each(function(elt, index) {
      elt.checked = window.save_checked[index];
    });
    Control.Modal.close();
  };
  
  autoRealiser = function(input) {
    var name = input.name.replace("souhait", "realise").replace("new", "temp");
    
    $A(input.form.elements[name]).each(function(elt) {
      if (elt.value == input.value) {
        elt.checked = "checked";
      }
    });
  };
  
  switchToNew = function(input) {
    input.name = input.name.replace("[temp]", "[new]");
  };

  switchToNewSousItem = function(input) {
    var input_item = input.up('fieldset').down('legend').down('input');
    input_item.checked = true;
    input.up('td').select('.sous_item').each(function(input) {
      input.checked = false;
    });
    input.checked = true;
    switchToNew(input_item);
    switchToNew(input);
  };

  onSubmitLiaisons = function(form, callback) {
    return onSubmitFormAjax(form, function() {
      if (Object.isFunction(callback)) {
        callback();
      }
      else {
        Prestations.urlPresta.refreshModal();
      }
    });
  };

  emptyLiaison = function(button) {
    var tr_souhait = button.up("tr").next("tr");
    var tr_realise = tr_souhait.next("tr");
    var input_souhait = tr_souhait.down("input[type=radio]");
    var input_realise = tr_realise.down("input[type=radio]");
    input_souhait.checked = "checked";
    input_souhait.onclick();
    input_realise.checked = "checked";
    input_realise.onclick();
  };

  removeLiaisons = function(date) {
    var form = getForm("delLiaisons");
    $V(form.date, date);
    onSubmitFormAjax(form, function() {
      Prestations.urlPresta.refreshModal();
    });
  };
</script>
{{math equation=x+2 x=$dates|@count assign="colspan"}}

<form name="add_prestation_ponctuelle" method="post" action="?" onsubmit="return onSubmitLiaisons(this);">
  <input type="hidden" name="m" value="hospi" />
  <input type="hidden" name="dosql" value="do_add_item_ponctuelle_aed" />
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
  <input type="hidden" name="item_prestation_id" value="" />
  <input type="hidden" name="date" value="" />
</form>

<form name="delLiaisons" method="post">
  <input type="hidden" name="m" value="hospi" />
  <input type="hidden" name="dosql" value="do_remove_liaisons_for_date" />
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
  <input type="hidden" name="date" />
</form>

<div style="height: 100%; overflow-y: auto;">
  <form name="edit_prestations" method="post" action="?" onsubmit="return onSubmitLiaisons(this);">
    <input type="hidden" name="m" value="hospi"/>
    <input type="hidden" name="dosql" value="do_items_liaisons_aed" />
    <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
    
    <table class="tbl">
      <tr>
        <th class="title narrow"></th>
        <th class="title" style="max-width: 45%" colspan="{{$prestations_j|@count}}">Journalières</th>
        {{if $context == "all"}}
          <th class="title">
            Ponctuelles
          </th>
        {{/if}}
      </tr>
      
      {{assign var=count_prestations value=$prestations_j|@count}}
      {{math equation=45/x x=$count_prestations assign=width_prestation}}
      
      <tr>
        <th class="narrow"></th>
        {{foreach from=$prestations_j item=_prestation}}
          <th style="width: {{$width_prestation}}%" class="text">{{$_prestation->nom}}</th>
        {{/foreach}}
        {{if $context == "all"}}
          <th>
              {{if $editRights}}
                <input type="hidden" name="date" class="date" value="{{$today_ponctuelle}}"/>
                <input type="text" class="autocomplete" name="keywords"/>
                <div class="autocomplete" id="prestation_autocomplete" style="display: none; color: #000; text-align: left;"></div>
              {{/if}}
              <script>
                Main.add(function() {
                  var form = getForm("edit_prestations");
                  var url = new Url("hospi", "ajax_item_prestation_autocomplete");
                  url.autoComplete(form.keywords, "prestation_autocomplete", {
                    minChars: 3,
                    method: "get",
                    select: "view",
                    dropdown: true,
                    afterUpdateElement: function(field,selected) {
                      var item_prestation_id = selected.get("id");
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
                  };
                  new Calendar.regField(form.date, dates);
                });
              </script>
          </th>
        {{/if}}
      </tr>
      {{foreach from=$dates item=_affectation_id key=_date name=foreach_date}}
        {{assign var=first_date value=$smarty.foreach.foreach_date.first}}
        {{assign var=day value=$_date|date_format:"%A"|upper|substr:0:1}}
        <tr class="{{if $_date == $relative_date}}border-bold{{/if}}">
          <td class="{{if $_date == $today}}current_hour{{/if}}"
            style="
            {{if $day == "S" || $day == "D"}}
              background-color: #ccc;
            {{elseif in_array($day, $bank_holidays)}}
              background-color: #fc0;
            {{/if}}">

            <span>
              {{if $editRights}}
                <button type="button" class="{{if array_key_exists($_date, $date_modified)}}edit{{else}}add{{/if}} notext" onclick="openModal('edit_{{$_date}}');"></button>
              {{/if}}
              <strong>{{$_date|date_format:"%d/%m"}} {{$day}}</strong>
            </span>
            
            <div id="edit_{{$_date}}" style="display: none;">
              <table class="form">
                <th class="title" colspan="2">
                  {{$_date|date_format:$conf.date}}
                </th>
                {{foreach from=$prestations_j item=_prestation key=prestation_id}}
                  {{if isset($liaisons_j.$_date.$prestation_id|smarty:nodefaults)}} 
                    {{assign var=liaison value=$liaisons_j.$_date.$prestation_id}}
                  {{else}}
                    {{assign var=liaison value=$empty_liaison}}
                  {{/if}}
                  <tr>
                    <th class="section" colspan="2">
                      {{mb_include module=system template=inc_object_history object=$liaison}}
                      {{$_prestation}}
                    </th>
                  </tr>
                  <tr>
                    <th>
                      Souhait
                    </th>
                    <td>
                      {{mb_include module=planningOp template=inc_vw_prestations_line}}
                    </td>
                  </tr>
                  <tr {{if $context != "all" || !$conf.dPhospi.show_realise}}style="display: none;"{{/if}}>
                    <th>
                      Réalisé
                    </th>
                    <td>
                      {{mb_include module=planningOp template=inc_vw_prestations_line type=realise}}
                    </td>
                  </tr>
                {{/foreach}}
                <tr>
                  <td class="button" colspan="2">
                    <button type="button" class="tick" onclick="onSubmitLiaisons(this.form, Control.Modal.close)">{{tr}}Validate{{/tr}}</button>
                    <button type="button" class="cancel" onclick="closeModal('edit_{{$_date}}')">{{tr}}Close{{/tr}}</button>
                  </td>
                </tr>
              </table>
            </div>
          </td>
          
          {{mb_include module=planningOp template=inc_vw_prestations_case_journaliere}}
          
          {{if $context == "all"}}
            {{mb_include module=planningOp template=inc_vw_prestations_case_ponctuelle}}
          {{/if}}
        </tr>
      {{/foreach}}

      {{if $dates_after|@count}}
        <tr>
          <th class="section" colspan="{{math equation=x+2 x=$prestations_j|@count}}">
            Prestations hors séjour
          </th>
        </tr>
        {{foreach from=$dates_after item=_date}}
          <tr>
            <td>
              <button type="button" class="cancel notext" onclick="removeLiaisons('{{$_date}}')">{{tr}}Delete{{/tr}}</button>
              <strong>{{$_date|date_format:"%d/%m"}} {{$day}}</strong>
            </td>

            {{mb_include module=planningOp template=inc_vw_prestations_case_journaliere}}

            {{if $context == "all"}}
              {{mb_include module=planningOp template=inc_vw_prestations_case_ponctuelle}}
            {{/if}}
          </tr>
        {{/foreach}}
      {{/if}}
    </table>
    <p style="text-align: center"><button type="button" class="cancel" onclick="Control.Modal.close();">{{tr}}Close{{/tr}}</button></p>
  </form>
</div>
