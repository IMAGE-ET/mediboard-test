<script type="text/javascript">
  Main.add(function() {
    Control.Tabs.create('tabs-protocole', true);
  });
  
  toggleTypePerfusion = function(oForm){
    if(!oForm.type){
      return;
    }
    if(oForm.prescription_line_mix_id.value == ""){
      oForm.type.show();
    } else {
      oForm.type.hide();
    }
  }

  var date = new Date().toDATE();
  var dDate = Date.fromDATE(date); 
  dDate.addDays(-1);
  date = dDate.toDATE();

  dates = {  
    limit: {
      start: date,
      stop: null
    }
  }

  modifFormDate = function(nb_prises, form_name, protocole,line_id){
    var oForm = document.forms[form_name];
   
    if(protocole == 0){
      oDiv = $('info_date_'+line_id);
      if(nb_prises > 0){
        oForm.hide();
        oDiv.show();
      } else {
        oForm.show();
        oDiv.hide();
      }
    }

    if(protocole == 1){
      if(nb_prises > 0){
        $V(oForm.duree,"1",false); 
        $V(oForm.jour_decalage,"I",false); 
        $V(oForm.decalage_line,"+0",false);
        $V(oForm.unite_decalage,"jour",false)
        $V(oForm.time_debut,"",false);
        $V(oForm.time_debut_da,"",false);
        $V(oForm.jour_decalage_fin,"",false);
        $V(oForm.decalage_line_fin,"",false);
        $V(oForm.unite_decalage_fin,"",false);
        $V(oForm.time_fin,"",false);
        $V(oForm.time_fin_da,"",false);

        // The time pickers
        $(oForm.time_debut).previousSiblings().first().select('img,div').invoke('hide');
        $(oForm.time_fin).previousSiblings().first().select('img,div').invoke('hide'); 
        
        onSubmitFormAjax(oForm);
        
        oForm.duree.disabled = "disabled";
        oForm.jour_decalage.disabled = "disabled";
        oForm.decalage_line.disabled = "disabled";
        oForm.unite_decalage.disabled = "disabled";
        oForm.time_debut.disabled = "disabled";
        oForm.jour_decalage_fin.disabled = "disabled";
        oForm.decalage_line_fin.disabled = "disabled";
        oForm.unite_decalage_fin.disabled = "disabled";
        oForm.time_fin.disabled = "disabled";
        
      } else {
        oForm.duree.disabled = false;
        if (oForm.jour_decalage) {
          oForm.jour_decalage.disabled = false;
        }
        oForm.decalage_line.disabled = false;
        if (oForm.unite_decalage) {
          oForm.unite_decalage.disabled = false;
        }
        oForm.time_debut.disabled = false;
        if (oForm.jour_decalage_fin) {
          oForm.jour_decalage_fin.disabled = false;
        }
        if (oForm.decalage_line_fin) {
          oForm.decalage_line_fin.disabled = false;
        }
        if (oForm.unite_decalage_fin) {
          oForm.decalage_line_fin.disabled = false;
        }
        if (oForm.time_fin) {
          oForm.time_fin.disabled = false; 
        }
        
        // The time pickers
        $(oForm.time_debut).previousSiblings().first().select('img,div').invoke('show');
        var time_fin = $(oForm.time_fin);
        if (time_fin) {
          time_fin.previous().select('img,div').invoke('show');
        }
      }
    }
  }
  
  submitMerge = function() {
    submitFormAjax(getForm('selLines'), 'systemMsg', {onComplete: function() {
      Control.Modal.close();
      Prescription.reloadPrescSejour('{{$prescription->_id}}');
    }})
  };
</script>

<form name="moment_unitaire" action="?" method="get">
  <select name="moment_unitaire_id" style="width: 150px; display: none;">  
    <option value="">&mdash; Moment</option>
   {{foreach from=$moments key=type_moment item=_moments}}
   <optgroup label="{{$type_moment}}">
   {{foreach from=$_moments item=moment}}
   {{if $type_moment == "Complexes"}}
     <option value="complexe-{{$moment->code_moment_id}}">{{$moment->_view}}</option>
   {{else}}
     <option value="unitaire-{{$moment->_id}}">{{$moment->_view}}</option>
   {{/if}}
   {{/foreach}}
   </optgroup>
   {{/foreach}}
  </select>
</form>

<ul id="tabs-protocole" class="control_tabs">
  {{foreach from=$chapitres item=_count key=chap}}
    <li>
      <a href="#element_{{$chap}}">{{tr}}CPrescription._chapitres.{{$chap}}{{/tr}}
      (<span id="nb_{{$chap}}">{{$_count}}</span>)
      </a>
    </li>
  {{/foreach}}
</ul>

<hr class="control_tabs" />

{{assign var=advanced_prot value=1}}
{{assign var=mode_pharma value=0}}
{{assign var=operation_id value=''}}
{{assign var=element value=''}}
{{assign var=mode_substitution value=''}}
{{assign var=mode_protocole value=''}}
{{assign var=mode_pharma value=''}}

{{mb_include template=js_functions}}

<form name="selLines" method="post" action="?">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_merge_prescriptions" />
  <input type="hidden" name="prescriptions_ids" value="{{$prescriptions_ids}}" />
  
  {{foreach from=$chapitres item=_count key=chap}}
    <div id="element_{{$chap}}">
      <table class="tbl">
        <tr>
          {{assign var=count_presc value=$prescriptions|@count}}
          {{math equation="100/$count_presc" assign=width format="%.1f"}}
          {{foreach from=1|range:$count_presc item=i}}
            <th style="width: {{$width}}%">Prescription {{$i}}</th>
          {{/foreach}}
        </tr>
        {{if $chap == "med" && $_count > 0}}
          <tr>
              <th class="category" colspan="{{$count_presc}}">{{tr}}CPrescriptionLineMedicament{{/tr}}</th>
          </tr>
          <tr>
            {{foreach from=$prescriptions item=_prescription}}
              <td>
                {{foreach from=$_prescription->_ref_lines_med_comments.med item=_line}}
                  {{mb_include template=inc_vw_line_medicament_lite curr_line=$_line}}
                {{foreachelse}}
                  {{tr}}CPrescriptionLineMedicament.none{{/tr}}
                {{/foreach}}
              </td>
            {{/foreach}}
          </tr>
          <tr>
            <th class="category" colspan="{{$count_presc}}">{{tr}}CPrescription._chapitres.perf{{/tr}}</th>
          </tr>
          <tr>
            {{foreach from=$prescriptions item=_prescription}}
              <td>
                {{foreach from=$_prescription->_ref_prescription_line_mixes item=_line}}
                  {{mb_include template=inc_vw_line_mix_lite _prescription_line_mix=$_line}}
                {{foreachelse}}
                  {{tr}}CPrescriptionLineMix.none{{/tr}}
                {{/foreach}}
              </td>
            {{/foreach}}
          </tr>
          
          <tr>
              <th class="category" colspan="{{$count_presc}}">{{tr}}CMediusers-back-prescription_comments{{/tr}}</th>
            </tr>
          <tr>
            {{foreach from=$prescriptions item=_prescription}}
              <td>
                {{foreach from=$_prescription->_ref_lines_med_comments.comment item=_line}}
                  <table class="tbl">
                    {{mb_include template=inc_vw_line_comment_lite _line_comment=$_line}}
                  </table>
                {{foreachelse}}
                  {{tr}}CPrescriptionLineComment.none{{/tr}}
                {{/foreach}}
              </td>
            {{/foreach}}
          </tr>
        {{elseif $_count > 0}}
          <tr>
            {{foreach from=$prescriptions item=_prescription}}
              <td>
                <table class="tbl">
                  {{if isset($_prescription->_ref_lines_elements_comments.$chap|smarty:nodefaults) && $_prescription->_ref_lines_elements_comments.$chap|@count}}
                    {{foreach from=$_prescription->_ref_lines_elements_comments.$chap item=_lines}}
                      {{foreach from=$_lines.element item=_line name=foreach_elt}}
                        {{if $smarty.foreach.foreach_elt.first}}
                          <tr>
                            <th class="category">
                              {{$_line->_ref_element_prescription->_ref_category_prescription->_view}}
                            </th>
                          </tr>
                        {{/if}}
                        <tr>
                          <td>
                            {{mb_include template=inc_vw_line_element_lite _line_element=$_line}}
                          </td>
                        </tr>
                      {{/foreach}}
                      {{if $_lines.comment|@count}}
                        {{foreach from=$_lines.comment item=_line name=foreach_elt}}
                          {{if $smarty.foreach.foreach_elt.first}}
                            <tr>
                              <th class="category">
                                {{$_line->_ref_category_prescription->_view}}
                              </th>
                            </tr>
                          {{/if}}
                          <tr>
                            <td>
                              <table class="tbl">
                                {{mb_include template=inc_vw_line_comment_lite _line_comment=$_line}}
                              </table>
                            </td>
                          </tr>
                        {{/foreach}}
                      {{/if}}
                    {{/foreach}}
                  {{else}}
                    <tr>
                      <td>
                      {{tr}}CPrescriptionLineElement.none{{/tr}}
                      </td>
                    </tr>
                  {{/if}}
                </table>
              </td>
            {{/foreach}}
          </tr>
        {{/if}}
      </table>
    </div>
  {{/foreach}}

  <button type="button" class="save onclick" onclick="submitMerge()">{{tr}}CPrescription.do_merge{{/tr}}</button>
</form>