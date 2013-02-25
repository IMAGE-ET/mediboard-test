{{mb_default var=display value=false}}

<table class="{{$tbl_class}}">
  <tr>
    <td>
      <table width="100%">
        <tr>
          <th class="title" colspan="4">
            {{if $offline && !$display}}
              <button type="button" style="float: right;" class="cancel not-printable" onclick="Control.Modal.close();">{{tr}}Close{{/tr}}</button>
            {{/if}}
            {{if $display}}
              Consultation pré-anesthésique
            {{else}}
              <a href="#" onclick="window.print()">
                Consultation pré-anesthésique
              </a>
            {{/if}}
          </th>
        </tr>
        <tr>
          <th>Date </th>
          <td style="font-size: 1.3em;">{{$consult->_ref_plageconsult->date|date_format:$conf.longdate}}</td>
          <th>Anesthésiste </th>
          <td style="font-size: 1.3em;">Dr {{$consult->_ref_chir->_view}}</td>
        </tr>
        <tr>
          <th>Patient </th>
          <td style="font-size: 1.3em;">{{$patient->_view}}</td>
          {{if $operation->_id}}
            <th>{{mb_label object=$dossier_anesth->_ref_operation field=chir_id}}</th>
            <td style="font-size: 1.3em;">{{mb_value object=$operation field=chir_id}}</td>
          {{else}}
            <th>{{mb_label object=$dossier_anesth field=chir_id}}</th>
            <td style="font-size: 1.3em;">{{mb_value object=$dossier_anesth field=chir_id}}</td>
          {{/if}}
        </tr>
        <tr>
          <th>
            Intervention
          </th>
          <td colspan="3" style="font-size: 1.3em;">
            {{if $operation->_id}}
              le {{$operation->_ref_plageop->date|date_format:"%A %d/%m/%Y"}}
              {{if $operation->libelle}}
                - {{$operation->libelle}}
              {{/if}}
            {{else}}
              le {{$dossier_anesth->date_interv|date_format:"%A %d/%m/%Y"}}
              {{if $dossier_anesth->libelle_interv}}
                - {{$dossier_anesth->libelle_interv}}
              {{/if}}
            {{/if}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>