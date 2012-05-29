<!-- $Id: print_noncote.tpl 15160 2012-04-10 09:35:58Z aurelie17 $ -->

<table class="main">
  <tr>
    <td class="halfPane">
      <table>
        <tr>
          <th>
            <a href="#" onclick="window.print()">
              Rapport 
              {{mb_include module=system template=inc_interval_date from=$filter->_date_min to=$filter->_date_max}}
            </a>
          </th>
        </tr>
        <!-- Praticiens concernés -->
        {{foreach from=$listPrat item=_prat}}
        <tr>
          <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_prat}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  {{if $filter->_type_affichage}}
    <td colspan="2" >
      <table class="">
        {{foreach from=$listConsults_date key=key_date item=consultations}}
          <tr>
            <td colspan="2"><strong>Dossiers non cotés du {{$key_date|date_format:$conf.longdate}}</strong></td>
          </tr>
          <tr>
            <td colspan="2">
              <table class="tbl">
                <tr>
                  <th style="width: 30%;">{{mb_label class=CConsultation field=_prat_id}}</th>
                  <th style="width: 30%;">{{mb_label class=CConsultation field=patient_id}}</th>
                  <th style="width: 30%;">{{mb_label class=CPlageConsult field=date}}</th>
                  <th style="width: 30%;">{{mb_label class=CConsultation field=heure}}</th>
                </tr>
                {{foreach from=$consultations.consult item=consultation}}
                
                  <tr>
                    <td class="text">
                      {{assign var=prat_id value=$consultation->_ref_chir->_id}}
                      {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$listPrat.$prat_id}}
                    </td>
          
                    <td class="text">
                      {{assign var=patient value=$consultation->_ref_patient}}
                      <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">{{$patient}}</span>
                    </td>
          
                    <td class="text">
                      {{mb_value object=$consultation->_ref_plageconsult field=date}}
                    </td>
                    
                    <td>{{mb_value object=$consultation field=heure}}</td>
                  </tr>
                {{/foreach}} 
             </table>
           </td>
         </tr> 
       {{/foreach}}  
     </table>
    </td>
   {{/if}}
  </tr>
</table>      