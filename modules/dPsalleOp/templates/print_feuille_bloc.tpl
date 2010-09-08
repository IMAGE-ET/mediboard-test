<table class="print">
  <tr>
    <td>
      <table width="100%" style="font-size: 110%;">
        <tr>
          <th class="title" colspan="4">
            <a href="#" onclick="window.print()">
              Feuille de Bloc
            </a>
          </th>
        </tr>
      </table>
    </td>
  </tr>
</table>

<table class="print">
  <tr>
    <td class="halfPane">
      <table width="100%" style="font-size: 100%;">
        <tr>
          <th class="category" colspan="2">Informations sur le patient</th>
        </tr>
        <tr>
          <td colspan="2">{{$patient->_view}}</td>
        </tr>
        <tr>
          <td colspan="2">
            Né{{if $patient->sexe != "m"}}e{{/if}} le {{mb_value object=$patient field=naissance}}
            ({{$patient->_age}} ans)
            - sexe {{if $patient->sexe == "m"}} masculin {{else}} féminin {{/if}}
          </td>
        </tr>
      </table>
      
      <table width="100%" style="font-size: 100%;">
        <tr>
          <th class="category" colspan="2">Intervention</th>
        </tr>

        <tr>
          <th>Date</th>
          <td class="greedyPane">
            {{$operation->_ref_plageop->date|date_format:$dPconfig.longdate}}
          </td>
        </tr>
        <tr>
          <th>Libellé</th>
          <td class="text">
            {{if $operation->libelle}}
            {{$operation->libelle}}
            {{else}}
            &mdash;
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>Côté</th>
          <td>
            {{tr}}COperation.cote.{{$operation->cote}}{{/tr}}
          </td>
        </tr>
        <tr>
          <th>Chirurgien</th>
          <td class="text">
            Dr {{$operation->_ref_chir->_view}}
          </td>
        </tr>
        
        <tr>
          <th>Anesthésiste</th>
          <td class="text">
            {{if $operation->_ref_anesth->user_id}}
            Dr {{$operation->_ref_anesth->_view}}
            {{else}}
            &mdash;
            {{/if}}
          </td>
        </tr>
        
        <tr>
          <th>Type d'anesthésie</th>
          <td class="text">
            {{if $operation->type_anesth}}
            {{$operation->_lu_type_anesth}}
            {{else}}
            &mdash;
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>Personnel</th>
          <td class="text">
            Non Disponible
          </td>
        </tr>
        <tr>
          <th>Matériel</th>
          <td class="text">
            {{if $operation->materiel}}
            {{$operation->materiel|nl2br}}
            {{else}}
            &mdash;
            {{/if}}
          </td>
        </tr>       
        <tr>
          <th>Remarques</th>
          <td class="text">
            {{if $operation->rques}}
            {{$operation->rques|nl2br}}
            {{else}}
            &mdash;
            {{/if}}
          </td>
        </tr>

      </table>
      
    </td>
    <td class="halfPane">
      <table width="100%" style="font-size: 100%;">
        <tr>
          <th class="category" colspan="2">Horaires</th>
        </tr>
        
        <tr>
          <th>{{mb_label object=$operation field=debut_prepa_preop}}</th>
          <td class="halfPane">{{mb_value object=$operation field=debut_prepa_preop}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$operation field=fin_prepa_preop}}</th>
          <td class="halfPane">{{mb_value object=$operation field=fin_prepa_preop}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$operation field=entree_salle}}</th>
          <td class="halfPane">{{mb_value object=$operation field=entree_salle}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=induction_debut}}</th>
          <td class="halfPane">{{mb_value object=$operation field=induction_debut}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=induction_fin}}</th>
          <td class="halfPane">{{mb_value object=$operation field=induction_fin}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=pose_garrot}}</th>
          <td class="halfPane">{{mb_value object=$operation field=pose_garrot}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=debut_op}}</th>
          <td class="halfPane">{{mb_value object=$operation field=debut_op}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=fin_op}}</th>
          <td class="halfPane">{{mb_value object=$operation field=fin_op}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=retrait_garrot}}</th>
          <td class="halfPane">{{mb_value object=$operation field=retrait_garrot}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=sortie_salle}}</th>
          <td class="halfPane">{{mb_value object=$operation field=sortie_salle}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=entree_reveil}}</th>
          <td class="halfPane">{{mb_value object=$operation field=entree_reveil}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=sortie_reveil}}</th>
          <td class="halfPane">{{mb_value object=$operation field=sortie_reveil}}</td>
        </tr>
      </table>    
      
      <table width="100%" style="font-size: 100%;">
        <tr>
          <th class="category" colspan="2">Durées</th>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=_presence_salle}}</th>
          <td class="halfPane">{{mb_value object=$operation field=_presence_salle}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=_duree_interv}}</th>
          <td class="halfPane">{{mb_value object=$operation field=_duree_interv}}</td>
        </tr>
      </table>      
    </td>
  </tr>
  
  <tr>
    <td colspan="2">
      <table width="100%" style="border-spacing: 0px;font-size: 100%;">
        <tr>
          <th class="category" colspan="5">Actes CCAM</th>
        </tr>
        {{assign var="styleBorder" value="border: solid #aaa 1px;"}}
        <tr>
          <th style="{{$styleBorder}}text-align:left;">Code</th>
          <th style="{{$styleBorder}}text-align:left;">Exécutant</th>
          <th style="{{$styleBorder}}text-align:left;">Activité</th>
          <th style="{{$styleBorder}}text-align:left;">Phase &mdash; Modifs.</th>
          <th style="{{$styleBorder}}text-align:left;">Association</th>
        </tr>
        {{foreach from=$operation->_ref_actes_ccam item=currActe}}
        <tr>
          <td class="text" style="{{$styleBorder}}">
            <strong>{{$currActe->code_acte}}</strong><br />
            {{$currActe->_ref_code_ccam->libelleLong}}
          </td>
          <td class="text" style="{{$styleBorder}}">{{$currActe->_ref_executant->_view}}</td>
          <td style="{{$styleBorder}}">{{$currActe->code_activite}}</td>
          <td style="{{$styleBorder}}">
            {{$currActe->code_phase}}
            {{if $currActe->modificateurs}}
            &mdash; {{$currActe->modificateurs}}
            {{/if}}
          </td>
          <td style="{{$styleBorder}}">{{$currActe->_guess_association}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>