/**
 * Created by flavien on 16/03/15.
 */

popFile = window.popFile || function(objectClass, objectId, elementClass, elementId, sfn) {
  new Url().ViewFilePopup(objectClass, objectId, elementClass, elementId, sfn);
};