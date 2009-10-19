{{if !$ajax}}
<h2>Génération d'un fichier XML PatientStayInformation</h2>
{{/if}}

<table class="main">
  <tr>
    <td>
      {{if !$doc_valid}}
        <h3>Document non valide!</h3>
      {{/if}}
      
      {{if $doc->msgError|@count}}
      <ul>
        {{foreach from=$doc->msgError item=curr_error}}
        <li>
          {{$curr_error}}
        </li>
        {{/foreach}}
      </ul>
      {{/if}}
      
      {{if !$ajax}}
        <h3>XML: Schema de validation</h3>
        <ul>
          <li>Consulter <a href="{{$doc->schemafilename}}">le Schema de validation XML</a>.</li>
        </ul>
      {{/if}}
  
      {{if $doc->documentfilename}}
        <h3>XML: Génération du document</h3>
        <ul>
          <li>
            Consulter <a href="{{$doc->documentfilename}}">le Document XML</a>: 
            Le document <strong>{{if $doc_valid}}est valide!{{else}}n'est pas valide...{{/if}}</strong>
          </li>
          <li>
            Visualiser <a href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$mbSejour->sejour_id}}">le sejour correspondant</a>
          </li>
        </ul>
      {{/if}}
    </td>
  </tr>
</table>