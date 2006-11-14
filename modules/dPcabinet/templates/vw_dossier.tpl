<!-- $Id$ -->

<script type="text/javascript">

function pageMain() {
  PairEffect.initGroup("consEffect");
  PairEffect.initGroup("operEffect");
}

function popPat() {
  var url = new Url;
  url.setModuleAction("dPpatients", "pat_selector");
  url.popup(500, 500, 'Patient');
}

function printPack(hospi_id, pack_id) {
  if (pack_id) {
    var url = new Url;
    url.setModuleAction("dPcompteRendu", "edit_compte_rendu");
    url.addParam("object_id", hospi_id);
    url.addParam("pack_id", pack_id);
    url.popup(700, 600, "Impression de pack");
  }
}

function setPat( key, val ) {
  var f = document.patFrm;

  if (val != '') {
    f.patSel.value = key;
    f.patNom.value = val;
  }
  
  f.submit();
}
</script>


<table class="main">
  <tr>
    <td class="greedyPane" colspan="2">
      <form name="patFrm" action="index.php" method="get">
      <table class="form">
        <tr><th>Choix du patient :</th>
          <td class="readonly">
            <input type="hidden" name="m" value="{{$m}}" />
            <input type="hidden" name="patSel" value="{{$patient->patient_id}}" />
            <input type="text" size="40" readonly="readonly" ondblclick="popPat()" name="patNom" value="{{$patient->_view}}" />
          </td>
          <td class="button">
            <button class="search" type="button" onclick="popPat()">Chercher</button>
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
  {{if $patient->patient_id}}
  <tr>
    <td>
      <table class="form">
		<!-- Consultations -->
        <tr>
          <th class="category" colspan="2">Consultations</th>
        </tr>
        {{foreach from=$patient->_ref_consultations item=curr_consult}}
        {{if $curr_consult->_canEdit}}
        <tr id="cons{{$curr_consult->consultation_id}}-trigger">
          <td colspan="2">
            <strong>
            Dr. {{$curr_consult->_ref_plageconsult->_ref_chir->_view}} &mdash;
            {{$curr_consult->_ref_plageconsult->date|date_format:"%A %d %B %Y"}} &mdash;
            {{$curr_consult->_etat}}
            </strong>
          </td>
        </tr>
        <tbody class="consEffect" id="cons{{$curr_consult->consultation_id}}">
          <tr>
            <td colspan="2">
              <a href="index.php?m=dPcabinet&amp;tab=edit_consultation&amp;selConsult={{$curr_consult->consultation_id}}">
                Voir la consultation
              </a>
            </td>
          </tr>
          <tr>
            <th>Motif :</th>
            <td class="text">{{$curr_consult->motif}}</td>
          </tr>
          {{if $curr_consult->rques}}
          <tr>
            <th>Remarques :</th>
            <td class="text">{{$curr_consult->rques}}</td>
          </tr>
          {{/if}}
          {{if $curr_consult->examen}}
          <tr>
            <th>Examen :</th>
            <td class="text">{{$curr_consult->examen}}</td>
          </tr>
          {{/if}}
          {{if $curr_consult->traitement}}
           <tr>
             <th>Traitement :</th>
             <td class="text">{{$curr_consult->traitement}}</td>
           </tr>
          {{/if}}
          <tr>
            <th>Documents attachés :</th>
            <td>
            <ul>
              {{if $curr_consult->_nb_files_docs}}
              <li>
                <a href="index.php?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$patient->_id}}&amp;selClass={{$curr_consult->_class_name}}&amp;selKey={{$curr_consult->_id}}">
                  Consulter les documents
                </a>
              </li>
              {{else}}
              <li>Aucun document créé</li>
              {{/if}}
              </ul>
              <form name="uploadFrm" action="?m={{$m}}" enctype="multipart/form-data" method="post">
              <input type="hidden" name="m" value="dPfiles" />
              <input type="hidden" name="dosql" value="do_file_aed" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="file_class" value="CConsultation" />
              <input type="hidden" name="file_object_id" value="{{$curr_consult->consultation_id}}" />
              <input type="file" name="formfile" />
              <button class="submit" type="submit">Ajouter</button>

              </form>
            </td>
          </tr>
        </tbody>
        {{/if}}
        {{/foreach}}
        
		<!-- Sejours -->
        {{foreach from=$patient->_ref_sejours item=curr_sejour}}
        {{if $curr_sejour->_canEdit}}
        <tr>
          <th class="category" colspan="2">
          	Séjour du {{$curr_sejour->entree_prevue|date_format:"%d %B %Y à %Hh%M"}}
          	au {{$curr_sejour->sortie_prevue|date_format:"%d %B %Y à %Hh%M"}}
          </th>
        </tr>
        {{foreach from=$curr_sejour->_ref_operations item=curr_op}}
        <tr id="oper{{$curr_op->operation_id}}-trigger">
          <td colspan="2">
            <strong>
            Dr. {{$curr_op->_ref_chir->_view}} &mdash;
            {{$curr_op->_ref_plageop->date|date_format:"%d %B %Y"}}
            {{if $curr_op->_nb_files_docs}}
              &mdash; {{$curr_op->_nb_files_docs}} Doc.
            {{/if}}
            </strong>
          </td>
        </tr>
        <tbody class="operEffect" id="oper{{$curr_op->operation_id}}">
          <tr>
            <td colspan="2">
              <a href="index.php?m=dPplanningOp&amp;tab=vw_idx_planning&amp;selChir={{$curr_op->_ref_plageop->chir_id}}&amp;date={{$curr_op->_ref_plageop->date}}">
                Voir l'intervention
              </a>
            </td>
          </tr>
          <tr>
            <th>Actes Médicaux :</th>
            <td class="text">
              <ul>
                {{if $curr_op->libelle}}
                <li><em>[{{$curr_op->libelle}}]</em></li>
                {{/if}}
                {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
                <li><strong>{{$curr_code->code}}</strong> : {{$curr_code->libelleLong}}</li>
                {{/foreach}}
              </ul>
            </td>
          </tr>
          <tr>
            <th>
              Documents attachés :
            </th>
            <td>
            <ul>
              {{if $curr_op->_nb_files_docs}}
              <li>
                <a href="index.php?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$patient->_id}}&amp;selClass={{$curr_op->_class_name}}&amp;selKey={{$curr_op->_id}}">
                  Consulter les documents
                </a>
              </li>
              {{else}}
              <li>Aucun document créé</li>
              {{/if}}
            </ul>
              <form name="uploadFrm" action="?m=dPcabinet" enctype="multipart/form-data" method="post">
              <input type="hidden" name="m" value="dPfiles" />
              <input type="hidden" name="dosql" value="do_file_aed" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="file_class" value="COperation" />
              <input type="hidden" name="file_object_id" value="{{$curr_op->operation_id}}" />
              <input type="file" name="formfile" />
              <button class="submit" type="submit">Ajouter</button>

              </form>
            </td>
          </tr>
        </tbody>
        {{/foreach}}
        {{/if}}
        {{/foreach}}
      </table>
    </td>
    <td class="pane" id="vwPatient">
    {{include file="../../dPpatients/templates/inc_vw_patient.tpl"}}
    </td>
  </tr>
  {{/if}}
</table>

