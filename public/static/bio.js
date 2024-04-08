/* ===================================================
 *  jquery-sortable.js v0.9.13
 *  http://johnny.github.com/jquery-sortable/
 * ===================================================
 *  Copyright (c) 2012 Jonas von Andrian
 *  All rights reserved.
 *
 *  Redistribution and use in source and binary forms, with or without
 *  modification, are permitted provided that the following conditions are met:
 *  * Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 *  * Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *  * The name of the author may not be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 *
 *  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 *  ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 *  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 *  DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
 *  DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 *  (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 *  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 *  ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 *  (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 *  SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * ========================================================== */


!function ( $, window, pluginName, undefined){
  var containerDefaults = {
    // If true, items can be dragged from this container
    drag: true,
    // If true, items can be droped onto this container
    drop: true,
    // Exclude items from being draggable, if the
    // selector matches the item
    exclude: "",
    // If true, search for nested containers within an item.If you nest containers,
    // either the original selector with which you call the plugin must only match the top containers,
    // or you need to specify a group (see the bootstrap nav example)
    nested: true,
    // If true, the items are assumed to be arranged vertically
    vertical: true
  }, // end container defaults
  groupDefaults = {
    // This is executed after the placeholder has been moved.
    // $closestItemOrContainer contains the closest item, the placeholder
    // has been put at or the closest empty Container, the placeholder has
    // been appended to.
    afterMove: function ($placeholder, container, $closestItemOrContainer) {
    },
    // The exact css path between the container and its items, e.g. "> tbody"
    containerPath: "",
    // The css selector of the containers
    containerSelector: "ol, ul",
    // Distance the mouse has to travel to start dragging
    distance: 0,
    // Time in milliseconds after mousedown until dragging should start.
    // This option can be used to prevent unwanted drags when clicking on an element.
    delay: 0,
    // The css selector of the drag handle
    handle: "",
    // The exact css path between the item and its subcontainers.
    // It should only match the immediate items of a container.
    // No item of a subcontainer should be matched. E.g. for ol>div>li the itemPath is "> div"
    itemPath: "",
    // The css selector of the items
    itemSelector: "li",
    // The class given to "body" while an item is being dragged
    bodyClass: "dragging",
    // The class giving to an item while being dragged
    draggedClass: "dragged",
    // Check if the dragged item may be inside the container.
    // Use with care, since the search for a valid container entails a depth first search
    // and may be quite expensive.
    isValidTarget: function ($item, container) {
      return true
    },
    // Executed before onDrop if placeholder is detached.
    // This happens if pullPlaceholder is set to false and the drop occurs outside a container.
    onCancel: function ($item, container, _super, event) {
    },
    // Executed at the beginning of a mouse move event.
    // The Placeholder has not been moved yet.
    onDrag: function ($item, position, _super, event) {
      $item.css(position)
    },
    // Called after the drag has been started,
    // that is the mouse button is being held down and
    // the mouse is moving.
    // The container is the closest initialized container.
    // Therefore it might not be the container, that actually contains the item.
    onDragStart: function ($item, container, _super, event) {
      $item.css({
        height: $item.outerHeight(),
        width: $item.outerWidth()
      })
      $item.addClass(container.group.options.draggedClass)
      $("body").addClass(container.group.options.bodyClass)
    },
    // Called when the mouse button is being released
    onDrop: function ($item, container, _super, event) {
      $item.removeClass(container.group.options.draggedClass).removeAttr("style")
      $("body").removeClass(container.group.options.bodyClass)
    },
    // Called on mousedown. If falsy value is returned, the dragging will not start.
    // Ignore if element clicked is input, select or textarea
    onMousedown: function ($item, _super, event) {
      if (!event.target.nodeName.match(/^(input|select|textarea)$/i)) {
        event.preventDefault()
        return true
      }
    },
    // The class of the placeholder (must match placeholder option markup)
    placeholderClass: "placeholder",
    // Template for the placeholder. Can be any valid jQuery input
    // e.g. a string, a DOM element.
    // The placeholder must have the class "placeholder"
    placeholder: '<li class="placeholder"></li>',
    // If true, the position of the placeholder is calculated on every mousemove.
    // If false, it is only calculated when the mouse is above a container.
    pullPlaceholder: true,
    // Specifies serialization of the container group.
    // The pair $parent/$children is either container/items or item/subcontainers.
    serialize: function ($parent, $children, parentIsContainer) {
      var result = $.extend({}, $parent.data())

      if(parentIsContainer)
        return [$children]
      else if ($children[0]){
        result.children = $children
      }

      delete result.subContainers
      delete result.sortable

      return result
    },
    // Set tolerance while dragging. Positive values decrease sensitivity,
    // negative values increase it.
    tolerance: 0
  }, // end group defaults
  containerGroups = {},
  groupCounter = 0,
  emptyBox = {
    left: 0,
    top: 0,
    bottom: 0,
    right:0
  },
  eventNames = {
    start: "touchstart.sortable mousedown.sortable",
    drop: "touchend.sortable touchcancel.sortable mouseup.sortable",
    drag: "touchmove.sortable mousemove.sortable",
    scroll: "scroll.sortable"
  },
  subContainerKey = "subContainers"

  /*
   * a is Array [left, right, top, bottom]
   * b is array [left, top]
   */
  function d(a,b) {
    var x = Math.max(0, a[0] - b[0], b[0] - a[1]),
    y = Math.max(0, a[2] - b[1], b[1] - a[3])
    return x+y;
  }

  function setDimensions(array, dimensions, tolerance, useOffset) {
    var i = array.length,
    offsetMethod = useOffset ? "offset" : "position"
    tolerance = tolerance || 0

    while(i--){
      var el = array[i].el ? array[i].el : $(array[i]),
      // use fitting method
      pos = el[offsetMethod]()
      pos.left += parseInt(el.css('margin-left'), 10)
      pos.top += parseInt(el.css('margin-top'),10)
      dimensions[i] = [
        pos.left - tolerance,
        pos.left + el.outerWidth() + tolerance,
        pos.top - tolerance,
        pos.top + el.outerHeight() + tolerance
      ]
    }
  }

  function getRelativePosition(pointer, element) {
    var offset = element.offset()
    return {
      left: pointer.left - offset.left,
      top: pointer.top - offset.top
    }
  }

  function sortByDistanceDesc(dimensions, pointer, lastPointer) {
    pointer = [pointer.left, pointer.top]
    lastPointer = lastPointer && [lastPointer.left, lastPointer.top]

    var dim,
    i = dimensions.length,
    distances = []

    while(i--){
      dim = dimensions[i]
      distances[i] = [i,d(dim,pointer), lastPointer && d(dim, lastPointer)]
    }
    distances = distances.sort(function  (a,b) {
      return b[1] - a[1] || b[2] - a[2] || b[0] - a[0]
    })

    // last entry is the closest
    return distances
  }

  function ContainerGroup(options) {
    this.options = $.extend({}, groupDefaults, options)
    this.containers = []

    if(!this.options.rootGroup){
      this.scrollProxy = $.proxy(this.scroll, this)
      this.dragProxy = $.proxy(this.drag, this)
      this.dropProxy = $.proxy(this.drop, this)
      this.placeholder = $(this.options.placeholder)

      if(!options.isValidTarget)
        this.options.isValidTarget = undefined
    }
  }

  ContainerGroup.get = function  (options) {
    if(!containerGroups[options.group]) {
      if(options.group === undefined)
        options.group = groupCounter ++

      containerGroups[options.group] = new ContainerGroup(options)
    }

    return containerGroups[options.group]
  }

  ContainerGroup.prototype = {
    dragInit: function  (e, itemContainer) {
      this.$document = $(itemContainer.el[0].ownerDocument)

      // get item to drag
      var closestItem = $(e.target).closest(this.options.itemSelector);
      // using the length of this item, prevents the plugin from being started if there is no handle being clicked on.
      // this may also be helpful in instantiating multidrag.
      if (closestItem.length) {
        this.item = closestItem;
        this.itemContainer = itemContainer;
        if (this.item.is(this.options.exclude) || !this.options.onMousedown(this.item, groupDefaults.onMousedown, e)) {
            return;
        }
        this.setPointer(e);
        this.toggleListeners('on');
        this.setupDelayTimer();
        this.dragInitDone = true;
      }
    },
    drag: function  (e) {
      if(!this.dragging){
        if(!this.distanceMet(e) || !this.delayMet)
          return

        this.options.onDragStart(this.item, this.itemContainer, groupDefaults.onDragStart, e)
        this.item.before(this.placeholder)
        this.dragging = true
      }

      this.setPointer(e)
      // place item under the cursor
      this.options.onDrag(this.item,
                          getRelativePosition(this.pointer, this.item.offsetParent()),
                          groupDefaults.onDrag,
                          e)

      var p = this.getPointer(e),
      box = this.sameResultBox,
      t = this.options.tolerance

      if(!box || box.top - t > p.top || box.bottom + t < p.top || box.left - t > p.left || box.right + t < p.left)
        if(!this.searchValidTarget()){
          this.placeholder.detach()
          this.lastAppendedItem = undefined
        }
    },
    drop: function  (e) {
      this.toggleListeners('off')

      this.dragInitDone = false

      if(this.dragging){
        // processing Drop, check if placeholder is detached
        if(this.placeholder.closest("html")[0]){
          this.placeholder.before(this.item).detach()
        } else {
          this.options.onCancel(this.item, this.itemContainer, groupDefaults.onCancel, e)
        }
        this.options.onDrop(this.item, this.getContainer(this.item), groupDefaults.onDrop, e)

        // cleanup
        this.clearDimensions()
        this.clearOffsetParent()
        this.lastAppendedItem = this.sameResultBox = undefined
        this.dragging = false
      }
    },
    searchValidTarget: function  (pointer, lastPointer) {
      if(!pointer){
        pointer = this.relativePointer || this.pointer
        lastPointer = this.lastRelativePointer || this.lastPointer
      }

      var distances = sortByDistanceDesc(this.getContainerDimensions(),
                                         pointer,
                                         lastPointer),
      i = distances.length

      while(i--){
        var index = distances[i][0],
        distance = distances[i][1]

        if(!distance || this.options.pullPlaceholder){
          var container = this.containers[index]
          if(!container.disabled){
            if(!this.$getOffsetParent()){
              var offsetParent = container.getItemOffsetParent()
              pointer = getRelativePosition(pointer, offsetParent)
              lastPointer = getRelativePosition(lastPointer, offsetParent)
            }
            if(container.searchValidTarget(pointer, lastPointer))
              return true
          }
        }
      }
      if(this.sameResultBox)
        this.sameResultBox = undefined
    },
    movePlaceholder: function  (container, item, method, sameResultBox) {
      var lastAppendedItem = this.lastAppendedItem
      if(!sameResultBox && lastAppendedItem && lastAppendedItem[0] === item[0])
        return;

      item[method](this.placeholder)
      this.lastAppendedItem = item
      this.sameResultBox = sameResultBox
      this.options.afterMove(this.placeholder, container, item)
    },
    getContainerDimensions: function  () {
      if(!this.containerDimensions)
        setDimensions(this.containers, this.containerDimensions = [], this.options.tolerance, !this.$getOffsetParent())
      return this.containerDimensions
    },
    getContainer: function  (element) {
      return element.closest(this.options.containerSelector).data(pluginName)
    },
    $getOffsetParent: function  () {
      if(this.offsetParent === undefined){
        var i = this.containers.length - 1,
        offsetParent = this.containers[i].getItemOffsetParent()

        if(!this.options.rootGroup){
          while(i--){
            if(offsetParent[0] != this.containers[i].getItemOffsetParent()[0]){
              // If every container has the same offset parent,
              // use position() which is relative to this parent,
              // otherwise use offset()
              // compare #setDimensions
              offsetParent = false
              break;
            }
          }
        }

        this.offsetParent = offsetParent
      }
      return this.offsetParent
    },
    setPointer: function (e) {
      var pointer = this.getPointer(e)

      if(this.$getOffsetParent()){
        var relativePointer = getRelativePosition(pointer, this.$getOffsetParent())
        this.lastRelativePointer = this.relativePointer
        this.relativePointer = relativePointer
      }

      this.lastPointer = this.pointer
      this.pointer = pointer
    },
    distanceMet: function (e) {
      var currentPointer = this.getPointer(e)
      return (Math.max(
        Math.abs(this.pointer.left - currentPointer.left),
        Math.abs(this.pointer.top - currentPointer.top)
      ) >= this.options.distance)
    },
    getPointer: function(e) {
      var o = e.originalEvent || e.originalEvent.touches && e.originalEvent.touches[0]
      return {
        left: e.pageX || o.pageX,
        top: e.pageY || o.pageY
      }
    },
    setupDelayTimer: function () {
      var that = this
      this.delayMet = !this.options.delay

      // init delay timer if needed
      if (!this.delayMet) {
        clearTimeout(this._mouseDelayTimer);
        this._mouseDelayTimer = setTimeout(function() {
          that.delayMet = true
        }, this.options.delay)
      }
    },
    scroll: function  (e) {
      this.clearDimensions()
      this.clearOffsetParent() // TODO is this needed?
    },
    toggleListeners: function (method) {
      var that = this,
      events = ['drag','drop','scroll']

      $.each(events,function  (i,event) {
        that.$document[method](eventNames[event], that[event + 'Proxy'])
      })
    },
    clearOffsetParent: function () {
      this.offsetParent = undefined
    },
    // Recursively clear container and item dimensions
    clearDimensions: function  () {
      this.traverse(function(object){
        object._clearDimensions()
      })
    },
    traverse: function(callback) {
      callback(this)
      var i = this.containers.length
      while(i--){
        this.containers[i].traverse(callback)
      }
    },
    _clearDimensions: function(){
      this.containerDimensions = undefined
    },
    _destroy: function () {
      containerGroups[this.options.group] = undefined
    }
  }

  function Container(element, options) {
    this.el = element
    this.options = $.extend( {}, containerDefaults, options)

    this.group = ContainerGroup.get(this.options)
    this.rootGroup = this.options.rootGroup || this.group
    this.handle = this.rootGroup.options.handle || this.rootGroup.options.itemSelector

    var itemPath = this.rootGroup.options.itemPath
    this.target = itemPath ? this.el.find(itemPath) : this.el

    this.target.on(eventNames.start, this.handle, $.proxy(this.dragInit, this))

    if(this.options.drop)
      this.group.containers.push(this)
  }

  Container.prototype = {
    dragInit: function  (e) {
      var rootGroup = this.rootGroup

      if( !this.disabled &&
          !rootGroup.dragInitDone &&
          this.options.drag &&
          this.isValidDrag(e)) {
        rootGroup.dragInit(e, this)
      }
    },
    isValidDrag: function(e) {
      return e.which == 1 ||
        e.type == "touchstart" && e.originalEvent.touches.length == 1
    },
    searchValidTarget: function  (pointer, lastPointer) {
      var distances = sortByDistanceDesc(this.getItemDimensions(),
                                         pointer,
                                         lastPointer),
      i = distances.length,
      rootGroup = this.rootGroup,
      validTarget = !rootGroup.options.isValidTarget ||
        rootGroup.options.isValidTarget(rootGroup.item, this)

      if(!i && validTarget){
        rootGroup.movePlaceholder(this, this.target, "append")
        return true
      } else
        while(i--){
          var index = distances[i][0],
          distance = distances[i][1]
          if(!distance && this.hasChildGroup(index)){
            var found = this.getContainerGroup(index).searchValidTarget(pointer, lastPointer)
            if(found)
              return true
          }
          else if(validTarget){
            this.movePlaceholder(index, pointer)
            return true
          }
        }
    },
    movePlaceholder: function  (index, pointer) {
      var item = $(this.items[index]),
      dim = this.itemDimensions[index],
      method = "after",
      width = item.outerWidth(),
      height = item.outerHeight(),
      offset = item.offset(),
      sameResultBox = {
        left: offset.left,
        right: offset.left + width,
        top: offset.top,
        bottom: offset.top + height
      }
      if(this.options.vertical){
        var yCenter = (dim[2] + dim[3]) / 2,
        inUpperHalf = pointer.top <= yCenter
        if(inUpperHalf){
          method = "before"
          sameResultBox.bottom -= height / 2
        } else
          sameResultBox.top += height / 2
      } else {
        var xCenter = (dim[0] + dim[1]) / 2,
        inLeftHalf = pointer.left <= xCenter
        if(inLeftHalf){
          method = "before"
          sameResultBox.right -= width / 2
        } else
          sameResultBox.left += width / 2
      }
      if(this.hasChildGroup(index))
        sameResultBox = emptyBox
      this.rootGroup.movePlaceholder(this, item, method, sameResultBox)
    },
    getItemDimensions: function  () {
      if(!this.itemDimensions){
        this.items = this.$getChildren(this.el, "item").filter(
          ":not(." + this.group.options.placeholderClass + ", ." + this.group.options.draggedClass + ")"
        ).get()
        setDimensions(this.items, this.itemDimensions = [], this.options.tolerance)
      }
      return this.itemDimensions
    },
    getItemOffsetParent: function  () {
      var offsetParent,
      el = this.el
      // Since el might be empty we have to check el itself and
      // can not do something like el.children().first().offsetParent()
      if(el.css("position") === "relative" || el.css("position") === "absolute"  || el.css("position") === "fixed")
        offsetParent = el
      else
        offsetParent = el.offsetParent()
      return offsetParent
    },
    hasChildGroup: function (index) {
      return this.options.nested && this.getContainerGroup(index)
    },
    getContainerGroup: function  (index) {
      var childGroup = $.data(this.items[index], subContainerKey)
      if( childGroup === undefined){
        var childContainers = this.$getChildren(this.items[index], "container")
        childGroup = false

        if(childContainers[0]){
          var options = $.extend({}, this.options, {
            rootGroup: this.rootGroup,
            group: groupCounter ++
          })
          childGroup = childContainers[pluginName](options).data(pluginName).group
        }
        $.data(this.items[index], subContainerKey, childGroup)
      }
      return childGroup
    },
    $getChildren: function (parent, type) {
      var options = this.rootGroup.options,
      path = options[type + "Path"],
      selector = options[type + "Selector"]

      parent = $(parent)
      if(path)
        parent = parent.find(path)

      return parent.children(selector)
    },
    _serialize: function (parent, isContainer) {
      var that = this,
      childType = isContainer ? "item" : "container",

      children = this.$getChildren(parent, childType).not(this.options.exclude).map(function () {
        return that._serialize($(this), !isContainer)
      }).get()

      return this.rootGroup.options.serialize(parent, children, isContainer)
    },
    traverse: function(callback) {
      $.each(this.items || [], function(item){
        var group = $.data(this, subContainerKey)
        if(group)
          group.traverse(callback)
      });

      callback(this)
    },
    _clearDimensions: function  () {
      this.itemDimensions = undefined
    },
    _destroy: function() {
      var that = this;

      this.target.off(eventNames.start, this.handle);
      this.el.removeData(pluginName)

      if(this.options.drop)
        this.group.containers = $.grep(this.group.containers, function(val){
          return val != that
        })

      $.each(this.items || [], function(){
        $.removeData(this, subContainerKey)
      })
    }
  }

  var API = {
    enable: function() {
      this.traverse(function(object){
        object.disabled = false
      })
    },
    disable: function (){
      this.traverse(function(object){
        object.disabled = true
      })
    },
    serialize: function () {
      return this._serialize(this.el, true)
    },
    refresh: function() {
      this.traverse(function(object){
        object._clearDimensions()
      })
    },
    destroy: function () {
      this.traverse(function(object){
        object._destroy();
      })
    }
  }

  $.extend(Container.prototype, API)

  /**
   * jQuery API
   *
   * Parameters are
   *   either options on init
   *   or a method name followed by arguments to pass to the method
   */
  $.fn[pluginName] = function(methodOrOptions) {
    var args = Array.prototype.slice.call(arguments, 1)

    return this.map(function(){
      var $t = $(this),
      object = $t.data(pluginName)

      if(object && API[methodOrOptions])
        return API[methodOrOptions].apply(object, args) || this
      else if(!object && (methodOrOptions === undefined ||
                          typeof methodOrOptions === "object"))
        $t.data(pluginName, new Container($t, methodOrOptions))

      return this
    });
  };

}(jQuery, window, 'sortable');

let iframesrc = $('.card-preview iframe').attr('src');

function saveBio(){
    $.ajax({
        type: 'POST',
        url: $('form').attr('action'),
        data: new FormData($('form')[0]),
        contentType: false,
        processData: false,
        dataType: 'json',
        beforeSend: function(){
            $('#loading').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
        },
        complete: function(){
            $('#loading span').remove();
        },
        success: function(response){
            $('input[name=_token]').val(response.token);
            if(response.error){
                $.notify({
                    message: response.message
                },{
                    type: 'danger',
                    placement: {
                        from: "top",
                        align: "right"
                    },
                });
            } else {
                $.notify({
                    message: response.message
                },{
                    type: 'success',
                    placement: {
                        from: "top",
                        align: "right"
                    },
                });
                if(response.html){
                  $('body').append(response.html);
                }
                $('input[type=file]').val('');
                $('.card-preview').prepend('<div class="frameloading d-flex align-items-center justify-content-center" style="position: absolute;top: 0;left: 0;height: 100%;width: 100%;background: rgba(0,0,0,0.4);z-index: 999;"><span class="spinner-border spinner-border-xl text-light" role="status" aria-hidden="true" style="width: 3rem; height: 3rem;"></span></div>');
                setTimeout(function(){
                  $('.card-preview .frameloading').remove();
                }, 2000);
				        $('.card-preview iframe').attr('src', iframesrc+'?token='+Date.now());
            }
        }
    });
}

$(document).ready(function(){

	$(document).on('change', '#generator input:not(.ignore),#generator textarea:not(.ignore),#generator select:not(.ignore)', function(){
		saveBio();
	});

	$(document).on('submit', 'form', function(e){
    e.preventDefault();
		saveBio();
	});

	$('[data-trigger=switcher]').click(function(e){
		e.preventDefault();
		if($(this).hasClass('active')) return false;
		$('.switcher').fadeOut('fast');
		$($(this).attr('href')).show();
		$(this).parents('.nav').find('a').removeClass('active');
		$(this).addClass('active');
	});

	$('[data-trigger=bgtype]').click(function(){
		if($(this).hasClass('active')) return false;
		$('.bgtype').fadeOut('fast').removeClass('show');
		$($(this).attr('href')).addClass('show');
		$('[data-trigger=bgtype]').removeClass('active');
		$(this).addClass('active');
	});

	var inplatforms = [];
	$('[data-trigger=addsocial]').click(function(e){
		e.preventDefault();
		let platform = $(this).parents('.card').find('select[name=platform]').val();
		let link = $(this).parents('.card').find('input[name=socialink]').val();
		let regex = /(?:https?):\/\/(\w+:?\w*)?(\S+)(:\d+)?(\/|\/([\w#!:.?+=&%!\-\/]))?/;

		if(link.length < 5 || !regex.test(link)){
			$.notify({
			message: $(this).data('error')
			},{
				type: 'danger',
				placement: {
					from: "top",
					align: "right"
				},
			});
			return false;
		}

		if(inplatforms.includes(platform)){
			$.notify({
			  message: $(this).data('error-alt')
			},{
				type: 'danger',
				placement: {
					from: "top",
					align: "right"
				},
			});
			return false;
		}

		inplatforms.push(platform);
    let icon = decodeURIComponent($(this).parents('.card').find('select[name=platform]').find(':selected').data('icon').replace(/\+/g, ' '));
		let html =  '<div class="border rounded p-2 mb-3 socialsortable">'+
                    '<div class="mb-3 d-flex align-items-center">'+
                        '<i class="fs-4 fa fa-align-justify handle ms-1" data-bs-toggle="tooltip"></i>'+
                        '<div class="ms-auto d-flex align-items-center">'+
                            '<a class="ms-auto fs-6 pe-2" data-trigger="deletesocial" href=""><i class="fa fa-times text-dark fs-4" data-bs-toggle="tooltip"></i></a>'+
                        '</div>'+
                    '</div>'+
                    '<div class="input-group">'+
                      '<div class="input-group-text bg-white">'+icon+'</div>'+
                      '<input type="text" class="form-control p-2" name="social['+platform+']" placeholder="https://" value="'+link+'">'+
                    '</div>'+
                '</div>';

		$("#sociallinksholder").removeClass('d-none').append(html);
		saveBio();
	});

	$(document).on('click', '[data-trigger=removesocial]', function(e){
		e.preventDefault();
		$(this).parents('.input-group').fadeOut('medium', function(){
			$(this).remove();
			saveBio();
		})
	});
	$('[data-trigger=bgtype]').click(function(){
		$('[data-trigger=bgtype]').removeClass('border-secondary');
		let val = $(this).attr('href').replace('#', '');
		$('input[name=mode]').val(val);
		$('input[name=theme]').val('');
		$('#singlecolor,#gradient,#image').removeAttr('style');
		$(this).addClass('border-secondary');
		saveBio();
	});

	$('[data-trigger=choosefont]').on('click', function(){
		$('[data-trigger=choosefont]').removeClass('border-secondary');
		$(this).addClass('border-secondary');
	});

	$('[data-trigger=chooselayout]').on('click', function(){
		$('[data-trigger=chooselayout]').removeClass('border-secondary');
		$(this).addClass('border-secondary');
		if($(this).data('value') == "layout2" || $(this).data('value') == "layout3"){
			$('#layoutbanner').addClass('show');
		}else{
			$('#layoutbanner').removeClass('show');
		}
	});

	$('#linkcontent').sortable({
		containerSelector: "#linkcontent",
		handle: '.handle',
		itemSelector: '.sortable',
		placeholder: '<div class="card p-4 bg-secondary shadow-none border"></div>',
		onMousedown: function ($item, _super, event) {
			if (!event.target.nodeName.match(/^(input|select|textarea)$/i)) {
				event.preventDefault()
				return true
			}
		},
		onDrop: function($item, container, _super, event) {
			$item.removeClass(container.group.options.draggedClass).removeAttr("style")
			$("body").removeClass(container.group.options.bodyClass)
			saveBio();
		}
	});

	$('[data-trigger=insertcontent]').click(function(e){
		e.preventDefault();
		let callback = 'fn'+$(this).data('type');
		$('.alt-error').remove();
		if(callback !== undefined){
			let response = window[callback]($(this));
			if(response === false) return;
			$("#contentModal div").removeClass('show');
			$("#options").addClass('show');
			$("#contentModal .btn-close").click();
      $('[data-toggle=select]').select2();
      $('.widget input[data-binary=true]').each(function(){
        $(this).before('<input type="hidden" value="0" name="'+ $(this).attr('name')+'">');
      });
			saveBio();
		}
	});
	$(document).on('click','[data-trigger=removeCard]', function(e){
		e.preventDefault();
		let id = $(this).parents('.widget').data('id');
		$('a[data-trigger=confirmremove]').data('id', id);
	});

	$(document).on('click','[data-trigger=confirmremove]', function(e){
		e.preventDefault();
		let id = $(this).data('id');
		$('[data-id='+id+']').remove();
		$("#preview").find('#'+id).parent('.item').remove();
		$("#removecard .btn-close").click();
		saveBio();
	});

	$("#dividercolor").spectrum({
		color: "#000000",
		showInput: true,
		preferredFormat: "hex"
	});

	$('#avatar').change(function(){
		var files = $(this).prop('files');

		for (var i = 0, f; f = files[i]; i++) {

			if (!["image/jpeg", "image/jpg", "image/png"].includes(f.type) || f.size > 500*1024) {
			$.notify({
				message: $('#avatar').data('error')
			},{
				type: 'danger',
				placement: {
					from: "top",
					align: "right"
				},
			});
			continue;
			}
			var reader = new FileReader();

			reader.onload = (function() {
			return function(e) {
				$('#useravatar').attr('src', e.target.result);
			}
			})(f);

			reader.readAsDataURL(f);
		}
	});

	$("[data-trigger=uploadavatar]").click(function(e){
		e.preventDefault();
		$("#avatar").click();
	});

	$('#bgimage').change(function(){
		var files = $(this).prop('files');

		for (var i = 0, f; f = files[i]; i++) {

			if (!["image/jpeg", "image/jpg", "image/png"].includes(f.type) || f.size > 1024*1024) {
				$.notify({
				message: $('#bgimage').data('error')
				},{
					type: 'danger',
					placement: {
						from: "top",
						align: "right"
					},
				});
				continue;
			}
		}
	});

	$("[data-trigger=color]").each(function(){
		var bg = '#000000';
		if($(this).data('default')) bg = $(this).data('default');
		$(this).spectrum({
			color: bg,
			showInput: true,
			preferredFormat: "hex",
		});

	});

	$("[data-trigger=changetheme]").click(function(){
		saveBio();
	});
  $('[data-trigger=livesearch]').keyup(function(){

		let query = $(this).val();

		if(query.length < 3) return $('#biowidgets .item').removeClass('d-none');

		$('#biowidgets .item').each(function(){
			if($(this).text().toLowerCase().includes(query.toLowerCase())) {
				$(this).removeClass('d-none');
			}else{
				$(this).addClass('d-none');
			}
		});
  });
  $('[data-toggle=biodatepicker]').each(function(){
      let el = $(this);
      el.daterangepicker({
        minDate: moment(),
        singleDatePicker: true,
        showDropdowns: true,
        autoApply: true,
        autoUpdateInput: false,
        timePicker: true,
        locale: {
          format: 'YYYY-MM-DD HH:mm'
        }
      }, function(s){
        el.val(s.format('YYYY-MM-DD HH:mm'));
        saveBio();
      });
    });
    $(document).on('click','[data-trigger=deletesocial]',function(e){
      e.preventDefault();
      let t = $(this);
      $(this).parents('.socialsortable').slideUp('slow',function(){
          $(this).find('input[type=text]').val('');
          t.parents('.socialsortable').remove();
          saveBio();
      });
      return false;
  });
  $('#sociallinksholder').sortable({
		containerSelector: "#sociallinksholder",
		handle: '.handle',
		itemSelector: '.socialsortable',
		placeholder: '<div class="card p-4 bg-secondary shadow-none border"></div>',
		onMousedown: function ($item, _super, event) {
			if (!event.target.nodeName.match(/^(input|select|textarea)$/i)) {
				event.preventDefault()
				return true
			}
		},
		onDrop: function($item, container, _super, event) {
			$item.removeClass(container.group.options.draggedClass).removeAttr("style")
			$("body").removeClass(container.group.options.bodyClass)
			saveBio();
		}
	});
});

function setColor(element, color, e){
  $('input[name=themeid]').val('');
	e.val(color.toHexString());
}

function customTheme(classname, buttoncolor, buttontextcolor, textcolor){

  $('input[name=themeid]').val('');
	$('input[name=theme]').val(classname);
	$('input[name=mode]').val('custom');

	$("#buttontextcolor").val(buttontextcolor);
	$("#buttontextcolor").spectrum({
		color: buttontextcolor,
		showInput: true,
		preferredFormat: "hex",
		move: function (color) { setColor("#preview .btn-custom", color, $(this)); },
		hide: function (color) { setColor("#preview .btn-custom", color, $(this)); saveBio()}
	});
	$("#buttoncolor").val(buttoncolor);
	$("#buttoncolor").spectrum({
		color: buttoncolor,
		showInput: true,
		preferredFormat: "hex",
		move: function (color) { setColor("#preview .btn-custom", color, $(this)); },
		hide: function (color) { setColor("#preview .btn-custom", color, $(this));  saveBio()}
	});
	$("#textcolor").val(textcolor);
	$("#textcolor").spectrum({
		color: textcolor,
		showInput: true,
		preferredFormat: "hex",
		move: function (color) { setColor("#preview, #preview h3 > span, #preview p", color, $(this)); },
		hide: function (color) { setColor("#preview, #preview h3 > span  #preview p", color, $(this)); saveBio()}
	});
}

function changeTheme(bg, bgst, bgsp, buttoncolor, buttontextcolor, textcolor, bgtype='single', buttonstyle = 'rectangle', gradientangle = '-45', shadow = false, shadowcolor = '#000', themeid = false){
  $('input[name=themeid]').val('');
  if(themeid){
    $('input[name=themeid]').val(themeid);
  }

	if(bgtype == 'gradient'){

		$('.bgtype').removeClass('show');
		$('#gradient').addClass('show');
		$('[data-trigger=bgtype]').removeClass('border-secondary');
		$('#forgradient').addClass('border-secondary');
		$('input[name=theme]').val('');
		$('input[name=mode]').val('gradient');

		$("#bgst").val(bgst);
		$("#bgst").spectrum({
			color: bgst,
			showInput: true,
			preferredFormat: "hex",
			move: function (color) { setColor("#preview .card", color, $(this)); },
			hide: function (color) { setColor("#preview .card", color, $(this)); saveBio()}
		});
		$("#bgsp").val(bgsp);
		$("#bgsp").spectrum({
			color: bgsp,
			showInput: true,
			preferredFormat: "hex",
			move: function (color) { setColor("#preview .card", color, $(this)); },
			hide: function (color) { setColor("#preview .card", color, $(this)); saveBio()}
		});

  }else if(bgtype == "image"){

		$('.bgtype').removeClass('show');
		$('#image').addClass('show');
		$('[data-trigger=bgtype]').removeClass('border-secondary');
		$('#forimage').addClass('border-secondary');
		$('input[name=theme]').val('');
		$('input[name=mode]').val('image');

	} else {
		$('.bgtype').removeClass('show');
		$('#singlecolor').addClass('show');
		$('[data-trigger=bgtype]').removeClass('border-secondary');
		$('#forsinglecolor').addClass('border-secondary');
		$('input[name=theme]').val('');
		$('input[name=mode]').val('singlecolor');
	}

	$("#bg").val(bg);
	$("#bg").spectrum({
		color: bg,
		showInput: true,
		preferredFormat: "hex",
		move: function (color) { setColor("#preview .card", color, $(this)); },
		hide: function (color) { setColor("#preview .card", color, $(this)); saveBio()}
	});
	$("#buttontextcolor").val(buttontextcolor);
	$("#buttontextcolor").spectrum({
		color: buttontextcolor,
		showInput: true,
		preferredFormat: "hex",
		move: function (color) { setColor("#preview .btn-custom", color, $(this)); },
		hide: function (color) { setColor("#preview .btn-custom", color, $(this)); saveBio()}
	});
	$("#buttoncolor").val(buttoncolor);
	$("#buttoncolor").spectrum({
		color: buttoncolor,
		showInput: true,
		preferredFormat: "hex",
		move: function (color) { setColor("#preview .btn-custom", color, $(this)); },
		hide: function (color) { setColor("#preview .btn-custom", color, $(this)); saveBio()}
	});
	$("#textcolor").val(textcolor);
	$("#textcolor").spectrum({
		color: textcolor,
		showInput: true,
		preferredFormat: "hex",
		move: function (color) { setColor("#preview, #preview h3 > span, #preview p", color, $(this)); },
		hide: function (color) { setColor("#preview, #preview h3 > span  #preview p", color, $(this)); saveBio()}
	});
  if(shadow){
    $('#shadow').val(shadow);
  }
  if(buttonstyle){
    $('#buttonstyle').val(buttonstyle);
  }
  if(shadowcolor){
    $("#shadowcolor").val(shadowcolor);
    $("#shadowcolor").spectrum({
      color: shadowcolor,
      showInput: true,
      preferredFormat: "hex",
      move: function (color) { setColor("#preview, #preview h3 > span, #preview p", color, $(this)); },
      hide: function (color) { setColor("#preview, #preview h3 > span  #preview p", color, $(this)); saveBio()}
    });
  }

}

function bioupdate(){
  for(bio in biodata){
      let callback = 'fn'+biodata[bio]['type'];
      window[callback]($('[data-type='+biodata[bio]['type']+']'), biodata[bio], bio);
  }
}

function slug(str) {
  	str = encodeURIComponent(str);
  	str = str.replace(/^\s+|\s+$/g, '');
  	str = str.toLowerCase();

  	var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
  	var to   = "aaaaeeeeiiiioooouuuunc------";
	for (var i=0, l=from.length ; i<l ; i++) {
		str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
	}

  	str = str.replace(/[^a-z0-9 -]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
  	return str;
}