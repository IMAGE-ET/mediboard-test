<script type="text/javascript">
  Main.add(function() {
    Control.Tabs.create('tabs-protocole');
  });

  submitMerge = function() {
    submitFormAjax(getForm('selLines'), 'systemMsg', {onComplete: function() {
      Control.Modal.close();
      Prescription.reloadPrescSejour('{{$prescription_id}}');
    }})
  };
</script>

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
            {{foreach from=$prescriptions item=prescription}}
              <td>
                {{foreach from=$prescription->_ref_lines_med_comments.med item=_line}}
                  {{mb_include template=inc_vw_line_medicament_lite curr_line=$_line}}
                {{foreachelse}}
                  {{tr}}CPrescriptionLine.none{{/tr}}
                {{/foreach}}
              </td>
            {{/foreach}}
          </tr>
          
          {{assign var=count_line_mixes value=0}}
          {{foreach from=$prescriptions item=prescription}}
            {{assign var=_temp_count value=$prescription->_ref_prescription_line_mixes|@count}} 
            {{assign var=count_line_mixes value=$count_line_mixes+$_temp_count}}
          {{/foreach}}
          {{if $count_line_mixes}}
            <tr>
              {{foreach from=$prescriptions item=prescription}}
                <td>
                  {{foreach from=$prescription->_ref_prescription_line_mixes item=_line}}
                    {{mb_include template=inc_vw_line_mix_lite _prescription_line_mix=$_line}}
                  {{/foreach}}
                </td>
              {{/foreach}}
            </tr>
          {{/if}}
          <tr>
            {{foreach from=$prescriptions item=prescription}}
              <td>
                {{foreach from=$prescription->_ref_lines_med_comments.comment item=_line}}
                  <table class="tbl">
                    {{mb_include template=inc_vw_line_comment_lite _line_comment=$_line}}
                  </table>
                {{/foreach}}
              </td>
            {{/foreach}}
          </tr>
        {{elseif $_count > 0}}
          <tr>
            {{foreach from=$prescriptions item=prescription}}
              <td>
                <table class="tbl">
                  {{if isset($prescription->_ref_lines_elements_comments.$chap|smarty:nodefaults)}}
                    {{foreach from=$prescription->_ref_lines_elements_comments.$chap item=_lines}}
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
                    {{/foreach}}
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