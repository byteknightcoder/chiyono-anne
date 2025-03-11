/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/product-editor-block/edit.js":
/*!******************************************!*\
  !*** ./src/product-editor-block/edit.js ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Edit: () => (/* binding */ Edit)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _woocommerce_block_templates__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @woocommerce/block-templates */ "@woocommerce/block-templates");
/* harmony import */ var _woocommerce_block_templates__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_block_templates__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_core_data__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/core-data */ "@wordpress/core-data");
/* harmony import */ var _wordpress_core_data__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_core_data__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _helpers__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./helpers */ "./src/product-editor-block/helpers.js");

/**
 * External dependencies
 */





function Edit({
  context: {
    postType
  }
}) {
  const [productId] = (0,_wordpress_core_data__WEBPACK_IMPORTED_MODULE_2__.useEntityProp)('postType', 'product', 'id');
  const [attributes, setAttributes] = (0,_wordpress_core_data__WEBPACK_IMPORTED_MODULE_2__.useEntityProp)('postType', postType, 'attributes');
  const {
    editEntityRecord
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_4__.useDispatch)('core');
  let inputSelector = `[name^=jckpc_images], #jckpc_sort_order, [name^=jckpc_conditionals],
	#jckpc_enabled, [name^=jckpc_defaults], [name^=jckpc_inventory]`;
  const handleInputChange = event => {
    var param = (0,_helpers__WEBPACK_IMPORTED_MODULE_5__.getParamsAsObject)(jQuery(inputSelector).serialize());
    editEntityRecord('postType', 'product', productId, {
      meta_data: [{
        key: 'jckpc_enabled',
        value: param.jckpc_enabled
      }, {
        key: 'jckpc_sort_order',
        value: param.jckpc_sort_order
      }, {
        key: 'jckpc_images',
        value: param.jckpc_images
      }, {
        key: 'jckpc_defaults',
        value: param.jckpc_defaults
      }, {
        key: 'jckpc_conditionals',
        value: param.jckpc_conditionals
      }, {
        key: 'jckpc_inventory',
        value: param.jckpc_inventory
      }]
    });
  };

  // Empty array as second argument to only run once.
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useEffect)(() => {
    var data = {
      action: 'iconic_pc_get_product_attributes_template',
      post: productId
    };

    // Fetch the template and initialize configurator UI.
    jQuery.post(ajaxurl + `?post=${productId}`, data, function (response) {
      jQuery('.iconic-pc-admin').html(response?.data?.html);
      jQuery('#jckpc_options').removeClass('hidden');
      iconic_pc_attribute.on_ready();
      iconic_pc_main.on_ready();
      iconic_pc_product.cache();
      iconic_pc_product.setup_sorting();
      iconic_pc_product.setup_static_layers();
      iconic_pc_toggle.cache();
      iconic_pc_toggle.setup_toggles();
      jQuery(".woocommerce-help-tip").tipTip({
        attribute: "data-tip",
        fadeIn: 50,
        fadeOut: 50,
        delay: 200,
        keepAlive: !0
      });
    });
    jQuery(document).on('change', inputSelector, handleInputChange);
    jQuery(document).on('click', '.jckpc-image-button, #jckpc-add-static-layer, .jckpc-layer-options__remove', handleInputChange);
    jQuery(document.body).on('pc_image_selected pc_layer_order_changed', handleInputChange);
  }, []);
  const blockProps = (0,_woocommerce_block_templates__WEBPACK_IMPORTED_MODULE_1__.useWooBlockProps)(attributes);
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    ...blockProps
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "iconic-pc-admin"
  }));
}

/***/ }),

/***/ "./src/product-editor-block/helpers.js":
/*!*********************************************!*\
  !*** ./src/product-editor-block/helpers.js ***!
  \*********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getParamsAsObject: () => (/* binding */ getParamsAsObject)
/* harmony export */ });
/**
 * Convert Query Parameters to nested object.
 *
 * @param {string} query The query string.
 * @returns object
 */
const getParamsAsObject = function (query) {
  query = query.substring(query.indexOf('?') + 1);
  var re = /([^&=]+)=?([^&]*)/g;
  var decodeRE = /\+/g;
  var decode = function (str) {
    return decodeURIComponent(str.replace(decodeRE, " "));
  };
  var params = {},
    e;
  while (e = re.exec(query)) {
    var k = decode(e[1]),
      v = decode(e[2]);
    if (k.substring(k.length - 2) === '[]') {
      k = k.substring(0, k.length - 2);
      (params[k] || (params[k] = [])).push(v);
    } else params[k] = v;
  }
  var assign = function (obj, keyPath, value) {
    var lastKeyIndex = keyPath.length - 1;
    for (var i = 0; i < lastKeyIndex; ++i) {
      var key = keyPath[i];
      if (!(key in obj)) obj[key] = {};
      obj = obj[key];
    }
    obj[keyPath[lastKeyIndex]] = value;
  };
  for (var prop in params) {
    var structure = prop.split('[');
    if (structure.length > 1) {
      var levels = [];
      structure.forEach(function (item, i) {
        var key = item.replace(/[?[\]\\ ]/g, '');
        levels.push(key);
      });
      assign(params, levels, params[prop]);
      delete params[prop];
    }
  }
  return params;
};


/***/ }),

/***/ "./src/product-editor-block/editor.scss":
/*!**********************************************!*\
  !*** ./src/product-editor-block/editor.scss ***!
  \**********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ ((module) => {

module.exports = window["React"];

/***/ }),

/***/ "@woocommerce/block-templates":
/*!****************************************!*\
  !*** external ["wc","blockTemplates"] ***!
  \****************************************/
/***/ ((module) => {

module.exports = window["wc"]["blockTemplates"];

/***/ }),

/***/ "@wordpress/blocks":
/*!********************************!*\
  !*** external ["wp","blocks"] ***!
  \********************************/
/***/ ((module) => {

module.exports = window["wp"]["blocks"];

/***/ }),

/***/ "@wordpress/core-data":
/*!**********************************!*\
  !*** external ["wp","coreData"] ***!
  \**********************************/
/***/ ((module) => {

module.exports = window["wp"]["coreData"];

/***/ }),

/***/ "@wordpress/data":
/*!******************************!*\
  !*** external ["wp","data"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["data"];

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ ((module) => {

module.exports = window["wp"]["element"];

/***/ }),

/***/ "./src/product-editor-block/block.json":
/*!*********************************************!*\
  !*** ./src/product-editor-block/block.json ***!
  \*********************************************/
/***/ ((module) => {

module.exports = /*#__PURE__*/JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":3,"name":"iconicpc/product-editor","version":"0.1.0","title":"Product Configurator Settings","category":"widgets","icon":"flag","description":"Settings for the WooCommerce Product Configurator by IconicWP","attributes":{"message":{"type":"string","__experimentalRole":"content","source":"text","selector":"div"}},"supports":{"html":false,"inserter":false},"textdomain":"jckpc","editorScript":"file:./index.js","editorStyle":"file:./index.css","style":"file:./style-index.css"}');

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!*******************************************!*\
  !*** ./src/product-editor-block/index.js ***!
  \*******************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _editor_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./editor.scss */ "./src/product-editor-block/editor.scss");
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./edit */ "./src/product-editor-block/edit.js");
/* harmony import */ var _block_json__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./block.json */ "./src/product-editor-block/block.json");





/**
 * Register block type.
 */
(0,_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__.registerBlockType)(_block_json__WEBPACK_IMPORTED_MODULE_3__, {
  edit: _edit__WEBPACK_IMPORTED_MODULE_2__.Edit
});
})();

/******/ })()
;
//# sourceMappingURL=index.js.map