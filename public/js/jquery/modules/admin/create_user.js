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
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 2);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/Jquery/modules/admin/create_user.js":
/*!**********************************************************!*\
  !*** ./resources/js/Jquery/modules/admin/create_user.js ***!
  \**********************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

var base_url = window.location.origin;
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});
$(function () {
  //Initialize Select2 Elements
  $('.select2').select2({
    theme: 'bootstrap4'
  });
  var Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
  });
  $('form[name="formUser"]').submit(function (event) {
    event.preventDefault();
    var form_data = new FormData();
    form_data.append('photo', $('#photo').prop('files')[0]);
    form_data.append('name', $("#name").val());
    form_data.append('email', $("#email").val());
    form_data.append('password', $("#password").val());
    form_data.append('profile', $('#profile').val());
    $('.overlay').removeClass('d-none');
    $.ajax({
      url: base_url + "/admin/new/user/create",
      type: "POST",
      data: form_data,
      dataType: 'text',
      cache: false,
      contentType: false,
      processData: false,
      enctype: 'multipart/form-data',
      success: function success(response) {
        var obj = JSON.parse(response);

        if (obj.success === true) {
          DefaultAlert("success", obj.message);
          $('.overlay').addClass('d-none');
          clearForm();
        } else {
          DefaultAlert("error", obj.message);
          $('.overlay').addClass('d-none');
        }
      }
    });
  }); // exemplo: DefaultAlert("success","Cadastro efetuado com sucesso."); 

  function DefaultAlert(type, msg) {
    Toast.fire({
      icon: type,
      title: msg
    });
  } //clear form


  function clearForm() {
    $("#name").val("");
    $("#email").val("");
    $("#password").val("");
  }
});

/***/ }),

/***/ 2:
/*!****************************************************************!*\
  !*** multi ./resources/js/Jquery/modules/admin/create_user.js ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /var/www/aero/public_html/aero/resources/js/Jquery/modules/admin/create_user.js */"./resources/js/Jquery/modules/admin/create_user.js");


/***/ })

/******/ });