/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./assets/src/public.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./assets/src/fancyboxModal.js":
/*!*************************************!*\
  !*** ./assets/src/fancyboxModal.js ***!
  \*************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var fancyboxModal =
/*#__PURE__*/
function () {
  function fancyboxModal(modalID, modalArgs) {
    _classCallCheck(this, fancyboxModal);

    this.modal_id = parseInt(modalID);
    this.modal_args = modalArgs ? modalArgs : {}; // @todo
    // this.defaults = {};
  }
  /**
   * Write new click count for analitics
   */


  _createClass(fancyboxModal, [{
    key: "increaseClickCount",
    value: function increaseClickCount(args) {
      $.post(args.ajax_url, {
        action: 'increase_click_count',
        nonce: args.nonce,
        modal_id: this.modal_id
      });
    }
    /**
     * Write cookie for temporary disable
     */

  }, {
    key: "writeCookieTime",
    value: function writeCookieTime(args) {
      var now = new Date().getTime(),
          time = parseFloat(this.modal_args.disable_ontime);
      if (!time || 0 >= time) return;
      args.disabled[this.modal_id] = now + oneHour * time;
      document.cookie = args.cookie + "=" + JSON.stringify(args.disabled) + "; path=/; expires=" + new Date(now + oneHour * args.expires).toUTCString();
    }
  }, {
    key: "open",
    value: function open(args) {
      var _this = this;

      var exclude = args.disabled || {};

      if (this.modal_args.disable_ontime <= 0 || !(this.modal_id in exclude) || new Date().getTime() > exclude[this.modal_id]) {
        try {
          var fancy = {
            src: '#modal-' + this.modal_id,
            type: 'inline',
            opts: {
              afterShow: function afterShow(instance, current) {
                _this.writeCookieTime();

                _this.increaseClickCount();
              }
            }
          };

          if ('script' == this.modal_args.modal_type) {
            fancy.src = this.modal_args.src;
            fancy.type = 'html';
          }

          $.fancybox.open(fancy);
        } catch (e) {
          console.error('Do you hooked up the Fancybox library?');
          console.log(e);
        }
      }
    }
  }]);

  return fancyboxModal;
}();

;

/***/ }),

/***/ "./assets/src/public.js":
/*!******************************!*\
  !*** ./assets/src/public.js ***!
  \******************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

jQuery(document).ready(function ($) {
  /**
   * Get list of disabled modals from cookie
   * @return Object from json
   */
  function getDisabledList() {
    var disabled = {};
    var o = document.cookie.match(new RegExp("(?:^|; )" + args.cookie.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, "\\$1") + "=([^;]*)"));
    var cookie = o ? decodeURIComponent(o[1]) : void 0;

    if (cookie) {
      try {
        disabled = JSON.parse(cookie);
      } catch (e) {
        console.error('Can\'t get disabled modal\'s cookie data');
        console.log(e);
      }
    }

    return disabled;
  }

  ;
  /**
   * @param  String
   * @return Bool
   */

  function isImage(src) {
    return /\.(jpe?g|png|gif|bmp|webp)$/i.test(src);
  }

  ; // set default buttons

  $.fancybox.defaults.buttons = args.buttons; // Extend russian language

  $.fancybox.defaults.lang = args.lang;
  $.fancybox.defaults.i18n[args.lang] = args.i18n;

  var fancyboxModal = __webpack_require__(/*! ./fancyboxModal.js */ "./assets/src/fancyboxModal.js");
  /** @type int for human acceptable */


  var oneHour = 60 * 60 * 1000;
  /** global FBModals list of modal posts */

  var modals = FBModals;
  /** global FBM_Settings general plugin settings */

  var args = FBM_Settings;
  args.disabled = getDisabledList(); // Installed fancybox settings

  var libraryArgs = {
    animationEffect: args.lib_args.openCloseEffect,
    transitionEffect: args.lib_args.nextPrevEffect
  };
  /** @type {String} Custom selector for ignore links */

  var stoplist = '.nolightbox';
  /** @type {String} Gallery items selector (need custom prepare @todo may be add filter) */

  var gallerySelector = '.gallery-item a';
  /**
   * Set events by wordpress gallery
   */

  if (args.gallery) {
    $(gallerySelector).not(stoplist).filter(function () {
      return isImage($(this).attr('href'));
    }).each(function () {
      var galleryid = $(this).closest('.gallery').attr("id");
      var $image = $(this).find("img");
      $(this).attr({
        'data-fancybox': galleryid,
        'title': $image.attr('title'),
        'data-caption': $image.attr('alt')
      });
    });
    stoplist += ', ' + gallerySelector;
  }
  /**
   * Set events by all image links
   */


  if (args.force) {
    $('a').not(stoplist).filter(function () {
      return isImage($(this).attr('href'));
    }).each(function () {
      $(this).fancybox(libraryArgs);
    });
  }
  /**
   * Set events by selector
   */


  if (args.selector) {
    // back compatibility
    $(args.selector).each(function (index, el) {
      if (!$(this).data('fancybox') && $(this).attr('rel')) {
        $(this).data('fancybox', $(this).attr('rel'));
      }
    });
    $(args.selector).not(stoplist).fancybox(libraryArgs);
  }
  /**
   * Set events by modal posts
   */


  $.each(modals, function (modal_id, modalArgs) {
    switch (modalArgs.trigger_type) {
      case 'onclick':
        $(modalArgs.trigger).on('click', function (event) {
          event.preventDefault();
          new fancyboxModal(modal_id, modalArgs).open(args);
        });
        break;

      case 'onload':
        $(window).on('ready post-load', function (event) {
          setTimeout(function () {
            new fancyboxModal(modal_id, modalArgs).open(args);
          }, modalArgs.trigger * 1000);
        }).trigger('ready');
        break;

      case 'onclose':
        $(document).one('mouseleave', function (event) {
          new fancyboxModal(modal_id, modalArgs).open(args);
        });
        break;

      case 'shortcode':
      default:
        $('[data-modal-id="' + modal_id + '"]').on('click', function (event) {
          event.preventDefault();
          new fancyboxModal($(this).data('modal-id'), modalArgs).open(args);
        });
        break;
    }
  });
});

/***/ })

/******/ });
//# sourceMappingURL=public.js.map