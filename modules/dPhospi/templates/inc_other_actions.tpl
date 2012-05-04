{{mb_default var=sejour_maman value=""}}

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
  
  Main.add(function() {
    var dates = {
      limit: {
        start: "{{$affectation->entree}}",
        stop: "{{$affectation->sortie}}"
      }
    }
    Calendar.regField(getForm('cutAffectation')._date_cut, dates, {timePicker: true});
    {{if "maternite"|module_active && $sejour_maman}}
      Calendar.regField(getForm('liaisonAffectation')._date_cut, null, {timePicker: true});
    {{/if}}
  } );
  
  liaisonMaman = function(status) {
    var oForm = getForm('liaisonAffectation');
    if (!status) {
      $V(oForm.parent_affectation_id, '');
      changeLit('{{$affectation->_id}}', null, 1);
      return;
    }
    $V(oForm._link_affectation, 1);
    oForm.onsubmit();
    Control.Modal.close();
  }
  
  submitLiaison = function(lit_id) {
    var oForm = getForm('liaisonAffectation');
    $V(oForm.lit_id, lit_id);
    oForm.onsubmit();
  }
  
  afterEditLiaison = function() {
    refreshMouvements(null, '{{$lit_id}}');
    refreshMouvements(null, $V(getForm('liaisonAffectation').lit_id));
    Control.Modal.close();
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
        <input type="text" name="_date_cut_da" value="{{$smarty.now|date_format:$conf.datetime}}" readonly="readonly"/>
        <input type="hidden" name="_date_cut" class="dateTime" value="{{$smarty.now|@date_format:"%Y-%m-%d %H:%M:%S"}}"
          onchange="checkCut(this)"/>
        <button type="button" class="cut" onclick="this.form.onsubmit();" id="cut_affectation" disabled="disabled">Scinder</button>
      </form>
      <br />
      {{if "maternite"|module_active && $sejour_maman}}
        <form name="liaisonAffectation" method="post" action="?" onsubmit="return onSubmitFormAjax(this, {onComplete: afterEditLiaison})">
          <input type="hidden" name="m" value="dPhospi" />
          <input type="hidden" name="dosql" value="do_cut_affectation_aed" />
          <input type="hidden" name="affectation_id" value="{{$affectation->_id}}" />
          <input type="hidden" name="parent_affectation_id" value="{{$affectation->parent_affectation_id}}"/>
          <input type="hidden" name="lit_id" value="{{$affectation->lit_id}}" />
          <input type="hidden" name="_link_affectation" value="0"/>
          <label>
            <input type="checkbox" name="_action_maman" {{if $affectation->parent_affectation_id}}checked="checked"{{/if}}
              onclick="liaisonMaman(this.checked)"/>
            Attacher à la maman ({{$sejour_maman->_ref_patient}})
          </label>
          <input type="text" name="_date_cut_da" value="{{$smarty.now|date_format:$conf.datetime}}" readonly="readonly"/>
          <input type="hidden" name="_date_cut" class="dateTime" value="{{$smarty.now|@date_format:"%Y-%m-%d %H:%M:%S"}}" />
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