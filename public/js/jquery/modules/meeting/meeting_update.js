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
/******/ 	return __webpack_require__(__webpack_require__.s = 10);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/Jquery/modules/meeting/addItemsUpdate.js":
/*!***************************************************************!*\
  !*** ./resources/js/Jquery/modules/meeting/addItemsUpdate.js ***!
  \***************************************************************/
/*! exports provided: addItemTopicsCovered, loadItemTopicsCovered, addItemTopic, loadItemTopic, addItemRegisteredUsers, loadItemRegisteredUSers, addItemInvitedUsers, loadItemInvitedUSers */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "addItemTopicsCovered", function() { return addItemTopicsCovered; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "loadItemTopicsCovered", function() { return loadItemTopicsCovered; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "addItemTopic", function() { return addItemTopic; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "loadItemTopic", function() { return loadItemTopic; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "addItemRegisteredUsers", function() { return addItemRegisteredUsers; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "loadItemRegisteredUSers", function() { return loadItemRegisteredUSers; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "addItemInvitedUsers", function() { return addItemInvitedUsers; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "loadItemInvitedUSers", function() { return loadItemInvitedUSers; });
var base_url = window.location.origin;
/**
 * Adicionar itens aos assuntos abordados.
 */

function addItemTopicsCovered() {
  //adicionar item ASSUNTOS ABORDADOS
  var count = 0;
  $("#addItemTopics_covered").click(function () {
    var topics_covered = "<div class='col-sm-6'>" + "<div class='card card-secondary shadow-lg' style='transition: all 0.15s ease 0s; height: inherit; width: inherit;'> " + "<div class='card-header'>" + "<h3 class='card-title'>Item</h3>" + "<div class='card-tools'>" + "<button type='button' class='btn btn-tool' data-card-widget='collapse'><i class='fas fa-minus'></i></button>" + "<button type='button' class='btn btn-tool' data-card-widget='maximize'><i class='fas fa-expand'></i></button>" + "<button type='button' class='btn btn-tool' data-card-widget='remove'><i class='fas fa-times'></i></button>" + "</div>" + "</div>" + "<div class='card-body'>" + "<div class='form-group'>" + "<label>Assuntos Abordados</label>" + "<input type='text' class='form-control' name='topics_covered[]' id='topics_covered[]' placeholder='' required>" + "</div>" + "<div class='form-group'>" + "<label>Providências</label>" + "<textarea class='form-control' name='providence[]' id='providence[]' cols='30' rows='5' required></textarea>" + "</div>" + "<input type='hidden' class='IdOccurrence' name='IdOccurrence[]' id='IdOccurrence-" + count + "' value=''>" + "<ul class='nav nav-pills flex-column'>" + "<li class='nav-item active'>" + "<a href='#'>" + "<i class='fas fa-clipboard-check'></i>&nbsp;&nbsp;<a data-toggle='modal' id='selectOcurrence-" + count + "' class='selectOcurrence' data-target='#ModalSelectOcurrence' href='javascript:'>Selecionar Ocorrência</a><span class='badge bg-primary float-right' id='numberOccurrence-" + count + "'></span>" + "</a>" + "</li>" + "</ul>" + "</div>" + "</div>" + "</div>";
    $("#divRowtopics_covered").append(topics_covered);
    modalSelectOccurrence();
    count++;
  });
}
function loadItemTopicsCovered() {
  var meeting_topics_covereds = $("#meeting_topics_covereds").val();
  $.each(JSON.parse(meeting_topics_covereds), function (index, value) {
    var count = 0;
    var topics_covered = "<div class='col-sm-6'>" + "<div class='card card-secondary shadow-lg' style='transition: all 0.15s ease 0s; height: inherit; width: inherit;'> " + "<div class='card-header'>" + "<h3 class='card-title'>Item</h3>" + "<div class='card-tools'>" + "<button type='button' class='btn btn-tool' data-card-widget='collapse'><i class='fas fa-minus'></i></button>" + "<button type='button' class='btn btn-tool' data-card-widget='maximize'><i class='fas fa-expand'></i></button>" + // "<button type='button' class='btn btn-tool' data-card-widget='remove'><i class='fas fa-times'></i></button>" +
    "</div>" + "</div>" + "<div class='card-body'>" + "<div class='form-group'>" + "<label>Assuntos Abordados</label>" + "<input type='text' class='form-control' value='" + value.subject_addressed + "' name='topics_covered[]' id='topics_covered[]' placeholder='' required>" + "<input type='hidden' name='topics_covered_id[]' id='topics_covered_id' value='" + value.id + "'>" + "</div>" + "<div class='form-group'>" + "<label>Providências</label>" + "<textarea class='form-control' name='providence[]' id='providence[]' cols='30' rows='5' required>" + value.providence + "</textarea>" + "</div>" + "<input type='hidden' class='IdOccurrence' value='" + value.occurrences_id + "' name='IdOccurrence[]' id='IdOccurrence-" + count + "' value=''>" + "<ul class='nav nav-pills flex-column'>" + "<li class='nav-item active'>" + "<a href='#'>" + //"<i class='fas fa-clipboard-check'></i>&nbsp;&nbsp;<a data-toggle='modal' id='selectOcurrence-" + count + "' class='selectOcurrence' data-target='#ModalSelectOcurrence' href='javascript:'>Selecionar Ocorrência</a><span class='badge bg-primary float-right' id='numberOccurrence-" + count + "'>"+value.occurrences_id+"</span>" +
    "</a>" + "</li>" + "</ul>" + "</div>" + "</div>" + "</div>";
    $("#divRowtopics_covered").append(topics_covered);
    modalSelectOccurrence();
    count++;
  });
}
/**
 * Adicionar itens a pauta
 */

function addItemTopic() {
  var countItem = 0;
  $("#addItemTopic").click(function () {
    var topic = "<tr class='item_append_topic'>" + "<td><input type='text' name='topic[]' id='topic[]' class='form-control form-control-sm rounded-0 indentificator-" + countItem + "' required></td>" + "<td>" + "<label class='click-upload-file-topic-" + countItem + "' style='cursor: pointer;' for='upload-file-topic-" + countItem + "'><i class='fas fa-paperclip'></i></label>&nbsp;" + "<label id='file-name-" + countItem + "'></label>&nbsp;&nbsp;" + "<input style='display:none' id='upload-file-topic-" + countItem + "' name='upload-file-topic-" + countItem + "[]' type='file'>" + "<a type='button' class='remove_item_topic' data-toggle='tooltip' data-placement='top' title='Excluir'><i class='far fa-trash-alt'></i></a>" + "</td>" + "</tr>";
    $("#tbodyItemTopic").append(topic); //remover item

    $(document).on('click', '.remove_item_topic', function () {
      $(this).closest('.item_append_topic').remove();
    });
    $(".click-upload-file-topic-" + countItem).click(function () {
      var identifierClass = $(this).attr("class");
      var numberClass = identifierClass.split('-');
      var selectNumber = numberClass[numberClass.length - 1]; //buscar a ultima posição 

      $("#file-name-" + selectNumber).html(1);
    });
    countItem++;
  });
}
function loadItemTopic() {
  var meeting_subjects = $("#meeting_subjects").val();
  var countItem = 0;
  $.each(JSON.parse(meeting_subjects), function (index, value) {
    var icoDownlaod = "";

    if (!value.url_archive == "") {
      icoDownlaod = "<label style='cursor: pointer;'><a href='" + base_url + "/event/meeting/downlaod/" + value.id + "'><i class='fas fa-download'></i></a></label>&nbsp;";
    }

    var topic = "<tr class='item_append_topic'>" + "<td>" + "<input type='text' name='topic[]' id='topic[]'  value='" + value.subject + "' class='form-control form-control-sm rounded-0 indentificator-" + countItem + "' required>" + "<input type='hidden' name='topics_id[]' id='topics_id' value='" + value.id + "'>" + "</td>" + "<td>" + "<label class='click-upload-file-topic-" + countItem + "' style='cursor: pointer;' for='upload-file-topic-" + countItem + "'><i class='fas fa-paperclip'></i></label>&nbsp;" + icoDownlaod + "<label id='file-name-" + countItem + "'></label>&nbsp;&nbsp;" + "<input style='display:none' id='upload-file-topic-" + countItem + "' name='upload-file-topic-" + countItem + "[]' type='file'>" + // "<a type='button' class='remove_item_topic' data-toggle='tooltip' data-placement='top' title='Excluir'><i class='far fa-trash-alt'></i></a>" +
    "</td>" + "</tr>";
    $("#tbodyItemTopic").append(topic);
    $(document).on('click', '.remove_item_topic', function () {
      $(this).closest('.item_append_topic').remove();
    });
    countItem++;
  });
}
/**
 * Adicionar usuários cadastrados
 */

function addItemRegisteredUsers() {
  $("#buttonUserRegistered").click(function () {
    var userRegistered = $("#userRegistered").val();
    $.get(base_url + "/event/meeting/getUsersRegistered/" + userRegistered, function (data) {
      var obj = JSON.parse(data);
      var html = "<div class='col-sm-6 user-block-registered'>" + " <div class='user-block'>" + "<img class='img-circle' src='/storage/" + obj.data.image + "' alt='User Image'>" + "<span class='username'>" + obj.data.name + "</span>" + "<input type='hidden' name='idUserRegistered[]' value='" + obj.data.id + "'>" + "<span class='description'>Sem função&nbsp;&nbsp;<a type='button' class='removeRegisteredUser' href='#'><i class='far fa-trash-alt'></i></a></span>" + "</div>" + "</div>";
      $("#registered_users").append(html);
      /** Remover participantes cadastrados */

      $(".removeRegisteredUser").click(function () {
        $(this).closest('.user-block-registered').remove();
      });
    });
  });
}
function loadItemRegisteredUSers() {
  var meeting_registered_participants = $("#meeting_registered_participants").val();
  $.each(JSON.parse(meeting_registered_participants), function (index, value) {
    var html = "<div class='col-sm-6 user-block-registered'>" + " <div class='user-block'>" + "<img class='img-circle' src='/storage/" + value.users.image + "' alt='User Image'>" + "<span class='username'>" + value.users.name + "</span>" + "<input type='hidden' name='idUserRegistered[]' value='" + value.users.id + "'>" + "<span class='description'>Sem função</span>" + "</div>" + "</div>";
    $("#registered_users").append(html);
    /** Remover participantes cadastrados */

    $(".removeRegisteredUser").click(function () {
      $(this).closest('.user-block-registered').remove();
    });
  });
}
/**
 * Adicionar usuários convidados
 */

function addItemInvitedUsers() {
  $('form[name="formAddParticipantGuest"]').submit(function (event) {
    event.preventDefault();
    var form_data = new FormData();
    form_data.append('name', $("#name").val());
    form_data.append('email', $("#email").val());
    form_data.append('telephone', $("#telephone").val());
    form_data.append('profession', $("#profession").val());
    $.ajax({
      url: base_url + "/event/meeting/store/participants",
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
          invitedUsers(obj.data);
          clear_form_invited();
        } else {
          DefaultAlert("error", obj.message);
        }
      }
    });
  });
}
function loadItemInvitedUSers() {
  var meeting_invited_participants = $("#meeting_invited_participants").val();
  $.each(JSON.parse(meeting_invited_participants), function (index, value) {
    var html = "<div class='col-sm-6 user-block-invited'>" + " <div class='user-block'>" + "<img class='img-circle' src='/" + value.participants.url_image + "' alt='User Image'>" + "<span class='username'>" + value.participants.name + "</span>" + "<input type='hidden' name='idInvitedUsers[]' value='" + value.participants.id + "'>" + "<span class='description'>" + value.participants.profession + "</span>" + "</div>" + "</div>";
    $("#invited_users").append(html);
    /** Remover participantes cadastrados */

    $(".remove_invited_users").click(function () {
      $(this).closest('.user-block-invited').remove();
    });
  });
}

function invitedUsers(data) {
  var html = "<div class='col-sm-6 user-block-invited'>" + " <div class='user-block'>" + "<img class='img-circle' src='/" + data.url_image + "' alt='User Image'>" + "<span class='username'>" + data.name + "</span>" + "<input type='hidden' name='idInvitedUsers[]' value='" + data.id + "'>" + "<span class='description'>" + data.profession + "&nbsp;&nbsp;<a type='button' class='remove_invited_users' href='#'><i class='far fa-trash-alt'></i></a></span>" + "</div>" + "</div>";
  $("#invited_users").append(html);
  /** Remover participantes cadastrados */

  $(".remove_invited_users").click(function () {
    $(this).closest('.user-block-invited').remove();
  });
}

function modalSelectOccurrence() {
  //var selectNumber = null;
  $(".selectOcurrence").click(function () {
    var identifierClass = this.id;
    var numberClass = identifierClass.split('-');
    var selectNumber = numberClass[numberClass.length - 1]; //buscar a ultima posição

    $(".buttonOccurrence").click(function () {
      var idOccurrence = $(".idOccurence").val();
      $("#IdOccurrence-" + selectNumber).val(idOccurrence);
      $("#numberOccurrence-" + selectNumber).html(idOccurrence);
      $("#ModalSelectOcurrence").modal('hide');
      selectNumber = null;
    });
  });
}

function clear_form_invited() {
  $("#name").val("");
  $("#email").val("");
  $("#telephone").val("");
  $("#profession").val("");
}

/***/ }),

/***/ "./resources/js/Jquery/modules/meeting/meeting_update.js":
/*!***************************************************************!*\
  !*** ./resources/js/Jquery/modules/meeting/meeting_update.js ***!
  \***************************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _addItemsUpdate_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./addItemsUpdate.js */ "./resources/js/Jquery/modules/meeting/addItemsUpdate.js");








var base_url = window.location.origin;
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});
$(function () {
  /**  addItemTopicsCovered() - Inserir itens aos assuntos abordados */
  Object(_addItemsUpdate_js__WEBPACK_IMPORTED_MODULE_0__["addItemTopicsCovered"])();
  Object(_addItemsUpdate_js__WEBPACK_IMPORTED_MODULE_0__["loadItemTopicsCovered"])();
  /** addItemTopic() - Inserir itens a pauta */

  Object(_addItemsUpdate_js__WEBPACK_IMPORTED_MODULE_0__["addItemTopic"])(); //carregar os topicos da pauta

  Object(_addItemsUpdate_js__WEBPACK_IMPORTED_MODULE_0__["loadItemTopic"])();
  /** addItemRegisteredUsers() - adicionar participante cadastrado */

  Object(_addItemsUpdate_js__WEBPACK_IMPORTED_MODULE_0__["addItemRegisteredUsers"])();
  Object(_addItemsUpdate_js__WEBPACK_IMPORTED_MODULE_0__["loadItemRegisteredUSers"])();
  /** addItemInvitedUsers() - adicionar participante convidados */

  Object(_addItemsUpdate_js__WEBPACK_IMPORTED_MODULE_0__["addItemInvitedUsers"])();
  Object(_addItemsUpdate_js__WEBPACK_IMPORTED_MODULE_0__["loadItemInvitedUSers"])();
  /** Alterar uma reunião */
  //Inicialização Select2 Elemento

  $('.select2').select2({
    theme: 'bootstrap4'
  }); //Inicialização Toast

  var Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
  });
  $('form[name="formMeetingEdit"]').submit(function (event) {
    event.preventDefault();
    var count = 0;
    var selectNumberArray = new Array();
    var form_data = new FormData();
    var valid = 0; //Validações

    if (!$('input[name="topic[]"]').length) {
      valid = 1;
      $("#alertError").removeClass('d-none');
      $("#alertError").html("<strong>Opps!</strong> Todos os campos da 'PAUTA' são obrigatórios.");
    }

    if (!$('input[name="topics_covered[]"]').length) {
      valid = 1;
      $("#alertError").removeClass('d-none');
      $("#alertError").html("<strong>Opps!</strong> Todos os campos dos 'ASSUNTOS ABORDADOS' são obrigatórios.");
    }

    if (!$('input[name="idUserRegistered[]"]').length) {
      valid = 1;
      $("#alertError").removeClass('d-none');
      $("#alertError").html("<strong>Opps!</strong> Todos os campos dos 'USUÁRIOS' cadastrados são obrigatórios.");
    }

    $('input[name="IdOccurrence[]"]').each(function () {
      if (this.value <= 0) {
        valid = 1;
        $("#alertError").removeClass('d-none');
        $("#alertError").html("<strong>Opps!</strong> Ao criar um item 'ASSUNTOS ABORDADOS' torna se obrigatório selecionar uma ocorrência ao item.");
      }
    });
    var topics = new Array();
    $('input[name="topic[]"]').each(function () {
      topics.push($(this).val());
      /** O objetivo a baixo é identificar o numero alocado nas classes dos itens e armazenar no array {selectNumberArray} */

      var identifierClass = $(this).attr("class");
      var numberClass = identifierClass.split('-');
      var selectNumber = numberClass[numberClass.length - 1]; //buscar a ultima posição 

      selectNumberArray.push(selectNumber);
    });
    form_data.append('topics[]', topics);
    /**
     * A regra a baixo tem como obejtivo identificar os itens que tem Anexos, o {for} 
     * percorre os itens criados, trazendo na classe um indetificador de cada registro.
     * {selectNumberArray} tem a função de trazer o identificador das classes dos itens da 
     * pauta.
     * {fileEmpty} Variavel do tipo File vazio, para identificar os intens sem anexo.
     */

    var fileEmpty = new File([""], "empty");

    for (var i = 0; i < selectNumberArray.length; i++) {
      $('input[name="upload-file-topic-' + selectNumberArray[i] + '[]"]').each(function () {
        var prop = $(this).prop('files');

        if (prop[0]) {
          form_data.append('files[]', prop[0]);
        } else {
          form_data.append('files[]', fileEmpty);
        }
      });
    }

    var topics_covered = new Array();
    $('input[name="topics_covered[]"]').each(function () {
      topics_covered.push($(this).val());
    });
    form_data.append('topics_covered[]', topics_covered);
    var providence = new Array();
    $('textarea[name="providence[]"]').each(function () {
      providence.push($(this).val());
    });
    form_data.append('providence[]', providence);
    var users_registered = new Array();
    $('input[name="idUserRegistered[]"]').each(function () {
      users_registered.push($(this).val());
    });
    form_data.append('users_registered[]', users_registered);
    var invited_users = new Array();
    $('input[name="idInvitedUsers[]"]').each(function () {
      invited_users.push($(this).val());
    });
    form_data.append('invited_users[]', invited_users);
    var IdOccurrence = new Array();
    $('input[name="IdOccurrence[]"]').each(function () {
      IdOccurrence.push($(this).val());
    });
    form_data.append('IdOccurrence[]', IdOccurrence); //ids

    form_data.append('meeting_id', $("#meeting_id").val());
    var topics_id = new Array();
    $('input[name="topics_id[]"]').each(function () {
      topics_id.push($(this).val());
    });
    form_data.append('topics_id[]', topics_id);
    var topics_covered_id = new Array();
    $('input[name="topics_covered_id[]"]').each(function () {
      topics_covered_id.push($(this).val());
    });
    form_data.append('topics_covered_id[]', topics_covered_id);

    if (valid === 0) {
      $('.overlay').removeClass('d-none');
    }

    if (valid === 0) {
      $.ajax({
        url: base_url + "/event/meeting/update",
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
            window.location.replace(base_url + "/event/list/meeting");
          } else {
            DefaultAlert("error", obj.message);
            $('.overlay').addClass('d-none');
          }
        }
      });
    }
  });
  /**
   * 
   * @param {string} type 
   * @param {string} msg 
   * exemplo: DefaultAlert("success","Cadastro efetuado com sucesso.");
   */

  function DefaultAlert(type, msg) {
    Toast.fire({
      icon: type,
      title: msg
    });
  }
});

/***/ }),

/***/ 10:
/*!*********************************************************************!*\
  !*** multi ./resources/js/Jquery/modules/meeting/meeting_update.js ***!
  \*********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /var/www/aero/public_html/aero/resources/js/Jquery/modules/meeting/meeting_update.js */"./resources/js/Jquery/modules/meeting/meeting_update.js");


/***/ })

/******/ });