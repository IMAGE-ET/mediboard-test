{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 6287 $
 * @author SARL OpenXtrem
 * @license GNU GPL
*}}

<h2>Import de protocoles de DHE Mediboard.</h2>

<div class="small-info">
  Veuillez indiquez les champs suivants dans un fichier CSV (<strong>au format ISO</strong>) dont les champs sont séparés par
  <strong>;</strong> et les textes par <strong>"</strong>, la première ligne étant sautée :
  <ul>
    <li>Nom de la fonction *</li>
    <li>Nom du praticien</li>
    <li>Prénom du praticien</li>
    <li>Motif d'hospitalisation *</li>
    <li>Durée d'intervention (HH:MM) *</li>
    <li>Acte(s) CCAM (séparés par des |)</li>
    <li>Type d'hospitalisation (comp, ambu, exte, seances, ssr, psy, urg ou consult) *</li>
    <li>Durée d'hospitalisation en nuits *</li>
    <li>Durée USCPO</li>
    <li>Durée préop (HH:MM)</li>
    <li>Présence préop (HH:MM)</li>
    <li>Présence postop (HH:MM)</li>
    <li>UF d'hébergement</li>
    <li>UF de soins</li>
    <li>UF médicale</li>
  </ul>
  <em>* : champs obligatoires</em>
</div>

<form method="post" action="?m={{$m}}&amp;{{$actionType}}={{$action}}&amp;dialog=1&amp;" name="import" enctype="multipart/form-data">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="{{$actionType}}" value="{{$action}}" />
  
  <input type="hidden" name="MAX_FILE_SIZE" value="4096000" />
  <input type="file" name="import" />
  
  <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
</form>

{{if $results|@count}}
<table class="tbl">
  <tr>
    <th class="title" colspan="16">{{$results|@count}} protocoles trouvés</th>
  </tr>
  <tr>
    <th>Etat</th>
    <th>Fonction</th>
    <th>Nom</th>
    <th>Prénom</th>
    <th>Motif d'hospitalisation</th>
    <th>Durée d'intervention</th>
    <th>Actes</th>
    <th>Type d'hospi</th>
    <th>Durée d'hospi</th>
    <th>Durée USCPO</th>
    <th>Durée préop</th>
    <th>Présence préop</th>
    <th>Présence postop</th>
    <th>UF d'hébergement</th>
    <th>UF médicale</th>
    <th>UF de soins</th>
  </tr>
  {{foreach from=$results item=_protocole}}
  <tr>
    <td class="text">
      {{if $_protocole.error}}
        {{$_protocole.error}}
      {{else}}
        OK
      {{/if}}
    </td>
    <td>{{$_protocole.function}}</td>
    <td>{{$_protocole.nom}}</td>
    <td>{{$_protocole.prenom}}</td>
    <td>{{$_protocole.motif}}</td>
    <td>{{$_protocole.temps_op}}</td>
    <td>{{$_protocole.actes}}</td>
    <td>{{$_protocole.type_hospi}}</td>
    <td>{{$_protocole.duree_hospi}}</td>
    <td>{{$_protocole.duree_uscpo}}</td>
    <td>{{$_protocole.duree_preop}}</td>
    <td>{{$_protocole.presence_preop}}</td>
    <td>{{$_protocole.presence_postop}}</td>
    <td>{{$_protocole.uf_hebergement}}</td>
    <td>{{$_protocole.uf_medicale}}</td>
    <td>{{$_protocole.uf_soins}}</td>
  </tr>
  {{/foreach}}
</table>
{{/if}}

