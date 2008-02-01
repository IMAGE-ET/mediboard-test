      <table class="tbl">
        <tr>
          <th colspan="4"class="title">Alertes</th>
        </tr>
        <tr>
          <td>
            {{if $alertesInteractions|@count}}
              <img src="images/icons/note_red.png" title="aucune" alt="aucune" />
            {{else}}
              <img src="images/icons/note_green.png" title="aucune" alt="aucune" />
            {{/if}}
          </td>
          <td>
            <strong>{{$alertesInteractions|@count}}</strong>
            interactions
          </td>
          <td>
            {{if $alertesAllergies|@count}}
              <img src="images/icons/note_red.png" title="aucune" alt="aucune" />
            {{else}}
              <img src="images/icons/note_green.png" title="aucune" alt="aucune" />
            {{/if}}
          </td>
          <td>
            <strong>{{$alertesAllergies|@count}}</strong>
            hypersensibilité(s)
          </td>
        </tr>
        <tr>
          <td>
            {{if $alertesProfil|@count}}
              <img src="images/icons/note_red.png" title="aucune" alt="aucune" />
            {{else}}
              <img src="images/icons/note_green.png" title="aucune" alt="aucune" />
            {{/if}}
          </td>
          <td colspan="3">
            <strong>{{$alertesProfil|@count}}</strong>
            contre-indication(s) / précaution(s) d'emploi
          </td>
        </tr>
        <tr>
          <td>
            {{if $alertesIPC|@count}}
              <img src="images/icons/note_red.png" title="aucune" alt="aucune" />
            {{else}}
              <img src="images/icons/note_green.png" title="aucune" alt="aucune" />
            {{/if}}
          </td>
          <td colspan="3">
            <strong>{{$alertesIPC|@count}}</strong>
            incompatibilité(s) physico-chimique(s)
          </td>
        </tr>
      </table>