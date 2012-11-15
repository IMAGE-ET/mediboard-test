ExClass.initPixelLayoutEditor = function(){
  // false focus event for IE
  /*if (Prototype.Browser.IE) {
    var overlays = $$(".pixel-positionning .field-input .overlay");
    overlays.observe("mousedown", function(e){
      e.up(".field-input").fire("ui:focus");
    });
    
    var inputs = $$(".pixel-positionning .field-input input, .pixel-positionning .field-input select, .pixel-positionning .field-input textarea").each(function(element){
      element.addAttribute("tabIndex", "-1");
    });
  }*/
  
  var fieldInputs = $$(".pixel-positionning .resizable");
  
  fieldInputs.each(function(f){
    // Attach events to the handles
    f.observeOnce("focus", function(event){
      var element = event.element();
      
      var drag = new Draggable(element, {
        handle: element.down(".overlayed"),
        onStart: function(draggable) {
          var element = draggable.element;
          if (element.hasClassName("field-input") ||
              element.hasClassName("subgroup") ||
              element.hasClassName("draggable-message")) {
            draggable._subgroups = draggable.element.up(".group-layout").select(".subgroup, .pixel-grid").without(element);

            draggable._subgroups.each(function(subgroup) {
              Droppables.add(subgroup, {
                onDrop: function(dragged, dropped, event) {
                  var dragSubgroup = dragged.up(".subgroup");
                  var fromGroup = !dragSubgroup;
                  var parent;

                  // Move inside the same subgroup or
                  if (dragSubgroup == dropped || dropped.hasClassName("pixel-grid") && !dragSubgroup) {
                    return;
                  }

                  var pos = {
                    left: parseInt(dragged.style.left),
                    top:  parseInt(dragged.style.top)
                  };

                  // offset from group to subgroup
                  parent = dropped;
                  while (parent && parent != dragSubgroup) {
                    var style = parent.style;
                    pos.left -= Number.getInt(style.left, 0)+1;
                    pos.top  -= Number.getInt(style.top, 0) +1;
                    parent = parent.up(".subgroup");
                  }

                  // group to subgroup
                  if (fromGroup) {
                    if (!dragged.descendantOf(dropped)) {
                      dropped.down("fieldset").insert(dragged);
                    }
                  }

                  // subgroup to other
                  else {
                    // offset from subgroup to group
                    parent = dragSubgroup;
                    while (parent) {
                      var style = parent.style;
                      pos.left += Number.getInt(style.left, 0)+1;
                      pos.top  += Number.getInt(style.top, 0) +1;
                      parent = parent.up(".subgroup");
                    }

                    dropped.insert(dragged);
                  }

                  dragged.setStyle({
                    left: pos.left+"px",
                    top:  pos.top+"px"
                  });

                  try {
                    dragged.focus();
                  } catch(e) {}
                },
                hoverclass: "dropactive"
              });
            });
          }
        },
        onEnd: function(draggable){
          var element = draggable.element;
          endDrag(element);
          element.style.zIndex = "";

          if (draggable._subgroups) {
            draggable._subgroups.each(Droppables.remove.bind(Droppables));
          }
        }
      });
      
      var initDrag = function(event) {
        if (event._stoppedByChild) {
          return;
        }

        event._stoppedByChild = true;

        if(!Object.isUndefined(Draggable._dragging[this.element]) &&
          Draggable._dragging[this.element]) return;
        if(Event.isLeftClick(event)) {
          var pointer = [Event.pointerX(event), Event.pointerY(event)];
          var pos     = this.element.cumulativeOffset();
          this.offset = [0,1].map( function(i) { return (pointer[i] - pos[i]) });
    
          Draggables.activate(this);
          //Event.stop(event); // This was removed
        }
      };
      
      var endDrag = function(box) {
        var style = box.style;
        var subgroup = box.up(".subgroup");
        
        var dims = {
          coord_top:    Number.getInt(style.top,    0),
          coord_left:   Number.getInt(style.left,   0),
          coord_width:  Number.getInt(style.width,  ""),
          coord_height: Number.getInt(style.height, "")
        };
        
        if (dims.coord_width && dims.coord_height) {
          box.removeClassName("no-size");
        }
        
        var url = new Url();
        
        // Field
        var field_id = box.get("field_id");
        if (field_id) {
          url.addParam("@class", "CExClassField");
          url.addParam("ex_class_field_id", field_id);
          url.addParam("subgroup_id", subgroup ? subgroup.get("subgroup_id") : "");
        }
        
        // Message
        var message_id = box.get("message_id");
        if (message_id) {
          url.addParam("@class", "CExClassMessage");
          url.addParam("ex_class_message_id", message_id);
        }
        
        // Subgroup
        var subgroup_id = box.get("subgroup_id");
        if (subgroup_id) {
          url.addParam("@class", "CExClassFieldSubgroup");
          url.addParam("ex_class_field_subgroup_id", subgroup_id);

          if (subgroup) {
            url.addParam("parent_class", "CExClassFieldSubgroup");
            url.addParam("parent_id", subgroup.get("subgroup_id"));
          }
          else {
            var group = box.up(".group-layout");
            url.addParam("parent_class", "CExClassFieldGroup");
            url.addParam("parent_id", group.get("group_id"));
          }
        }
        
        url.mergeParams(dims);
        url.requestUpdate(SystemMessage.id, {method: "post"});
      };
      
      Event.stopObserving(drag.handle, "mousedown", drag.eventMouseDown);
      drag.eventMouseDown = initDrag.bindAsEventListener(drag);
      Event.observe(drag.handle, "mousedown", drag.eventMouseDown);
      
      // we handle the handles...
      var handles = element.select(".handle");
      
      handles.each(function(handle){
        handle.observe("mousedown", function(e){
          if (window._draggingElement) {
            return;
          }

          var element = e.element();
          var box = element.up(".resizable");
          
          element.store("orig-x", e.clientX);
          element.store("orig-y", e.clientY);
          
          ["top", "left"].each(function(carac){
            box.store("orig-"+carac, parseInt(box.style[carac]));
          });
          
          box.store("orig-width",  parseInt(box.style.width)  || box.getWidth());
          box.store("orig-height", parseInt(box.style.height) || box.getHeight());
          
          window._draggingElement = element;
          Event.stop(e);
        });
      });
      
      document.observe("mousemove", function(e){
        if (!window._draggingElement) {
          return;
        }
        
        var dragging = window._draggingElement;
        var box     = dragging.up(".resizable");
        var offsetX = dragging.retrieve("orig-x") - e.clientX;
        var offsetY = dragging.retrieve("orig-y") - e.clientY;
        var origTop    = box.retrieve("orig-top");
        var origLeft   = box.retrieve("orig-left");
        var origWidth  = box.retrieve("orig-width");
        var origHeight = box.retrieve("orig-height");
        
        var way = dragging.get("way");
        
        switch(way) {
          case "w":
          case "nw":
            var width = (origWidth + offsetX);
            if (width > 0) {
              box.style.left  = (origLeft - offsetX) + "px";
              box.style.width = width + "px";
            }
          case "n":
            if (way !== "w") {
              var height = (origHeight + offsetY);
              if (height > 0) {
                box.style.top    = (origTop - offsetY) + "px";
                box.style.height = height + "px";
              }
            }
            return;
            
          case "sw":
            var width = (origWidth + offsetX);
            if (width > 0) {
              box.style.left  = (origLeft - offsetX) + "px";
              box.style.width = width + "px";
            }
          case "s":
            var height = (origHeight - offsetY);
            if (height > 0) {
              box.style.height = height + "px";
            }
            return;
            
          case "e":
          case "se":
          case "ne":
            var width = (origWidth - offsetX);
            if (width > 0) {
              box.style.width = width + "px";
            }
            
            if (way === "se") {
              var height = (origHeight - offsetY);
              if (height > 0) {
                box.style.height = height + "px";
              }
            }
            
            if (way === "ne") {
              var height = (origHeight + offsetY);
              if (height > 0) {
                box.style.top    = (origTop - offsetY) + "px";
                box.style.height = height + "px";
              }
            }
            return;
        }
      });
      
      document.observe("mouseup", function(e){
        if (!window._draggingElement) {
          return;
        }
          
        endDrag(window._draggingElement.up(".resizable"));
        
        window._draggingElement = null;
      });
    });
  });
};
