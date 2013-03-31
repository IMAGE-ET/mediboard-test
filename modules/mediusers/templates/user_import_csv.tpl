{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage mediusers
 * @version $Revision: 6287 $
 * @author SARL OpenXtrem
 * @license GNU GPL
*}}

<h2>Import d'utilisateurs Mediboard.</h2>

{{mb_include module=system template=inc_import_csv_info_intro}}
  <li><strong>{{mb_label class=CMediusers field=_user_last_name }}</strong></li>
  <li><strong>{{mb_label class=CMediusers field=_user_first_name}}</strong></li>
  <li>{{mb_label class=CMediusers field=_user_username  }} </li>
  <li>{{mb_label class=CMediusers field=_user_password  }} </li>
  <li><strong>{{mb_label class=CMediusers field=_user_type      }}</strong> (code numérique)</li>
  <li><strong>{{mb_label class=CMediusers field=function_id     }}</strong> ({{mb_label class=CFunctions field=text}}) : fonction créée si introuvable</li>
  <li>{{mb_label class=CMediusers field=_profile_id     }} ({{mb_label class=CUser field=user_username}}): profil non créé si introuvable</li>
  <li>{{mb_label class=CMediusers field=adeli           }} </li>
  <li>{{mb_label class=CMediusers field=rpps            }} </li>
  <li>{{mb_label class=CMediusers field=spec_cpam_id    }} (code à deux chiffres): spcécialité non créée si introuvable</li>
  <li>{{mb_label class=CMediusers field=discipline_id   }} : discipline non créée si introuvable</li>
{{mb_include module=system template=inc_import_csv_info_outro}}

<form method="post" action="?m={{$m}}&amp;{{$actionType}}={{$action}}&amp;dialog=1&amp;" name="import" enctype="multipart/form-data">
  <input type="hidden"   name="m" value="{{$m}}" />
  <input type="hidden"   name="{{$actionType}}" value="{{$action}}" />
  <input type="hidden"   name="MAX_FILE_SIZE" value="4096000" />
  <input type="file"     name="import" />
  <input type="checkbox" name="dryrun" value="1" checked="checked" />
  <label for="dryrun">Essai à blanc</label>
  <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
</form>

{{if $results|@count}}
<table class="tbl">
  <tr>
    <th class="title" colspan="12">{{$results|@count}} utilisateurs trouvés</th>
  </tr>
  <tr>
    <th>Etat</th>
    <th>{{mb_label class=CMediusers field=_user_last_name }}</th>
    <th>{{mb_label class=CMediusers field=_user_first_name}}</th>
    <th>{{mb_label class=CMediusers field=_user_username  }}</th>
    <th>{{mb_label class=CMediusers field=_user_password  }}</th>
    <th>{{mb_label class=CMediusers field=_user_type      }}</th>
    <th>{{mb_label class=CMediusers field=function_id     }}</th>
    <th>{{mb_label class=CMediusers field=adeli           }}</th>
    <th>{{mb_label class=CMediusers field=rpps            }}</th>
    <th>
      {{mb_label class=CMediusers field=_profile_id     }} 
      {{if array_key_exists("profil_name", $unfound)}}      
        <br/>
        {{$unfound.profil_name|@count}} non trouvé(s)
      {{/if}}
    </th>
    <th>
      {{mb_label class=CMediusers field=spec_cpam_id    }}
      {{if array_key_exists("spec_cpam_code", $unfound)}}      
        <br/>
        {{$unfound.spec_cpam_code|@count}} non trouvée(s)
      {{/if}}
    </th>
    <th>
      {{mb_label class=CMediusers field=discipline_id   }}
      {{if array_key_exists("discipline_name", $unfound)}}      
        <br/>
        {{$unfound.discipline_name|@count}} non trouvée(s)
      {{/if}}
    </th>
  </tr>
  {{foreach from=$results item=_user}}
  <tr>
    {{if $_user.error}}
    <td class="text warning">
      {{$_user.error}}
    </td>
    {{elseif $dryrun}}
    <td class="">
      Essai
    </td>
    {{else}}
    <td class="text ok">
      OK
    </td>
    {{/if}}
    <td class="text">{{$_user.lastname}}       </td>
    <td class="text">{{$_user.firstname}}      </td>
    <td class="text">{{$_user.username}}       </td>
    <td class="text">{{$_user.password}}       </td>
    <td class="text">{{$_user.type}}           </td>
    <td class="text">{{$_user.function_name}}  </td>
    <td class="text">{{$_user.adeli}}          </td>
    <td class="text">{{$_user.rpps}}          </td>
    <td class="text {{if array_key_exists($_user.profil_name    , $unfound.profil_name    )}}warning{{/if}}">{{$_user.profil_name    }}</td>
    <td class="text {{if array_key_exists($_user.spec_cpam_code , $unfound.spec_cpam_code )}}warning{{/if}}">{{$_user.spec_cpam_code }}</td>
    <td class="text {{if array_key_exists($_user.discipline_name, $unfound.discipline_name)}}warning{{/if}}">{{$_user.discipline_name}}</td>
  </tr>
  {{/foreach}}
</table>
{{/if}}

