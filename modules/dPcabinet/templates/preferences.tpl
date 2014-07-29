{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage 
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

  
{{mb_include template=inc_pref spec=bool var=AUTOADDSIGN}}
{{mb_include template=inc_pref spec=enum var=MODCONSULT values="0|1"}}
{{mb_include template=inc_pref spec=bool var=dPcabinet_show_program}}
{{mb_include template=inc_pref spec=enum var=DossierCabinet values="dPcabinet|dPpatients"}}
{{mb_include template=inc_pref spec=bool var=viewWeeklyConsultCalendar}}
{{mb_include template=inc_pref spec=enum var=simpleCabinet values="0|1"}}
{{mb_include template=inc_pref spec=enum var=ccam_consultation values="0|1"}}
{{mb_include template=inc_pref spec=enum var=view_traitement values="0|1"}}
{{mb_include template=inc_pref spec=bool var=autoCloseConsult}}
{{mb_include template=inc_pref spec=bool var=resumeCompta}}
{{mb_include template=inc_pref spec=bool var=showDatesAntecedents}}
{{mb_include template=inc_pref spec=bool var=displayDocsConsult}}
{{mb_include template=inc_pref spec=bool var=choosePatientAfterDate}}
{{mb_include template=inc_pref spec=bool var=empty_form_atcd}}
{{mb_include template=inc_pref spec=str var=order_mode_grille readonly=true}}
{{mb_include template=inc_pref spec=bool var=create_dossier_anesth}}
{{mb_include template=inc_pref spec=bool var=displayPremedConsult}}
{{mb_include template=inc_pref spec=bool var=displayResultsConsult}}
{{mb_include template=inc_pref spec=bool var=viewFunctionPrats}}
{{mb_include template=inc_pref spec=bool var=viewAutreResult}}
{{mb_include template=inc_pref spec=bool var=use_acte_date_now}}
{{mb_include template=inc_pref spec=bool var=multi_popups_resume}}
<tr><th class="category" colspan="6">Planning</th></tr>
{{mb_include template=inc_pref spec=bool var=allow_plage_holiday}}
{{mb_include template=inc_pref spec=bool var=new_semainier}}
{{mb_include template=inc_pref spec=bool var=showIntervPlanning}}
{{mb_include template=inc_pref spec=enum var=AFFCONSULT values="0|1"}}
{{mb_include template=inc_pref spec=enum var=DefaultPeriod values="day|week|month|weekly" value_locale_prefix="Period."}}
<tr><th class="category" colspan="6">Consultations multiples</th></tr>
{{mb_include template=inc_pref spec=enum var=NbConsultMultiple values="2|3|4|5|6"}}
{{mb_include template=inc_pref spec=enum var=today_ref_consult_multiple values="0|1"}}
<tr><th class="category" colspan="6">Prise de RDV personnalisée, prendre RDV pour :</th></tr>
{{mb_include template=inc_pref spec=bool var=take_consult_for_chirurgien}}
{{mb_include template=inc_pref spec=bool var=take_consult_for_anesthesiste}}
{{mb_include template=inc_pref spec=bool var=take_consult_for_medecin}}
{{mb_include template=inc_pref spec=bool var=take_consult_for_infirmiere}}
{{mb_include template=inc_pref spec=bool var=take_consult_for_reeducateur}}
{{mb_include template=inc_pref spec=bool var=take_consult_for_sage_femme}}
{{mb_include template=inc_pref spec=bool var=take_consult_for_dentiste}}