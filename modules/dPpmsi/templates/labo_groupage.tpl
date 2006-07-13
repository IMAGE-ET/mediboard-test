<table class="main">
  <tr>
    <td class="halfpane">
      <table class="form">
        <tr>
          <th class="title" style="text-align:left;">
            <a class="button" href="index.php?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$patient->patient_id}}" style="float:right;">
              Modifier le patient
            </a>
            {{$patient->_view}}
          </th>
        </tr>
        <tr>
          <td>
          	Séjour du {{$sejour->entree_prevue|date_format:"%A %d %B %Y à %H:%M"}}
            au {{$sejour->sortie_prevue|date_format:"%A %d %B %Y à %H:%M"}}
          </td>
        </tr>
        <tr>
          <td>Agé de {{$GHM->_age}} lors de son admission, de sexe {{$GHM->_sexe}}</td>
        </tr>
      </table>
      <table class="form">
        <tr>
          <th class="title" style="text-align:left;">
            <a class="button" href="index.php?m=dPpmsi&tab=vw_dossier&amp;pat_id={{$patient->patient_id}}" style="float:right;">
              Voir le dossier
            </a>
            Hospitalisation
          </th>
        </tr>
      </table>
      <form name="editFrm" action="index.php?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="dosql" value="do_ghm_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="ghm_id" value="{{$GHM->ghm_id}}" />
      <input type="hidden" name="sejour_id" value="{{$sejour->sejour_id}}" />
      <table class="form">
        <tr>
          <th class="category">DP</th>
          <th class="category">DR</th>
          <th class="category">DAS (sign.)</th>
          <th class="category">DAD (doc.)</th>
        </tr>
        <tr>
          <td>{{$GHM->_DP}}</td>
          <td><input type="text" name="DR" value="{{$GHM->DR}}" size="7" /></td>
          <td>
            {{counter start=0 print=false assign=curr}}
            {{foreach from=$GHM->_DASs item=DAS key=key}}
            <input type="text" name="_DASs[{{$curr}}]" value="{{$DAS}}" size="7" /><br />
            {{counter}}
            {{/foreach}}
            <input type="text" name="_DASs[{{$GHM->_DASs|@count}}]" value="" size="7" />
          </td>
          <td>
            {{counter start=0 print=false assign=curr}}
            {{foreach from=$GHM->_DADs item=DAD key=key}}
            <input type="text" name="_DADs[{{$curr}}]" value="{{$DAD}}" size="7" /><br />
            {{counter}}
            {{/foreach}}
            <input type="text" name="_DADs[{{$GHM->_DADs|@count}}]" value="" size="7" />
          </td>
        </tr>
      </table>
      <table class="form">
        <tr>
          <th class="category">Type</th>
          <th class="category">Durée</th>
          <th class="category">Nb. de séances</th>
          <th class="category">Motif de séjour</th>
          <th class="category">Destination</th>
        </tr>
        <tr>
          <td>{{$GHM->_type_hospi}}</td>
          <td>{{$GHM->_duree}} jours</td>
          <td>{{if $GHM->_seances}}{{$GHM->_seances}}{{else}}-{{/if}}</td>
          <td>{{$GHM->_motif}}</td>
          <td>{{$GHM->_destination}}</td>
        </tr>
        <tr>
          <td class="button" colspan="5">
            <button class="submit" type="submit">Sauver</button>
          </td>
        </tr>
      </table>
      </form>
      <table class="form">
        {{foreach from=$sejour->_ref_operations item=curr_op}}
        <tr>
          <th class="title" colspan="3" style="text-align:left;">
            <a class="button" href="index.php?m=dPpmsi&amp;tab=edit_actes&amp;operation_id={{$curr_op->operation_id}}" style="float:right;">
              Modifier les actes
            </a>
            Actes du Dr. {{$curr_op->_ref_chir->_view}} le {{$curr_op->_datetime|date_format:"%d %B %Y"}}
          </th>
        </tr>
        {{/foreach}}
        <tr>
          <th class="category">Code</th>
          <th class="category">Phase</th>
          <th class="category">Activite</th>
        </tr>
        {{foreach from=$GHM->_actes item=acte key=key}}
        <tr>
          <td>{{$acte.code}}</td>
          <td>{{$acte.phase}}</td>
          <td>{{$acte.activite}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
    <td class="halfpane">
      <table class="tbl">
        <tr>
          <th class="title">
            Résultat
          </th>
        </tr>
        <tr>
          <td class="text">
            {{if $GHM->_CM}}
            <strong>Catégorie majeure CM{{$GHM->_CM}}</strong> : {{$GHM->_CM_nom}}
            <br />
            <strong>GHM</strong> : {{$GHM->_GHM}} ({{$GHM->_tarif_2006}} €)
            <br />
            {{$GHM->_GHM_nom}}
            <br />
            <i>Appartenance aux groupes {{$GHM->_GHM_groupe}}</i>
            <br />
            <strong>Bornes d'hospitalisation</strong> : de {{$GHM->_borne_basse}} jour(s) à {{$GHM->_borne_haute}} jours
            <br />
            <strong>Chemin :</strong> <br />
            {{$GHM->_chemin}}
            {{else}}
            <strong>{{$GHM->_GHM}}</strong>
            {{/if}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>