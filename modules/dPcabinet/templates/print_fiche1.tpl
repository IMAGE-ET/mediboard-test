    </td>
  </tr>
</table>

{{assign var="patient" value=$consult->_ref_patient}}
{{assign var="consult_anesth" value=$consult->_ref_consult_anesth}}
{{assign var="sejour" value=$consult_anesth->_ref_operation->_ref_sejour}}
{{assign var="operation" value=$consult_anesth->_ref_operation}}

{{assign var=const_med value=$patient->_ref_constantes_medicales}}
{{assign var=dossier_medical value=$patient->_ref_dossier_medical}}
{{assign var=ant value=$dossier_medical->_ref_antecedents}}
<table class="form" id="admission" style="page-break-after: always;">
  <tr>
    <td colspan="2">
      <table width="100%">
        <tr>
          <th class="title" colspan="4">
            <a href="#" onclick="window.print()">
              Dossier d'anesthésie (1/2)
            </a>
          </th>
        </tr>
        <tr>
          <th class="category" colspan="4">
            {{$patient->_view}}
          </th>
        </tr>
        <tr>
          <th>Telephone</th>
          <td>{{$patient->tel}}</td>
          <th>Mobile</th>
          <td>{{$patient->tel2}}</td>
        </tr>
        <tr>
          <th>Age</th>
          <td>{{$patient->_age}} ans</td>
          <th>Profession</th>
          <td>{{$patient->profession}}</td>
        </tr>
        <tr>
          <th>Poids</th>
          <td>{{if $const_med->poids}}{{$const_med->poids}} kg{{else}}-{{/if}}</td>
          <th>Taille</th>
          <td>{{if $const_med->taille}}{{$const_med->taille}} cm{{else}}-{{/if}}</td>
        </tr>
        <tr>
          <th>Anesthésiste</th>
          <td>Dr {{$consult->_ref_chir->_view}}</td>
          <th>Consultation</th>
          <td>{{$consult->_ref_plageconsult->date|date_format:"%A %d %B %Y"}}</td>
        </tr>
        <tr>
          <th>Intervention</th>
          <td>{{$operation->libelle}}</td>
          <th>Date</th>
          <td>Dr {{$operation->_ref_chir->_view}}</td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td width="50%">
      <table width="100%">
        <tr>
          <th class="category" colspan="2">Séjour</th>
        </tr>
        <tr>
          <th>Type</th>
          <td>{{mb_value object=$sejour field="type"}}</td>
        </tr>
        <tr>
          <th>Entrée</th>
          <td>{{mb_value object=$sejour field="_entree"}}</td>
        </tr>
        <tr>
          <th>Sortie</th>
          <td>{{mb_value object=$sejour field="_sortie"}}</td>
        </tr>
      </table>
    </td>
    <td width="50%">
      <table width="100%">
        <tr>
          <th class="category" colspan="2">Intervention</th>
        </tr>
        <tr>
          <th>Date</th>
          <td>{{mb_value object=$operation->_ref_plageop field="date"}}</td>
        </tr>
        <tr>
          <th>Chirurgien</th>
          <td>Dr {{$operation->_ref_chir->_view}}</td>
        </tr>
        <tr>
          <th>Libellé</th>
          <td>
            <ul>
              {{if $operation->libelle}}
                <li><em>[{{$operation->libelle}}]</em></li>
              {{/if}}
              {{foreach from=$operation->_ext_codes_ccam item=curr_code}}
              <li><em>{{$curr_code->libelleLong}}</em> ({{$curr_code->code}}) (coté {{tr}}COperation.cote.{{$operation->cote}}{{/tr}})</li>
              {{/foreach}}
            </ul>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
      <table width="100%">
        <tr>
          <th class="category">Examen clinique</th>
        </tr>
        <tr>
          <td>
            <ul>
              <li>
                <strong>Cardiovasculaire</strong>
                <br />
                {{$consult_anesth->examenCardio|nl2br}}
              </li>
              <li>
                <strong>Pulmonaire</strong>
                <br />
                {{$consult_anesth->examenPulmo|nl2br}}
              </li>
              <li>
                <strong>Autre</strong>
                <br />
                {{$consult->examen|nl2br}}
              </li>
            </ul>
          </td>
        </tr>
      </table>
    </td>
    <td>
      <table width="100%">
        <tr>
          <th class="category">Addictions</th>
        </tr>
        <tr>
          <td>
            {{if $dossier_medical->_ref_addictions}}
              {{foreach from=$dossier_medical->_ref_types_addiction key=curr_type item=list_addiction}}
                {{if $list_addiction|@count}}
                <strong>{{tr}}CAddiction.type.{{$curr_type}}{{/tr}}</strong>
                {{foreach from=$list_addiction item=curr_addiction}}
                  <ul>
                    <li>
                      {{$curr_addiction->addiction}}
                    </li>
                  </ul>
                {{/foreach}}
                {{/if}}
              {{/foreach}}
            {{/if}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
      <table width="100%">
        <tr>
          <th class="category">Traitements</th>
        </tr>
        <tr>
          <td>
          </td>
        </tr>
      </table>
    </td>
    <td>
      <table width="100%">
        <tr>
          <th class="category">Intubation</th>
        </tr>
        <tr>
          <td>
          </td>
        </tr>
        <tr>
          <th class="category">Prémédication</th>
        </tr>
        <tr>
          <td>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<table class="form" id="admission" style="page-break-after: always;">
  <tr>
    <td>
      <table width="100%">
        <tr>
          <th class="title" colspan="4">
            <a href="#" onclick="window.print()">
              Dossier d'anesthésie (2/2)
            </a>
          </th>
        </tr>
        <tr>
          <th class="category" colspan="4">
            Antécédents chirgicaux et anesthésiques
          </th>
        </tr>
        <tr>
          <th class="category" colspan="4">
            Cardiovasculaire
          </th>
        </tr>
        <tr>
          <th class="category" colspan="4">
            Pulmonaire
          </th>
        </tr>
        <tr>
          <th class="category" colspan="4">
            Digestif
          </th>
        </tr>
        <tr>
          <th class="category" colspan="2">
            Uro-nephrologie
          </th>
          <th class="category" colspan="2">
            Neuro-psychiatrie
          </th>
        </tr>
        <tr>
          <th class="category" colspan="2">
            Gyneco-obstétrique
          </th>
          <th class="category" colspan="2">
            Endocrinologie
          </th>
        </tr>
        <tr>
          <th class="category" colspan="2">
            Hématologie - hémostase
          </th>
          <th class="category" colspan="2">
            Divers
          </th>
        </tr>
      </table>
    </td>
  </tr>
</table>

<table class="main">
  <tr>
    <td>