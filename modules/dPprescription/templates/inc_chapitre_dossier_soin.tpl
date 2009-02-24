{{if $chapitre == "med"}}
	{{foreach from=$prescription->_ref_lines_med_for_plan item=_cat_ATC key=_key_cat_ATC name="foreach_cat"}}
	  {{foreach from=$_cat_ATC item=_line name="foreach_med"}}
	    {{foreach from=$_line key=unite_prise item=line_med name="foreach_line"}} 
	      {{if !$line_med->_is_injectable}}
	      {{include file="../../dPprescription/templates/inc_vw_line_dossier_soin.tpl" 
		            line=$line_med
		            nodebug=true
		            first_foreach=foreach_med
		            last_foreach=foreach_line
		            global_foreach=foreach_cat
		            nb_line=$_line|@count
		            type="med"
		            dosql=do_prescription_line_medicament_aed}} 
	      {{/if}}
	   {{/foreach}}
	 {{/foreach}} 		 
	{{/foreach}}
{{/if}}

{{if $chapitre == "inj"}}
  {{foreach from=$prescription->_ref_injections_for_plan item=inj_cat_ATC key=_key_cat_ATC name="_foreach_cat"}}
    {{foreach from=$inj_cat_ATC item=inj_line name="_foreach_med"}}
      {{foreach from=$inj_line key=unite_prise item=inj_line_med name="_foreach_line"}} 
        {{include file="../../dPprescription/templates/inc_vw_line_dossier_soin.tpl" 
            line=$inj_line_med
            nodebug=true
            first_foreach=_foreach_med
            last_foreach=_foreach_line
            global_foreach=_foreach_cat
            nb_line=$inj_line|@count
            type="inj"
            dosql=do_prescription_line_medicament_aed}}
     {{/foreach}}
   {{/foreach}} 		 
  {{/foreach}}
{{/if}}

{{if $chapitre == "perf"}}
	{{foreach from=$prescription->_ref_perfusions_for_plan item=_perfusion name=foreach_perfusion}}
	  <tr id="line_{{$_perfusion->_guid}}">
	  {{include file="../../dPprescription/templates/inc_vw_perf_dossier_soin.tpl" nodebug=true}}
	  </tr>
  {{/foreach}}
{{/if}}

{{if ($chapitre != "med") && ($chapitre != "inj") && ($chapitre != "perf")}}
{{assign var=elements value=$prescription->_ref_lines_elt_for_plan}}
{{assign var=elements_chap value=$elements.$chapitre}}
{{assign var=name_chap value=$chapitre}}
  {{foreach from=$elements_chap key=name_cat item=elements_cat name="foreach_chap"}}
    {{assign var=categorie value=$categories.$name_chap.$name_cat}}
    {{foreach from=$elements_cat item=_element name="foreach_cat"}}
      {{foreach from=$_element key=unite_prise item=element name="foreach_elt"}} 	          
        {{include file="../../dPprescription/templates/inc_vw_line_dossier_soin.tpl" 
                  line=$element
                  nodebug=true
                  first_foreach=foreach_cat
                  last_foreach=foreach_elt
                  global_foreach=foreach_chap
                  nb_line=$_element|@count
                  dosql=do_prescription_line_element_aed}}
      {{/foreach}}
    {{/foreach}}
  {{/foreach}}
{{/if}}