<script type="text/javascript">

// Parcours de toutes les checkbox, 3 cas possibles:
// - checkbox disabled: on ne fait rien
// - checkbox decoché: on supprime le code
// - checkbox coché: on rajoute le code
submitCdarrs = function(){
  var form = getForm('editCodes');
  $V(form.added_codes, '');
  $V(form.remed_codes, '');

  // Parcours des checkbox
  $$('.list-codes input[type="checkbox"]:not(.disabled)').each(function(checkbox) {
    var added_codes = new TokenField(form.added_codes);
    var remed_codes = new TokenField(form.remed_codes);
    (checkbox.checked ? added_codes : remed_codes).add(checkbox.value);
  });

  return onSubmitFormAjax(form, function() {
    refreshPlanningsSSR();
    modalWindow.close();
  });
};

updateFieldCodeModal = function(selected) {
  var code_selected = selected.childElements()[0].textContent;
  selected.up(3).down('.other_codes').insert({ bottom:
    DOM.span({},
      DOM.input({
        type: 'hidden',
        id: 'editCodes__codes['+code_selected+']',
        name:'_codes['+code_selected+']',
        value: code_selected
      }),
      DOM.button({
        className: "cancel notext",
        type: "button",
        onclick: "deleteCode(this)"
      }),
      DOM.label({}, code_selected)
    )
  });
};

Main.add(function() {
  {{foreach from=$actes key=_type item=_actes}}
    var url = new Url("ssr", "httpreq_do_{{$_type}}_autocomplete");
    url.autoComplete(getForm("editCodes").code_{{$_type}}, "other_{{$_type}}_auto_complete", {
      dropdown: true,
      minChars: 2,
      select: "value",
      updateElement: updateFieldCodeModal
    } );
  {{/foreach}}
});

</script>

<form name="editCodes" action="?" method="post">
  <input type="hidden" name="m" value="ssr" />
  <input type="hidden" name="dosql" value="do_codes_multi_aed" />
  <input type="hidden" name="token_evts" value="{{$token_evts}}" />
  <input type="hidden" name="added_codes" />
  <input type="hidden" name="remed_codes" />

{{assign var="count_events" value=$evenements|@count}}
<table class="main tbl">
  <tr>
    <th colspan="5" class="title">Evenements sélectionnés</th>
  </tr>
  <tr>
    <th colspan="2">{{mb_label class="CEvenementSSR" field="debut"}}</th>
    <th class="narrow">{{mb_label class="CEvenementSSR" field="duree"}}</th>
    <th class="narrow">CdARR</th>
    <th class="narrow">CsARR</th>
  </tr>
  {{foreach from=$evenements item=_evenement}}
  <tr>
    <td class="narrow">{{$_evenement->debut|date_format:"%A"}}</td>
    <td>{{mb_value object=$_evenement field="debut"}}</td>
    <td style="text-align: right;">{{mb_value object=$_evenement field="duree"}} min</td>
    <td style="text-align: center;">{{$_evenement->_ref_actes_cdarr|@count|nozero}}</td>
    <td style="text-align: center;">{{$_evenement->_ref_actes_csarr|@count|nozero}}</td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="5" class="empty">Aucun événement sélectionné</td>
  </tr>
  {{/foreach}}

  {{foreach from=$actes key=_type item=_actes}}

  <tr>
    <th colspan="5">{{tr}}CEvenementSSR-back-actes_{{$_type}}{{/tr}}</th>
  </tr>

  <tr class="list-codes">
    <td colspan="5" class="text">
      <!--
        strong: les actes présents sur tous les événements (checked)
        opacity: les actes présents sur certains événements
      -->
      {{foreach from=$_actes key=_code item=_acte}}
         <span style="whitespace: nowrap; display: inline-block;">
          {{if array_key_exists($_code, $count_actes.$_type)}}
            {{if $count_actes.$_type.$_code == $count_events}}
              <input name="{{$_type}}[{{$_code}}]" type="checkbox" checked="checked" value="{{$_code}}" />
              <strong onmouseover="ObjectTooltip.createEx(this, '')">
                {{$_code}}
              </strong>
            {{else}}
              <!-- Activation de la checkbox -->
              <input name="{{$_type}}[{{$_code}}]" type="checkbox" checked="checked" class="disabled" value="{{$_code}}" onclick="this.removeClassName('disabled');"/>
              <span onmouseover="ObjectTooltip.createEx(this, '')">
                {{$_code}}
              </span>
            {{/if}}
          {{else}}

            <span onmouseover="ObjectTooltip.createEx(this, 'CActivite{{$_type}}-{{$_code}}')">
              <input name="{{$_type}}[{{$_code}}]" type="checkbox" value="{{$_code}}" /> {{$_code}}
            </span> 
          {{/if}}
         </span>
      {{foreachelse}}
        <div class="empty">{{tr}}CEvenementSSR-back-actes_{{$_type}}.empty{{/tr}}</div>
      {{/foreach}}
    </td>
  </tr>

  {{if $_type != "cdarr"}}
    <tr>
      <td colspan="5" class="text">
        <input type="text" name="code_{{$_type}}" class="autocomplete" canNull=true size="6" />
        <div style="display: none;" class="autocomplete" id="other_{{$_type}}_auto_complete"></div>
        <span class="other_codes"></span>
      </td>
    </tr>
  {{/if}}

  {{/foreach}}


  <tr>
    <td colspan="5" class="button">
      <button type="button" class="cancel" onclick="modalWindow.close();">{{tr}}Close{{/tr}}</button>
       <button type="button" class="submit" onclick="submitCdarrs();">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
</table>

</form>