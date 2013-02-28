<table class="main">
  <tr>
    <td>
      <table>
        <tr>
          <th>
            <a href="#" onclick="window.print()">
              Rapport 
              {{mb_include module=system template=inc_interval_date from=$filter->_date_min to=$filter->_date_max}}
            </a>
          </th>
        </tr>
        {{foreach from=$listPrat item=_prat}}
        <tr>
          <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_prat}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
    {{if $filter->_type_affichage}}
      <td colspan="2">
        <table>
          {{foreach from=$listConsults_date key=key_date item=consultations}}
            <tr>
              <td colspan="2"><strong>Dossiers non cotés du {{$key_date|date_format:$conf.longdate}}</strong></td>
            </tr>
            <tr>
              <td colspan="2">
                <table class="tbl">
                  <tr>
                    <th>{{mb_label class=CConsultation field=_prat_id}}</th>
                    <th>{{mb_label class=CConsultation field=patient_id}}</th>
                    <th>{{mb_label class=CPlageconsult field=date}}</th>
                    <th>{{mb_label class=CConsultation field=heure}}</th>
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
          {{foreachelse}}
            <tr>
              <th>Pas de dossier non coté pour cette période</th>
            </tr>
          {{/foreach}}
       </table>
      </td>
    {{/if}}
  </tr>
</table>