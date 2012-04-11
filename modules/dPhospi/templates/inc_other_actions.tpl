<script type="text/javascript">
  checkCut = function(elt) {
    if (elt.value == $V(elt.form.entree)) {
      $('cut_affectation').disabled='disabled'
    }
    else {
      $('cut_affectation').disabled = '';
    }
  }
</script>

<table class="form">
  <tr>
    <th class="category" colspan="4">Autres actions</th>
  </tr>
  <tr>
    <td colspan="4">
      <form name="cutAffectation" method="post" action="?"
        onsubmit="return onSubmitFormAjax(this, {onComplete: function() {
          refreshMouvements(null, '{{$affectation->lit_id}}');
          refreshMouvements(Control.Modal.close, '{{$lit_id}}');
           }})">
        <input type="hidden" name="m" value="dPhospi" />
        <input type="hidden" name="dosql" value="do_cut_affectation_aed" />
        <input type="hidden" name="lit_id" value="{{$lit_id}}" />
        <input type="hidden" name="entree" value="{{$affectation->entree}}" />
        {{mb_key object=$affectation}}
        <input type="text" name="_date_cut_da" value="{{$affectation->entree|date_format:$conf.datetime}}" readonly="readonly"/>
        <input type="hidden" name="_date_cut" class="dateTime" value="{{$affectation->entree}}"
          onchange="checkCut(this)"/>
        
        <script type="text/javascript">
          var dates = {
            limit: {
              start: "{{$affectation->entree}}",
              stop: "{{$affectation->sortie}}"
            }
          }
          
          Main.add( function(){
            Calendar.regField(getForm("cutAffectation")._date_cut, dates, {timePicker: true});
          } );
        </script>
        <button type="button" class="cut" onclick="this.form.onsubmit();" id="cut_affectation" disabled="disabled">Scinder</button>
      </form>
      <br />
      {{if "maternite"|module_active && $affectations|@count}}
        <form name="dissociateAffectation" method="post" action="?" onsubmit="return onSubmitFormAjax(this)">
          <input type="hidden" name="m" value="dPhospi" />
          <input type="hidden" name="dosql" value="do_affectation_aed" />
          <input type="hidden" name="affectation_id" value="{{$affectation->_id}}" />
          
          {{if $lit_id}}
            <input type="hidden" name="lit_id" value="{{$lit_id}}" />
          {{/if}}
          
          Affectation parente ({{$sejour_parturiente->_ref_patient}}) : <br />
          <select name="parent_affectation_id" onchange="this.form.onsubmit();">
            <option value="">Aucune affectation</option>
            {{foreach from=$affectations item=_affectation}}
              <option value="{{$_affectation->_id}}" {{if $affectation->parent_affectation_id == $_affectation->_id}}selected="selected"{{/if}}>
                Affectation {{mb_include module=system template=inc_interval_date_progressive object=$_affectation from_field=entree to_field=sortie}} 
                </option>
            {{/foreach}}
          </select>
        </form>
      {{/if}}
    </td>
  </tr>
</table>