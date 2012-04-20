<script type="text/javascript">
  checkCut = function(elt) {
    if (elt.value == $V(elt.form.entree)) {
      $('cut_affectation').disabled='disabled'
    }
    else {
      $('cut_affectation').disabled = '';
    }
  }
  addUfs = function(){
    var form = getForm("affect_uf");
    var form2 = getForm("cutAffectation");
    form2.uf_hebergement_id.value = form.uf_hebergement_id.value;
    form2.uf_medicale_id.value    = form.uf_medicale_id.value;
    form2.uf_soins_id.value       = form.uf_soins_id.value;
  }
</script>

<table class="form">
  <tr>
    <th class="category" colspan="4">Autres actions</th>
  </tr>
  <tr>
    <td colspan="4">
      <form name="cutAffectation" method="post" action="?"
        onsubmit="addUfs();return onSubmitFormAjax(this, {onComplete: function() {
          refreshMouvements(null, '{{$affectation->lit_id}}');
          refreshMouvements(Control.Modal.close, '{{$lit_id}}');
           }})">
        <input type="hidden" name="m" value="dPhospi" />
        <input type="hidden" name="dosql" value="do_cut_affectation_aed" />
        <input type="hidden" name="lit_id" value="{{$lit_id}}" />
        <input type="hidden" name="entree" value="{{$affectation->entree}}" />
        <input type="hidden" name="uf_hebergement_id" value="" />
        <input type="hidden" name="uf_medicale_id" value="" />
        <input type="hidden" name="uf_soins_id" value="" />
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
          
          Affectation parente ({{$sejour_maman->_ref_patient}}) : <br />
          <select name="parent_affectation_id" onchange="this.form.onsubmit();">
            <option value="">Aucune affectation</option>
            {{foreach from=$affectations item=_affectation}}
              <option value="{{$_affectation->_id}}" {{if $affectation->parent_affectation_id == $_affectation->_id}}selected="selected"{{/if}}>
                Affectation {{mb_include module=system template=inc_interval_date_progressive object=$_affectation from_field=entree to_field=sortie nodebug=1}} 
                </option>
            {{/foreach}}
          </select>
        </form>
      {{/if}}
    </td>
  </tr>
  <tr>
    <td id="ufs_affectation">
      <script>
        Main.add( function(){
          var url = new Url("hospi", "ajax_vw_association_uf");
          url.addParam("curr_affectation_guid", '{{$affectation->_guid}}');
          url.addParam("lit_guid", "CLit-"+'{{$affectation->lit_id}}');
          url.addParam("see_validate", 0);
          url.requestUpdate('ufs_affectation');
        } );
      </script>
    </td>
  </tr>
</table>