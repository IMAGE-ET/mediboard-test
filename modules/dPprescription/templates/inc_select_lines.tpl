<script type="text/javascript">
  Main.add(function() {
    Control.Tabs.create('tabs-protocole', true);
    
    // Si la sauvegarde des checkboxes est présente,
    // alors on restaure leur état
    if (window.save_checkboxes) {
     window.save_checkboxes.ids.each(function(elt_id, index) {
       if (elt_id) {
         var new_elt = $(elt_id);
         if (new_elt) {
           new_elt.checked = window.save_checkboxes.checked[index];
           $V(new_elt.next('input'), new_elt.checked ? 1 : 0);
         }
       }
     });
    }
  });

  cancelProtocole = function(elt) {
    $V(elt.up('div.modal').select('input[type="checkbox"]'), null)
    submitChangedLines();
  }
  
  submitChangedLines = function() {
    return onSubmitFormAjax(getForm('selLines'), {onComplete: function() {
      {{if $mode == "stopped_lines"}}
			modalWindowTools.close();
			{{/if}}
		  Control.Modal.close();
      Prescription.reloadPrescSejour('{{$prescription_id}}', null, null, null, null, null, null, null, '{{$pratSel_id}}', null, '{{$praticien_id}}');
    } });
  };
</script>

{{if $count_past_lines > 0}}
  <div class="small-warning">
    Il y a {{$count_past_lines}} ligne{{if $count_past_lines > 1}}s{{/if}} dont le début est antérieur à la date courante.
  </div>
{{/if}}

<ul id="tabs-protocole" class="control_tabs">
  {{if $prescription->_ref_lines_med_comments.med|@count || $prescription->_ref_prescription_line_mixes || $prescription->_ref_lines_med_comments.comment|@count}}
    <li><a href="#medicaments">{{tr}}CPrescription._chapitres.med{{/tr}}
      (<span id="nb_med">{{$prescription->_counts_by_chapitre.med}}</span>)</a></li>
  {{/if}}
  {{foreach from=$prescription->_ref_lines_elements_comments item=_lines_by_chap key=chap}}
	  {{if $prescription->_counts_by_chapitre.$chap}}
    <li><a href="#element_{{$chap}}">{{tr}}CPrescription._chapitres.{{$chap}}{{/tr}}
      (<span id="nb_{{$chap}}">{{$prescription->_counts_by_chapitre.$chap}}</span>)</a></li>
		{{/if}}
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
  <input type="hidden" name="m" value="dPprescription"/>
	
	{{if $mode == "validation" || $mode == "tp"}}
    <input type="hidden" name="dosql" value="do_select_lines_aed"/>
	{{else}}
	  <input type="hidden" name="dosql" value="do_duplicate_lines_aed"/>
  {{/if}}
  {{mb_key object=$prescription}}
  
  <input type="hidden" name="pratSel_id" value="{{$pratSel_id}}" />
  <input type="hidden" name="praticien_id" value="{{$praticien_id}}" />
  
  {{if $prescription->_ref_lines_med_comments.med|@count ||
    $prescription->_ref_lines_med_comments.comment|@count ||
    $prescription->_ref_prescription_line_mixes|@count}}
    <div id="medicaments">
      {{foreach from=$prescription->_ref_lines_med_comments.med item=_line}}
        {{assign var=protocole_id value=$_line->protocole_id}}
				
				{{if $mode == "tp"}}
				  {{assign var=checked_lines value=0}}
        {{elseif $mode == "validation"}}
					{{assign var=checked_lines value=$checked_lines_tab.$protocole_id}}
	      {{else}}
					{{assign var=checked_lines value=1}}
	      {{/if}}
				
				{{mb_include template=inc_vw_line_medicament_lite curr_line=$_line}}
      {{/foreach}}
      
      <table class="tbl">
      {{foreach from=$prescription->_ref_lines_med_comments.comment item=_line}}
        {{assign var=protocole_id value=$_line->protocole_id}}
        {{if $mode == "validation"}}
          {{assign var=checked_lines value=$checked_lines_tab.$protocole_id}}
        {{else}}
          {{assign var=checked_lines value=1}}
        {{/if}}
        {{mb_include template=inc_vw_line_comment_lite _line_comment=$_line}}
      {{/foreach}}
      </table>
      
      {{foreach from=$prescription->_ref_prescription_line_mixes item=_line}}
        {{assign var=protocole_id value=$_line->protocole_id}}
        {{if $mode == "validation"}}
          {{assign var=checked_lines value=$checked_lines_tab.$protocole_id}}
        {{else}}
          {{assign var=checked_lines value=1}}
        {{/if}}
        {{mb_include template=inc_vw_line_mix_lite _prescription_line_mix=$_line}}
      {{/foreach}}
    </div>
  {{/if}}
    
  {{foreach from=$prescription->_ref_lines_elements_comments item=_lines_by_chap key=chap}}
    <div id="element_{{$chap}}">
      {{assign var=has_element value=0}}
      {{foreach from=$_lines_by_chap item=_lines_by_cat}}
        {{foreach from=$_lines_by_cat.element item=_line name=foreach_elt}}
          {{assign var=protocole_id value=$_line->protocole_id}}
          {{if $mode == "validation"}}
	          {{assign var=checked_lines value=$checked_lines_tab.$protocole_id}}
	        {{else}}
	          {{assign var=checked_lines value=1}}
	        {{/if}}
          {{if $smarty.foreach.foreach_elt.first}}
            {{assign var=has_element value=1}}
            <table class="tbl">
              <tr>
                <th class="category">
                  {{$_line->_ref_element_prescription->_ref_category_prescription->_view}}
                </th>
              </tr>
            </table>
          {{/if}}
          {{mb_include template=inc_vw_line_element_lite _line_element=$_line}}
        {{/foreach}}
        
        {{foreach from=$_lines_by_cat.comment item=_line name=foreach_elt}}
          {{assign var=protocole_id value=$_line->protocole_id}}
          {{if $mode == "validation"}}
	          {{assign var=checked_lines value=$checked_lines_tab.$protocole_id}}
	        {{else}}
	          {{assign var=checked_lines value=1}}
	        {{/if}}
          {{if $smarty.foreach.foreach_elt.first && !$has_element}}
            <table class="tbl">
              <tr>
                <th class="category">
                  {{$_line->_ref_category_prescription->_view}}
                </th>
              </tr>
            </table>
          {{/if}}
          <table class="tbl">
            {{mb_include template=inc_vw_line_comment_lite _line_comment=$_line}}
          </table>
        {{/foreach}}
      {{/foreach}}
    </div>
  {{/foreach}}
  <div style="text-align: center; padding-top: 5px;">
	  {{if $mode == "tp"}}
      <button type="button" class="save oneclick" onclick="submitChangedLines()">{{tr}}CPrescription.apply_advanced_protocole{{/tr}} {{if $app->user_id == $praticien_id}}et signer{{/if}}</button>
      <button type="button" class="cancel oneclick" onclick="cancelProtocole(this)">Annuler l'ajout des traitements personnels</button>
    {{/if}}
  
	  {{if $mode == "validation"}}
      <button type="button" class="save oneclick" onclick="submitChangedLines()">{{tr}}CPrescription.apply_advanced_protocole{{/tr}} {{if $app->user_id == $praticien_id}}et signer{{/if}}</button>
      <button type="button" class="cancel oneclick" onclick="cancelProtocole(this)">{{tr}}CPrescription.cancel_advanced_protocole{{/tr}}</button>
    {{/if}}
		
		{{if $mode == "stopped_lines"}}
		  <button type="button" class="save oneclick" onclick="submitChangedLines()">Reprendre les lignes</button>
      <button type="button" class="cancel oneclick" onclick="Control.Modal.close();">{{tr}}Cancel{{/tr}}</button>
    {{/if}}
	</div>
</form>