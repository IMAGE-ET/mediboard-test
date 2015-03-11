<script>
  function addAntecedent(event, rques, date, type, appareil, input) {
    if (event && event.ctrlKey) {
      window.save_params = { 'input': input, 'type': type, 'appareil': appareil};
      var complete_atcd = $('complete_atcd');
      $V(complete_atcd.down("textarea"), rques);
      Modal.open(complete_atcd);
      return;
    }
    if (window.opener) {
      var oForm = window.opener.getForm('editAntFrm');
      if (oForm) {
        if ($V(oForm._patient_id) != "{{$patient->_id}}") {
          alert("Veuillez fermer cette fenêtre, elle ne concerne pas ce patient.");
          input.checked = false;
          return;
        }
        oForm.rques.value = rques;
        oForm.type.value = type;
        oForm.appareil.value = appareil;
        if (oForm.date) {
          oForm.date.value = date;
        }

        window.opener.onSubmitAnt(oForm);

        input.disabled = 'disabled';

        $(input).up('td').setStyle({cursor: 'default', opacity: 0.3});
      }
    }
    window.focus();
  }

  completeAtcd = function() {
    var form = getForm("completeAtcdFom");
    addAntecedent(null, $V(form.rques), $V(form.date), window.save_params.type, window.save_params.appareil, window.save_params.input);
    emptyDate(form);
  };

  emptyDate = function(form) {
    form.select("input.date").each(function(input) {
      $V(input, "");
    });
  };

  var oFormAntFrmGrid;

  Main.add(function () {
    Control.Tabs.create('tab-antecedents', false);

    var oFormAnt = window.opener.document.editAntFrm;
    oFormAntFrmGrid = document.editAntFrmGrid;
    $V(oFormAntFrmGrid._patient_id,  oFormAnt._patient_id.value);
    if(oFormAnt._sejour_id){
      $V(oFormAntFrmGrid._sejour_id,  oFormAnt._sejour_id.value);
    }

    $$(".droppable").each(function(li) {
      Droppables.add(li, {
        onDrop: function(from, to, event) {
          var parent = from.up("ul");
          if (parent != to.up("ul") || !to) {
            return;
          }
          // S'ils sont côte à côte, juste insérer le premier après le deuxième
          if (from.next("li") == to) {
            from = from.remove();
            to.insert({after: from});
            return;
          }
          if (from.previous("li") == to) {
            from = from.remove();
            to.insert({before: from});
            return;
          }

          // Sinon on sauvegarde la position et on insère
          // Cas du dernier élément
          var next = from.next("li");
          if (next) {
            from = from.remove();
            to.insert({before: from});
            to = to.remove();
            next.insert({before: to});
            return;
          }

          var previous = from.previous("li");
          if (previous) {
            from = from.remove();
            to.insert({after: from});
            to = to.remove();
            previous.insert({after: to});
            return;
          }
        },
        accept: 'draggable',
        hoverclass: "atcd_hover"
      });
    });

    $$(".draggable").each(function(li) {
      new Draggable(li, {
        onEnd: function() {
          var form = getForm("editPref");
          var pref_tabs = {};

          $("tab-antecedents").select("a").each(function(a) {

            var appareils = $(a.href.split("#")[1]).select("a").invoke("get", "appareil").join("|");
            var type = a.get("type");
            pref_tabs[type] = appareils;
          });

          $V(form.elements["pref[order_mode_grille]"], Object.toJSON(pref_tabs));
          onSubmitFormAjax(form);
        },
        revert: true });
      });

    Calendar.regProgressiveField($('date_atcd'));
  });
</script>

<form name="editPref" method="post">
  <input type="hidden" name="m" value="admin" />
  <input type="hidden" name="dosql" value="do_preference_aed" />
  <input type="hidden" name="user_id" value="{{$user_id}}" />
  <input type="hidden" name="pref[order_mode_grille]" value="{{$order_mode_grille|@json}}" />
</form>

<div id="complete_atcd" style="display: none; width: 500px; height: 180px;">
  <form name="completeAtcdFom" method="get">
    <table class="form">
      <tr>
        <th class="title" colspan="3">
          Compléter l'antécédent
        </th>
      </tr>
      <tr>
        <th style="height: 100%;" class="narrow">{{mb_label class=CAntecedent field=date}}</th>
        <td class="narrow">
          <input type="hidden" name="date" class="date" id="date_atcd" />
        </td>
        <td>
          <textarea name="rques"></textarea>
        </td>
      </tr>
      <tr>
        <td colspan="3" class="button">
          <button type="button" class="tick" onclick="Control.Modal.close(); completeAtcd();">
            {{tr}}Validate{{/tr}}
           </button>
          <button type="button" class="close" onclick="Control.Modal.close(); window.save_params.input.checked = ''; emptyDate(this.form);">{{tr}}Close{{/tr}}</button>
        </td>
      </tr>
    </table>
  </form>
</div>

<!-- Antécédents -->
{{assign var=numCols value=4}}
{{math equation="100/$numCols" assign=width format="%.1f"}}
<table id="antecedents" class="main" style="display: none;">
  <tr>
    <td colspan="3">
      <form name="editAntFrmGrid" action="?m=dPcabinet" method="post" onsubmit="return window.opener.onSubmitAnt(this)">
        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_antecedent_aed" />
        <input type="hidden" name="_patient_id" value="" />
        <input type="hidden" name="_sejour_id" value="" />
      
        <input type="hidden" name="_hidden_rques" value="" />
        <input type="hidden" name="rques" onchange="this.form.onsubmit();"/>
       
        <input type="hidden" name="type" />
        <input type="hidden" name="appareil" />
       
        {{mb_label object=$antecedent field=_search}}
        {{mb_field object=$antecedent field=_search size=25 class="autocomplete"}}
      </form>
    </td>  
  </tr>
  <tr>
    <td style="vertical-align: top;" class="narrow">
      <ul id="tab-antecedents" class="control_tabs_vertical">
      {{foreach from=$antecedent->_count_rques_aides item=count key=type}}
        {{if $count}}
          <li class="draggable droppable">
            <a href="#antecedents_{{$type}}" style="white-space: nowrap;" data-type="{{$type}}">
              {{tr}}CAntecedent.type.{{$type}}{{/tr}}
              <small>({{$count}})</small>
            </a>
          </li>
        {{/if}}
      {{/foreach}}
      </ul>
    </td>
    <td>
      {{foreach from=$antecedent->_count_rques_aides item=count key=type}}
        {{if $count}}
          <table id="antecedents_{{$type}}" class="main" style="border: none;">
            <tr>
              <td class="narrow text" style="background-color: transparent; border: none;">
                <script>
                  Main.add(function() {
                    Control.Tabs.create('tab-{{$type}}', false);
                  });
                </script>

                <ul id="tab-{{$type}}" class="control_tabs">
                  {{foreach from=$aides_antecedent.$type item=_aides key=appareil}}
                    <li class="draggable droppable">
                      <a href="#{{$type}}-{{$appareil}}"style="white-space: nowrap;" data-appareil="{{$appareil}}">
                        {{tr}}CAntecedent.appareil.{{$appareil}}{{/tr}} <small>({{$antecedent->_count_rques_aides_appareil.$type.$appareil}})</small>
                      </a>
                    </li>
                  {{/foreach}}
                </ul>
              </td>
            </tr>
            <tr>
              <td>
                {{if $count}}
                  {{mb_include module=cabinet template=inc_grid_list_antecedents}}
                {{/if}}
              </td>
            </tr>
          </table>
        {{/if}}
      {{/foreach}}
    </td>
  </tr>
</table>