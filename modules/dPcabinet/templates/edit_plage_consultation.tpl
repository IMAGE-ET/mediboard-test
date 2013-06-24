{{* $Id: edit_plage_consultation.tpl$  *}}

{{*
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision: 11962 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  modifEtatDesistement = function(valeur){
    if(valeur != 0){
      $('remplacant_plage').setVisible(valeur);
      $$('.remplacement_plage').invoke('setVisible', valeur);
      $$('.retrocession').invoke('setVisible', valeur);
    }
    else{
      var form = getForm('editFrm');
      form.remplacant_id.value = '';
      if(form.pour_compte_id.value == ""){
        $('remplacant_plage').hide();
        $$('.remplacement_plage').invoke('hide');
        $$('.retrocession').invoke('hide');
      }
      else{
        $$('.remplacement_plage').invoke('hide');
      }
    }
  };
  
  modifPourCompte = function(valeur){
    if(valeur != 0){
      $('remplacant_plage').setVisible(valeur);
      $$('.retrocession').invoke('setVisible', valeur);
    }
    else{
      var form = getForm('editFrm');
      if(form.desistee.value == 0){
        $('remplacant_plage').hide();
      }
    }
  };
  
  Main.add(function(){
    var form = getForm('editFrm');
    
    {{if !$can->admin && $plageSel->_id && !$plageSel->_canEdit}}
      makeReadOnly(form);
    {{/if}}
    
    Calendar.regField(form.debut);
    Calendar.regField(form.fin  );
    
    form._repeat.addSpinner({min: 0});
  });
</script>

{{mb_script module="mediusers" script="color_selector" ajax=true}}

<form name='editFrm' action='?m=dPcabinet' method='post' onsubmit="this._type_repeat.disabled = ''; return PlageConsultation.checkForm(this);">

<input type='hidden' name='dosql' value='do_plageconsult_multi_aed' />
<input type='hidden' name='del' value='0' />
{{mb_key object=$plageSel}}

<table class="form">
  {{mb_include module=system template=inc_form_table_header object=$plageSel colspan=4}}
  <tr>
    <td>
      <fieldset>
        <legend>Informations sur la plage</legend>
        <table class="form">
          <tr>
            <th>{{mb_label object=$plageSel field="chir_id"}}</th>
            <td>
              <select name="chir_id" class="{{$plageSel->_props.chir_id}}" style="width: 15em;">
                <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                {{mb_include module=mediusers template=inc_options_mediuser list=$listChirs selected=$chirSel}}
              </select>
            </td>
            <th>{{mb_label object=$plageSel field="libelle"}}</th>
            <td>{{mb_field object=$plageSel field="libelle" style="width: 15em;"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$plageSel field="date"}}</th>
            <td>
              <select name="date" class="{{$plageSel->_props.date}}" style="width: 15em;">
                <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                {{foreach from=$listDaysSelect item=curr_day}}
                  <option value="{{$curr_day}}" {{if $curr_day == $plageSel->date}} selected="selected" {{/if}}>
                    {{$curr_day|date_format:"%A"}}
                  </option>
                {{/foreach}}
              </select>
            </td>
            <th>{{mb_label object=$plageSel field="color"}}</th>
            <td>
              <script>
                ColorSelector.init = function(){
                  this.sForm  = "editFrm";
                  this.sColor = "color";
                  this.sColorView = "color-view";
                  this.pop();
                };
              </script>
              <span class="color-view" id="color-view" style="background: #{{if $plageSel->color}}{{$plageSel->color}}{{else}}DDDDDD{{/if}};">
                {{tr}}Choose{{/tr}}
              </span>
              <button type="button" class="search notext" onclick="ColorSelector.init()">
                {{tr}}Choose{{/tr}}
              </button>
              {{mb_field object=$plageSel field="color" hidden=1}}
            </td>
          </tr>
          <tr>
            <th>{{mb_label object=$plageSel field="debut"}}</th>
            <td>{{mb_field object=$plageSel field="debut"}}</td>
            <th>{{mb_label object=$plageSel field="locked"}}</th>
            <td>{{mb_field object=$plageSel field="locked" typeEnum="checkbox"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$plageSel field="fin"}}</th>
            <td>{{mb_field object=$plageSel field="fin"}}</td>
            <th></th>
            <td>
              {{if $plageSel->_affected}}
                Déjà <strong>{{$plageSel->_affected}} consultations</strong> planifiées
                de <strong>{{$_firstconsult_time}}</strong> à <strong>{{$_lastconsult_time}}</strong>
              {{/if}}
              <input type='hidden' name='nbaffected' value='{{$plageSel->_affected}}' />
              <input type='hidden' name='_firstconsult_time' value='{{$_firstconsult_time}}' />
              <input type='hidden' name='_lastconsult_time' value='{{$_lastconsult_time}}' />
            </td>
          </tr>
          <tr>
            <th>{{mb_label object=$plageSel field="_freq"}}</th>
            <td>
              <select name="_freq">
                <option value="05" {{if ($plageSel->_freq == "05")}} selected="selected" {{/if}}>05</option>
                <option value="10" {{if ($plageSel->_freq == "10")}} selected="selected" {{/if}}>10</option>
                <option value="15" {{if ($plageSel->_freq == "15") || (!$plageSel->_id)}} selected="selected" {{/if}}>15</option>
                <option value="20" {{if ($plageSel->_freq == "20")}} selected="selected" {{/if}}>20</option>
                <option value="30" {{if ($plageSel->_freq == "30")}} selected="selected" {{/if}}>30</option>
                <option value="45" {{if ($plageSel->_freq == "45")}} selected="selected" {{/if}}>45</option>
                <option value="60" {{if ($plageSel->_freq == "60")}} selected="selected" {{/if}}>60</option>
              </select> min
            </td>
            <th>{{mb_label object=$plageSel field="_skip_collisions"}}</th>
            <td>{{mb_field object=$plageSel field="_skip_collisions" typeEnum=checkbox}}</td>
          </tr>
          <tr>
            <th></th>
            <td></td>
            <th>{{mb_label object=$plageSel field="pour_tiers"}}</th>
            <td>{{mb_field object=$plageSel field="pour_tiers" typeEnum=checkbox}}</td>
          </tr>
        </table>
      </fieldset>
    </td>
  </tr>
  <tr>
    <td>
      <fieldset>
        <legend>Répétition</legend>
        <table class="form">
          <tr>
            <th><label for="_repeat" title="Nombre de semaines de répétition">Nombre de semaines</label></th>
            <td>
              <input type="text" size="2" name="_repeat" value="1"
                     onchange="this.form._type_repeat.disabled = this.value <= 1 ? 'disabled' : '';"
                     onKeyUp="this.form._type_repeat.disabled = this.value <= 1 ? 'disabled' : '';" />
              (max. 100)
            </td>
            <td rowspan="3" class="text">
              <div class="small-info">
                Pour modifier plusieurs plages (nombre de plages > 1),
                veuillez <strong>ne pas changer les champs début et fin en même temps</strong>.
                <br />
                L'état de verrouillage de la plage ne sera pas propagé sur les plages suivantes.
              </div>
            </td>
          </tr>
          <tr>
            <th>{{mb_label object=$plageSel field="_type_repeat"}}</th>
            <td>{{mb_field object=$plageSel field="_type_repeat" style="width: 15em;" typeEnum="select" disabled="disabled"}}</td>
          </tr>
        </table>
      </fieldset>
    </td>
  </tr>
  <tr>
    <td>
      <fieldset>
        <legend>Remplacements</legend>
        <table class="form">
          <tr>
            <th>{{mb_label object=$plageSel field="desistee"}}</th>
            <td>{{mb_field object=$plageSel field="desistee"  typeEnum="checkbox" onchange="modifEtatDesistement(this.value);" }}</td>
            <th>{{mb_label object=$plageSel field="pour_compte_id"}}</th>
            <td>
              <select name="pour_compte_id" style="width: 15em;"  onchange="modifPourCompte(this.value);">
                <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                {{mb_include module=mediusers template=inc_options_mediuser list=$listChirs selected=$plageSel->pour_compte_id disabled=$chirSel}}
              </select>
            </td>
          </tr>
          <tr id="remplacant_plage" {{if !$plageSel->desistee && !$plageSel->pour_compte_id}} style="display:none"{{/if}}>
            <th>
              <span class="remplacement_plage" {{if !$plageSel->desistee}}style="display:none;"{{/if}}>
              {{mb_label object=$plageSel field="remplacant_id"}}
              </span>
            </th>
            <td>
              <select name="remplacant_id" style="width: 15em;{{if !$plageSel->desistee}}display:none;{{/if}}" class="remplacement_plage">
                <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                {{mb_include module=mediusers template=inc_options_mediuser list=$listChirs selected=$plageSel->remplacant_id }}
              </select>
            </td>
            <th>
              <span class="retrocession">
                {{mb_label object=$plageSel field="pct_retrocession"}}
              </span>
            </th>
            <td>
              <span class="retrocession">
                {{mb_field object=$plageSel field="pct_retrocession" size="2" increment=true form=editFrm  class="retrocession"}}
              </span>
            </td>
          </tr>
        </table>
      </fieldset>
    </td>
  </tr>
</table>
          
<table class="form">

  <tr>
    {{if !$plageSel->_id}}
    <td class="button" colspan="4"><button type="submit" class="submit">{{tr}}Create{{/tr}}</button></td>
    {{else}}
    <td class="button" colspan="4">
      <button type="submit" class="modify">{{tr}}Modify{{/tr}}</button>
      <button class="trash" type='button'
        onclick="confirmDeletion(this.form, {
          typeName:'la plage de consultations du',objName:'{{$plageSel->date|date_format:$conf.longdate}}',
          callback: function() {
            var form = getForm('editFrm');
            form._type_repeat.disabled = '';
            form.submit(); 
          }})">
        {{tr}}Delete{{/tr}}
      </button>
    </td>
    {{/if}}
  </tr>

</table>

</form>
    