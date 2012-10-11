{{assign var=patient value=$object}}
{{assign var=corresp value=$patient->loadRefsCorrespondantsPatient()}}

{{mb_include 
   module=patients 
   template=inc_list_correspondants 
   nb_correspondants=$patient->_ref_correspondants_patient|@count 
   correspondants_by_relation=$patient->_ref_cp_by_relation
   readonly=true}}