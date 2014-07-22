/**
 * $Id$
 *
 * @category Drawing
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

DrawObject = {
  canvas : null,
  canvas_element : 'canvas',

  init : function(canvas) {
    DrawObject.canvas = new fabric.Canvas('canvas');
    DrawObject.canvas.freeDrawingBrush.color = "black";
    DrawObject.canvas.freeDrawingBrush.width = 3;
    DrawObject.canvas.isDrawingMode = true;

    // object selected
    DrawObject.canvas.on('object:selected', function(obj) {
      if (obj.target.type == "text") {
        $V('content_text_cv', obj.target.text, true);
        $V('color_text_cv', obj.target.fill, true);
        $V('bgcolor_text_cv', obj.target.shadow, true);
      }
    });

    /*
    // no selection
    DrawObject.canvas.on('selection:cleared', function(tata) {
      console.log('unselected');
    });
    */

    return DrawObject.canvas;

  },

  getSvgStr : function() {
    return DrawObject.canvas.toSVG();
  },

  getJsonStr : function() {
    var json = DrawObject.canvas.toDatalessJSON();
    return JSON.stringify(json);
  },

  loadDraw : function(data) {
    DrawObject.canvas.loadFromDatalessJSON(data).renderAll();
  },

  setProperty : function(name, value) {
    DrawObject.canvas.name = value;
  },

  refresh : function() {
    DrawObject.canvas.renderAll();
  },

  toggleMode : function(button) {
    DrawObject.canvas.isDrawingMode = !DrawObject.canvas.isDrawingMode;
    if (button) {
      var text = button.innerText;
    }
    return DrawObject.canvas.isDrawingMode;
  },

  addEditText : function(str, col, ctr) {
    var text = str;
    var color = col || "#000000";
    var color_2 = ctr;
    if (!text || !color) {
      return;
    }
    var active = DrawObject.canvas.getActiveObject();
    if (active && active.type == "text") {
      console.log(active);
      active.set({
        text: text,
        fill : color,
        shadow: color_2
      });
      DrawObject.refresh();
    }
    //add text
    else {
      var canvas_text = new fabric.Text(text, {});
      canvas_text.set({
        left: (DrawObject.canvas.width-canvas_text.width)/2,
        top: (DrawObject.canvas.height-canvas_text.height)/2,
        fill : col,
        shadow: color_2
      });
      DrawObject.canvas.add(canvas_text);
    }
  },

  changeDrawWidth : function(value) {
    if (value) {
      DrawObject.canvas.freeDrawingBrush.width = value;
    }

    // multiple objects
    var objects = DrawObject.canvas.getActiveGroup();
    if (objects) {
      objects.forEachObject(function(_o){
        if (_o.type == "path") {
          _o.set(
            {strokeWidth : value}
          );
        }
      });
    }

    // one object
    var active = DrawObject.canvas.getActiveObject();
    if (active && active.type == "path") {
      active.set({strokeWidth : value});
    }

    if (active || objects) {
      DrawObject.refresh();
    }
  },

  changeOpacty : function(ivalue) {
    var active = DrawObject.canvas.getActiveObject();
    if (active && ivalue) {
      active.set({opacity : ivalue/100})
    }
    DrawObject.refresh();
  },

  changeDrawColor : function(value) {
    DrawObject.canvas.freeDrawingBrush.color = value;

    // multiple objects
    var objects = DrawObject.canvas.getActiveGroup();
    if (objects) {
      objects.forEachObject(function(_o){
        if (_o.type == "path") {
          _o.set(
            {stroke : value}
          );
        }
      });
    }

    // one object
    var active = DrawObject.canvas.getActiveObject();
    if (active && active.type == "path") {

      active.set({stroke : value});
    }

    if (active || objects) {
      DrawObject.refresh();
    }
  },

  zoomInObject: function() {
    var object = DrawObject.canvas.getActiveObject();
    if (object) {
      object.scaleX = object.scaleX+ (10*object.scaleX)/100;
      object.scaleY = object.scaleY+ (10*object.scaleY)/100;
    }
    DrawObject.canvas.renderAll();
  },

  zoomOutObject: function() {
    var object = DrawObject.canvas.getActiveObject();
    if (object) {
      object.scaleX = object.scaleX - (10*object.scaleX)/100;
      object.scaleY = object.scaleY - (10*object.scaleY)/100;
    }
    DrawObject.canvas.renderAll();
  },

  sendToBack : function() {
    var activeObject = DrawObject.canvas.getActiveObject();
    if (activeObject) {
      DrawObject.canvas.sendToBack(activeObject);
    }
  },

  sendBackwards : function() {
    var activeObject = DrawObject.canvas.getActiveObject();
    if (activeObject) {
      DrawObject.canvas.sendBackwards(activeObject);
    }
  },

  bringForward  : function() {
    var activeObject = DrawObject.canvas.getActiveObject();
    if (activeObject) {
      DrawObject.canvas.bringForward(activeObject);
    }
  },

  bringToFront : function() {
    var activeObject = DrawObject.canvas.getActiveObject();
    if (activeObject) {
      DrawObject.canvas.bringToFront(activeObject);
    }
  },

  /**
   * remove an object from canvas
   *
   * @param object_to_remove
   * @param unique
   */
  removeObject : function (object_to_remove, unique) {
    if (object_to_remove) {
      DrawObject.canvas.remove(object_to_remove);
    }
    if (unique) {
      DrawObject.canvas.renderAll();
    }
  },

  clearCanvas : function () {
    if (confirm("Effacer toute la zone de dessin ?")) {
      DrawObject.canvas.clear().renderAll();
    }
  },

  undo : function() {
    var objects = DrawObject.canvas.getObjects();
    if (objects.length > 0 && objects[(objects.length)-1]) {
      DrawObject.removeObject(objects[(objects.length)-1], true);
    }
  },

  /**
   * remove the selected object
   */
  removeActiveObject : function() {
    var object = DrawObject.canvas.getActiveObject();
    if (object) {
      DrawObject.removeObject(object, true);
    }
  },

  flipXObject : function() {
    var object = DrawObject.canvas.getActiveObject();
    if (object) {
      object.flipX = !object.flipX;
      DrawObject.canvas.renderAll();
    }
  },

  flipYObject : function() {
    var object = DrawObject.canvas.getActiveObject();
    if (object) {
      object.flipY = !object.flipY;
      DrawObject.canvas.renderAll();
    }
  },

  /** IMAGES **/

  insertSVGStr : function(str) {
    var imgfjs = fabric.loadSVGFromString(str, function(objects, options) {
      objects.each(function(img) {
        DrawObject.canvas.add(img);
      });
      DrawObject.canvas.calcOffset();
      DrawObject.canvas.renderAll.bind(DrawObject.canvas);
    });
  },

  insertSVG : function(uri) {
    var group = [];
    var imgfjs = fabric.loadSVGFromURL(uri, function(objects, options) {
      var shape = fabric.util.groupSVGElements(objects, options);
      $(DrawObject.canvas_element).width = shape.getWidth() || 600;
      $(DrawObject.canvas_element).height = shape.getHeight() || 600;
      DrawObject.canvas = new fabric.Canvas('canvas', { backgroundColor: '#fff' });
      DrawObject.canvas.add(shape);
      //shape.center();
      DrawObject.canvas.renderAll();
    });
  },

  insertImg : function(uri) {
    var imgfjs = fabric.Image.fromURL(uri, function(img) {

      //if the pic is bigger than canvas we resize
      if (img.width && img.height && (img.width > DrawObject.canvas.width || img.height > DrawObject.canvas.height)) {
        var width_sup_height = img.width > img.height;
        var ratio = img.width/img.height;
        img.set({
          width: width_sup_height ? DrawObject.canvas.width : DrawObject.canvas.height*ratio,
          height: width_sup_height ? DrawObject.canvas.width/ratio : DrawObject.canvas.height
        });
      }

      // positionning
      img.set({
        left: (DrawObject.canvas.width - img.width)/2,
        top: (DrawObject.canvas.height - img.height)/2
      });

      DrawObject.canvas.add(img);
      DrawObject.canvas.renderAll.bind(DrawObject.canvas);
    });
  }
};