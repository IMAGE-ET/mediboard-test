<script type="text/javascript">
function addAntecedent(event, rques, type, appareil, input) {
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
      oForm.rques.value = rques;
      oForm.type.value = type;
      oForm.appareil.value = appareil;
      window.opener.onSubmitAnt(oForm);
      
      //input.checked = 'checked';
      input.disabled = 'disabled';
      
      $(input).up('td').setStyle({cursor: 'default', opacity: 0.3});
    }
  }
  window.focus();
}

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
        // S'ils sont c�te � c�te, juste ins�rer le premier apr�s le deuxi�me
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
        
        // Sinon on sauvegarde la position et on ins�re
        // Cas du dernier �l�ment
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
        var pref_tabs ={};
        
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
  });

</script>

<form name="editPref" method="post">
  <input type="hidden" name="m" value="admin" />
  <input type="hidden" name="dosql" value="do_preference_aed" />
  <input type="hidden" name="user_id" value="{{$user_id}}" />
  <input type="hidden" name="pref[order_mode_grille]" value="{{$order_mode_grille|@json}}" />
</form>

<div class="modal" id="complete_atcd" style="display: none; width: 400px; height: 180px;">
 <table class="form">
   <tr>
     <th class="title">
       Compl�ter l'ant�c�dent
     </th>
   </tr>
   <tr>
     <td>
       <textarea></textarea>
     </td>
   </tr>
   <tr>
     <td class="button">
       <button type="button" class="tick"
         onclick="Control.Modal.close(); addAntecedent(null, $V($('complete_atcd').down('textarea')), window.save_params.type, window.save_params.appareil, window.save_params.input)">
           {{tr}}Validate{{/tr}}
        </button>
       <button type="button" class="close" onclick="Control.Modal.close(); window.save_params.input.checked = ''">{{tr}}Close{{/tr}}</button>
     </td>
   </tr>
 </table> 
</div>

<!-- Ant�c�dents -->
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
        <li class="draggable droppable">
          <a href="#antecedents_{{$type}}" style="white-space: nowrap;" class="{{if !$count}}empty{{/if}}" data-type="{{$type}}">
            {{tr}}CAntecedent.type.{{$type}}{{/tr}}
            {{if $count}}
            <small>({{$count}})</small>
            {{/if}}
          </a>
        </li>
      {{/foreach}}
      </ul>
    </td>
    
    <td>
      {{foreach from=$antecedent->_count_rques_aides item=count key=type}}
      {{if $count}}
        
        <table class="main tbl" id="antecedents_{{$type}}" style="display: none; border: none;">
          <tr>
            <td class="text" colspan="1000" style="background-color:transparent; border: none;">     
              <script type="text/javascript">
                Main.add(function(){
                    Control.Tabs.create('tab-{{$type}}', false);
                });
              </script>  
              
              <ul id="tab-{{$type}}" class="control_tabs">
                {{foreach from=$aides_antecedent.$type item=_aides key=appareil}}
                  <li class="draggable droppable">
                    <a href="#{{$type}}-{{$appareil}}" data-appareil="{{$appareil}}">
                      {{tr}}CAntecedent.appareil.{{$appareil}}{{/tr}} <small>({{$_aides|@count}})</small>
                    </a>
                  </li>
                {{/foreach}}
              </ul>
            </td>  
          </tr>   

          
          {{foreach from=$aides_antecedent.$type item=_aides key=appareil}}
            <tbody id="{{$type}}-{{$appareil}}">
              <tr>
                <th colspan="1000" class="title">
                   <button style="float: right" class="add notext" onclick="$('textarea-ant-{{$type}}-{{$appareil}}').toggle(); this.toggleClassName('remove').toggleClassName('add')">Ajouter</button>
                  {{tr}}CAntecedent.appareil.{{$appareil}}{{/tr}}
                </th>
              </tr>
              <tr id="textarea-ant-{{$type}}-{{$appareil}}" style="display: none;">
                <td colspan="1000">
                  <form name="addAnt-{{$type}}-{{$appareil}}" method="post">
                    <input name="antecedent" size="60"/>
                    <button class="submit" type="button" onclick="$V(oFormAntFrmGrid.type, '{{$type}}'); $V(oFormAntFrmGrid.appareil, '{{$appareil}}'); $V(oFormAntFrmGrid.rques, this.form.antecedent.value); $V(this.form.antecedent, '');">Ajouter l'ant�c�dent</button>
                  </form>
                </td>  
              </tr>
              <tr>
              {{foreach from=$_aides item=curr_aide name=aides}}
                {{if $curr_aide instanceof CAideSaisie}}
                  {{if $curr_aide->_owner == "user"}}
                    {{assign var=owner_icon value="user"}}
                  {{elseif $curr_aide->_owner == "func"}}
                    {{assign var=owner_icon value="function"}}
                  {{else}}
                    {{assign var=owner_icon value="group"}}
                  {{/if}}
                  {{assign var=i value=$smarty.foreach.aides.index}}
                  {{assign var=text value=$curr_aide->text}}
                  {{assign var=checked value=$curr_aide->_applied}}
                  <td class="text {{if $checked}}opacity-30{{/if}} {{$owner_icon}}"
                      style="cursor: pointer; width: {{$width}}%; {{if $checked}}cursor: default;{{/if}}" >
                    <label onmouseover="ObjectTooltip.createDOM(this, 'tooltip_{{$curr_aide->_guid}}')">
                      <input type="checkbox" {{if $checked}}checked="checked" disabled="disabled"{{/if}} id="aide_{{$curr_aide->_guid}}"
                        onclick="addAntecedent(arguments[0] || window.event, '{{$curr_aide->text|smarty:nodefaults|JSAttribute}}', '{{$type}}', '{{$appareil}}', this)"/>
                      {{$curr_aide->name}}
                    </label>
                    <div style="display: none" id="tooltip_{{$curr_aide->_guid}}">
                      <table class="tbl">
                        <tr>
                          <th>
                            {{$curr_aide->text}}
                          </th>
                        </tr>
                        <tr>
                          <td class="button">
                            <button type="button" class="edit"
                              onclick="var event = {ctrlKey: true}; addAntecedent(event, '{{$curr_aide->text|smarty:nodefaults|JSAttribute}}', '{{$type}}', '{{$appareil}}', $('aide_{{$curr_aide->_guid}}'))">Compl�ter</button>
                          </td>
                        </tr>
                      </table>
                    </div>
                  </td>
                  {{if ($i % $numCols) == ($numCols-1) && !$smarty.foreach.aides.last}}</tr><tr>{{/if}}
                {{/if}}
              {{/foreach}}
              </tr>
            </tbody>
          {{/foreach}}
          
        </table>
      {{else}}
        <div class="small-info" id="antecedents_{{$type}}" style="display: none;">
          {{tr}}CAideSaisie.none{{/tr}}
          pour le type
          '{{tr}}CAntecedent.type.{{$type}}{{/tr}}'
        </div>
      {{/if}}
      {{/foreach}}
    </td>
  </tr>
</table>