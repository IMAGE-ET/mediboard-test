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
  <li><strong>{{mb_label class=CProtocole field=temp_operation}}</strong> (<tt>HH:MM</tt>)</li>
  <li>{{mb_label class=CProtocole field=codes_ccam}} (séparés par des barres verticales <tt>|</tt>)</li>
  <li>
    <strong>{{mb_label class=CProtocole field=type}}</strong> 
    (parmi <tt>comp</tt>, <tt>ambu</tt>, <tt>exte</tt>, <tt>seances</tt>, <tt>ssr</tt>, <tt>psy</tt>, <tt>urg</tt> ou <tt>consult</tt>)
  </li>
  <li><strong>{{mb_label class=CProtocole field=duree_hospi}}</strong></li>
  <li>{{mb_label class=CProtocole field=duree_uscpo}}</li>
  <li>{{mb_label class=CProtocole field=duree_preop}} (<tt>HH:MM</tt>)</li>
  <li>{{mb_label class=CProtocole field=presence_preop }} (<tt>HH:MM</tt>)</li>
  <li>{{mb_label class=CProtocole field=presence_postop}} (<tt>HH:MM</tt>)</li>
  <li>{{mb_label class=CProtocole field=uf_hebergement_id}}</li>
  <li>{{mb_label class=CProtocole field=uf_medicale_id}}</li>
  <li>{{mb_label class=CProtocole field=uf_soins_id}}</li>
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
    <th class="title" colspan="16">{{$results|@count}} protocoles trouvés</th>
  </tr>
  <tr>
    <th>Etat</th>
    <th>{{mb_title class=CProtocole field=function_id}}</th>
    <th>{{mb_title class=CProtocole field=chir_id}} <br />{{mb_title class=CMediusers field=_user_last_name }}</th>
    <th>{{mb_title class=CProtocole field=chir_id}} <br />{{mb_title class=CMediusers field=_user_first_name}}</th>
    <th>{{mb_title class=CProtocole field=libelle}}</th>
    <th>{{mb_title class=CProtocole field=temp_operation}}</th>
    <th>{{mb_title class=CProtocole field=codes_ccam}}</th>
    <th>{{mb_title class=CProtocole field=type}}</th>
    <th>{{mb_title class=CProtocole field=duree_hospi}}</th>
    <th>{{mb_title class=CProtocole field=duree_uscpo}}</th>
    <th>{{mb_title class=CProtocole field=duree_preop}}</th>
    <th>{{mb_title class=CProtocole field=presence_preop}}</th>
    <th>{{mb_title class=CProtocole field=presence_postop}}</th>
    <th>{{mb_title class=CProtocole field=uf_hebergement_id}}</th>
    <th>{{mb_title class=CProtocole field=uf_medicale_id}}</th>
    <th>{{mb_title class=CProtocole field=uf_soins_id}}</th>
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
    <td class="text">{{$_protocole.temp_operation}}</td>
    <td class="text">{{$_protocole.codes_ccam}}</td>
    <td class="text">{{$_protocole.type_hospi}}</td>
    <td class="text">{{$_protocole.duree_hospi}}</td>
    <td class="text">{{$_protocole.duree_uscpo}}</td>
    <td class="text">{{$_protocole.duree_preop}}</td>
    <td class="text">{{$_protocole.presence_preop}}</td>
    <td class="text">{{$_protocole.presence_postop}}</td>
    <td class="text">{{$_protocole.uf_hebergement}}</td>
    <td class="text">{{$_protocole.uf_medicale}}</td>
    <td class="text">{{$_protocole.uf_soins}}</td>
  </tr>
  {{/foreach}}
</table>
{{/if}}

