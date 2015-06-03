{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 6287 $
 * @author SARL OpenXtrem
 * @license GNU GPL
*}}

<h2>Import de protocoles de DHE Mediboard.</h2>

{{mb_include module=system template=inc_import_csv_info_intro}}
  <li><strong>{{mb_label class=CProtocole field=function_id}}</strong> ({{mb_label class=CFunctions field=text}})</li>
  <li>{{mb_label class=CProtocole field=chir_id         }} ({{mb_label class=CMediusers field=_user_last_name }})</li>
  <li>{{mb_label class=CProtocole field=chir_id         }} ({{mb_label class=CMediusers field=_user_first_name}})</li>
  <li><strong>{{mb_label class=CProtocole field=libelle}}</strong> (mise à jour du protocole ayant exactement le même libellé)</li>
  <li><strong>{{mb_label class=CProtocole field=libelle_sejour}}</strong> (mise à jour du protocole de séjour ayant exactement le même libellé)</li>
  <li><strong>{{mb_label class=CProtocole field=temp_operation}}</strong> (<code>HH:MM</code>)</li>
  <li>{{mb_label class=CProtocole field=codes_ccam}} (séparés par des barres verticales <code>|</code>)</li>
  <li>{{mb_label class=CProtocole field=DP}} (séparés par des barres verticales <code>|</code>)</li>
  <li>
    <strong>{{mb_label class=CProtocole field=type}}</strong> 
    (parmi <code>comp</code>, <code>ambu</code>, <code>exte</code>, <code>seances</code>, <code>ssr</code>, <code>psy</code>, <code>urg</code> ou <code>consult</code>)
  </li>
  <li><strong>{{mb_label class=CProtocole field=duree_hospi}}</strong></li>
  <li>{{mb_label class=CProtocole field=duree_uscpo}}</li>
  <li>{{mb_label class=CProtocole field=duree_preop}} (<code>HH:MM</code>)</li>
  <li>{{mb_label class=CProtocole field=presence_preop }} (<code>HH:MM</code>)</li>
  <li>{{mb_label class=CProtocole field=presence_postop}} (<code>HH:MM</code>)</li>
  <li>{{mb_label class=CProtocole field=uf_hebergement_id}}</li>
  <li>{{mb_label class=CProtocole field=uf_medicale_id}}</li>
  <li>{{mb_label class=CProtocole field=uf_soins_id}}</li>
  <li><strong>mb_label class=CProtocole field=facturable}}</strong>(<code>0</code> pour un protocole non facturable, <code>1</code> pour un protocole facturable)</li>
  <li><strong>{{mb_label class=CProtocole field=for_sejour}}</strong> (<code>0</code> pour un protocole d'intervention, <code>1</code> pour un protocole de séjour uniquement)</li>
  <li>{{mb_label class=CProtocole field=exam_extempo}}</li>
  <li>
    {{mb_label class=CProtocole field=cote}}
    (parmi <code>comp</code>, <code>droit</code>, <code>gauche</code>, <code>haut</code>, <code>bas</code>, <code>bilatéral</code>, <code>total</code> ou <code>inconnu</code>)
  </li>
  <li>{{mb_label class=CProtocole field=examen}}</li>
  <li>{{mb_label class=CProtocole field=materiel}}</li>
  <li>{{mb_label class=CProtocole field=exam_per_op}}</li>
  <li>{{mb_label class=CProtocole field=depassement}}</li>
  <li>{{mb_label class=CProtocole field=forfait}}</li>
  <li>{{mb_label class=CProtocole field=fournitures}}</li>
  <li>{{mb_label class=CProtocole field=rques_operation}}</li>
  <li>{{mb_label class=CProtocole field=convalescence}}</li>
  <li>{{mb_label class=CProtocole field=rques_sejour}}</li>
  <li>{{mb_label class=CProtocole field=septique}}</li>
  <li>{{mb_label class=CProtocole field=duree_heure_hospi}}</li>
  <li>{{mb_label class=CProtocole field=pathologie}}</li>
  <li>{{mb_label class=CProtocole field=type_pec}}</li>

{{mb_include module=system template=inc_import_csv_info_outro}}

<form method="post" action="?m={{$m}}&amp;{{$actionType}}={{$action}}&amp;dialog=1&amp;" name="import" enctype="multipart/form-data">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="{{$actionType}}" value="{{$action}}" />  
  <input type="hidden" name="MAX_FILE_SIZE" value="4096000" />
  <input type="file" name="import" />
  <input type="checkbox" name="dryrun" value="1" checked="checked" />
  <label for="dryrun">Essai à blanc</label>
  <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
</form>

{{if $results|@count}}
<table class="tbl">
  <tr>
    <th class="title" colspan="35">{{$results|@count}} protocoles trouvés</th>
  </tr>
  <tr>
    <th>Etat</th>
    <th>{{mb_title class=CProtocole field=function_id}}</th>
    <th>{{mb_title class=CProtocole field=chir_id}} <br />{{mb_title class=CMediusers field=_user_last_name }}</th>
    <th>{{mb_title class=CProtocole field=chir_id}} <br />{{mb_title class=CMediusers field=_user_first_name}}</th>
    <th>{{mb_title class=CProtocole field=libelle}}</th>
    <th>{{mb_title class=CProtocole field=libelle_sejour}}</th>
    <th>{{mb_title class=CProtocole field=temp_operation}}</th>
    <th>{{mb_title class=CProtocole field=codes_ccam}}</th>
    <th>{{mb_title class=CProtocole field=DP}}</th>
    <th>{{mb_title class=CProtocole field=type}}</th>
    <th>{{mb_title class=CProtocole field=duree_hospi}}</th>
    <th>{{mb_title class=CProtocole field=duree_uscpo}}</th>
    <th>{{mb_title class=CProtocole field=duree_preop}}</th>
    <th>{{mb_title class=CProtocole field=presence_preop}}</th>
    <th>{{mb_title class=CProtocole field=presence_postop}}</th>
    <th>{{mb_title class=CProtocole field=uf_hebergement_id}}</th>
    <th>{{mb_title class=CProtocole field=uf_medicale_id}}</th>
    <th>{{mb_title class=CProtocole field=uf_soins_id}}</th>
    <th>{{mb_title class=CProtocole field=facturable}}</th>
    <th>{{mb_title class=CProtocole field=for_sejour}}</th>
    <th>{{mb_title class=CProtocole field=exam_extempo}}</th>
    <th>{{mb_title class=CProtocole field=cote}}</th>
    <th>{{mb_title class=CProtocole field=examen}}</th>
    <th>{{mb_title class=CProtocole field=materiel}}</th>
    <th>{{mb_title class=CProtocole field=exam_per_op}}</th>
    <th>{{mb_title class=CProtocole field=depassement}}</th>
    <th>{{mb_title class=CProtocole field=forfait}}</th>
    <th>{{mb_title class=CProtocole field=fournitures}}</th>
    <th>{{mb_title class=CProtocole field=rques_operation}}</th>
    <th>{{mb_title class=CProtocole field=convalescence}}</th>
    <th>{{mb_title class=CProtocole field=rques_sejour}}</th>
    <th>{{mb_title class=CProtocole field=septique}}</th>
    <th>{{mb_title class=CProtocole field=duree_heure_hospi}}</th>
    <th>{{mb_title class=CProtocole field=pathologie}}</th>
    <th>{{mb_title class=CProtocole field=type_pec}}</th>
  </tr>
  {{foreach from=$results item=_protocole}}
  <tr>
    {{if count($_protocole.errors)}}
    <td class="text warning compact">
      {{foreach from=$_protocole.errors item=_error}}
        <div>{{$_error}}</div>
      {{/foreach}}
    </td>
    {{else}}
    <td class="text ok">
      OK
    </td>
    {{/if}}

    <td class="text">{{$_protocole.function_name}}</td>
    <td class="text">{{$_protocole.praticien_lastname}}</td>
    <td class="text">{{$_protocole.praticien_firstname}}</td>
    <td class="text">{{$_protocole.motif}}</td>
    <td class="text">{{$_protocole.libelle_sejour}}</td>
    <td class="text">{{$_protocole.temp_operation}}</td>
    <td class="text">{{$_protocole.codes_ccam}}</td>
    <td class="text">{{$_protocole.DP}}</td>
    <td class="text">{{$_protocole.type_hospi}}</td>
    <td class="text">{{$_protocole.duree_hospi}}</td>
    <td class="text">{{$_protocole.duree_uscpo}}</td>
    <td class="text">{{$_protocole.duree_preop}}</td>
    <td class="text">{{$_protocole.presence_preop}}</td>
    <td class="text">{{$_protocole.presence_postop}}</td>
    <td class="text">{{$_protocole.uf_hebergement}}</td>
    <td class="text">{{$_protocole.uf_medicale}}</td>
    <td class="text">{{$_protocole.uf_soins}}</td>
    <td class="text">{{$_protocole.facturable}}</td>
    <td class="text">{{$_protocole.for_sejour}}</td>
    <td class="text">{{$_protocole.Exam_extempo_prevu}}</td>
    <td class="text">{{$_protocole.cote}}</td>
    <td class="text">{{$_protocole.bilan_preop}}</td>
    <td class="text">{{$_protocole.materiel_a_prevoir}}</td>
    <td class="text">{{$_protocole.examens_perop}}</td>
    <td class="text">{{$_protocole.depassement_honoraires}}</td>
    <td class="text">{{$_protocole.forfait_clinique}}</td>
    <td class="text">{{$_protocole.fournitures}}</td>
    <td class="text">{{$_protocole.rques_interv}}</td>
    <td class="text">{{$_protocole.convalesence}}</td>
    <td class="text">{{$_protocole.rques_sejour}}</td>
    <td class="text">{{$_protocole.septique}}</td>
    <td class="text">{{$_protocole.duree_heure_hospi}}</td>
    <td class="text">{{$_protocole.pathologie}}</td>
    <td class="text">{{$_protocole.type_pec}}</td>
  </tr>
  {{/foreach}}
</table>
{{/if}}

