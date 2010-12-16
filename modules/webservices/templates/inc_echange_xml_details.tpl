{{* $Id: vw_idx_echange_xml.tpl 10195 2010-09-28 15:58:38Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision: 10195 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $echange_xml->_message === null && $echange_xml->_acquittement === null}}
  <div class="small-info">{{tr}}{{$echange_xml->_class_name}}-purge-desc{{/tr}}</div>
{{else}}
<script type="text/javascript">
  Main.add(function () {
    Control.Tabs.create('tabs-contenu', true);
  });
</script>
<tr>
  <td>
    <ul id="tabs-contenu" class="control_tabs">
      <li><a href="#message">{{mb_title object=$echange_xml field="_message"}}</a></li>
      <li><a href="#ack">{{mb_title object=$echange_xml field="_acquittement"}}</a></li>
    </ul>
    
    <hr class="control_tabs" />
    
    <div id="message" style="display: none;">
      {{mb_value object=$echange_xml field="_message"}}
      <a target="blank" href="?m=webservices&a=download_echange_xml&echange_xml_guid={{$echange_xml->_guid}}&dialog=1&suppressHeaders=1&message=1" class="button modify">{{tr}}Save{{/tr}}</a>
      {{if $echange_xml->message_valide != 1 && count($doc_errors_msg) > 0}}
      <div class="big-error">
        <strong>Erreur validation schéma du message</strong> <br />
        {{$doc_errors_msg}}
      </div>
      {{/if}}
    </div>
    
    
    <div id="ack" style="display: none;">
      {{if $echange_xml->message_valide == 1 || $echange_xml->acquittement_valide == 1}}
        {{if $echange_xml->_acquittement}}
          {{mb_value object=$echange_xml field="_acquittement"}}
          <a target="blank" href="?m=webservices&a=download_echange_xml&echange_xml_guid={{$echange_xml->_guid}}&dialog=1&suppressHeaders=1&ack=1" class="button modify">{{tr}}Save{{/tr}}</a>
          <div class="big-{{if ($echange_xml->statut_acquittement == 'erreur') || 
                               ($echange_xml->statut_acquittement == 'err')}}error
                          {{elseif ($echange_xml->statut_acquittement == 'avertissement') || 
                                   ($echange_xml->statut_acquittement == 'avt')
                          }}warning
                          {{else}}info{{/if}}">
            {{foreach from=$observations item=observation}}
              <strong>Code :</strong> {{$observation.code}} <br />
              <strong>Libelle :</strong> {{$observation.libelle}} <br />
              <strong>Commentaire :</strong> {{$observation.commentaire}} <br />
            {{/foreach}}
          </div>
        {{else}}
          <div class="big-info">Aucun acquittement n'a été reçu.</div>
        {{/if}}
      {{else}}
        {{if count($doc_errors_ack) > 0}}
        <div class="big-error">
          <strong>Erreur validation schéma de l'acquittement</strong> <br />
          {{$doc_errors_ack}}
        </div>
        {{else}}
        <div class="big-info">Aucun acquittement n'a été reçu.</div>
        {{/if}}
      {{/if}}
    </div>
  </td>
</tr> 
{{/if}}