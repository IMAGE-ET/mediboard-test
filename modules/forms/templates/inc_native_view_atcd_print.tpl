{{assign var=patient value=$object->loadRelPatient()}}
{{assign var=dossier_medical value=$patient->loadRefDossierMedical()}}
{{assign var=dossier_medical_complete value=$dossier_medical->loadComplete()}}

{{mb_include module=patients template=CDossierMedical_complete object=$dossier_medical hide_header=true}}