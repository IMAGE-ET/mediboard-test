{{*
 * $Id$
 *  
 * @category Formulaires
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_script module=forms script=exObject}}

{{assign var=object_guid value="`$ex_object->object_class`-`$ex_object->object_id`"}}

<button class="edit notext compact"
        onclick="ExObject.edit('{{$ex_object->_id}}', '{{$ex_object->_ex_class_id}}', '{{$object_guid}}')">{{tr}}Edit{{/tr}}</button>
<button class="search notext compact"
        onclick="ExObject.display('{{$ex_object->_id}}', '{{$ex_object->_ex_class_id}}', '{{$object_guid}}')">{{tr}}Display{{/tr}}</button>
<button class="history notext compact"
        onclick="ExObject.history('{{$ex_object->_id}}', '{{$ex_object->_ex_class_id}}')">{{tr}}History{{/tr}}</button>
<button class="print notext compact"
        onclick="ExObject.print('{{$ex_object->_id}}', '{{$ex_object->_ex_class_id}}', '{{$object_guid}}')">{{tr}}Print{{/tr}}</button>