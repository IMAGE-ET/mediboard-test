{{assign var=patient value=$object->loadRelPatient()}}
{{assign var=dossier_medical value=$patient->loadRefDossierMedical()}}
{{mb_include module=patients template=inc_vw_antecedents show_all=true}}
