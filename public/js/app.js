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
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/app.js":
/*!*****************************!*\
  !*** ./resources/js/app.js ***!
  \*****************************/
/*! no static exports found */
/***/ (function(module, exports) {

/* GLOBALS */
var isoDateFormat = "YYYY-MM-DD",
    calendar = "#calendar",
    calendarInput = "#calendar-value",
    loader = "#loader";
$.ajaxSetup({
  headers: {
    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
  }
});
$(document).ajaxStart(function () {
  $(loader).show();
}).ajaxStop(function () {
  $(loader).hide();
});
/* DATEPICKER */

var setWeek = function setWeek(startDate, endDate) {
  window.location.href = "/calendar?startDate=".concat(startDate, "&endDate=").concat(endDate);
};

var betweenDates = function betweenDates(startDate, endDate) {
  var dates = [];
  var currDate = moment(startDate).startOf("day");
  var lastDate = moment(endDate).startOf("day");
  dates.push(currDate.clone().toDate());

  while (currDate.add(1, "days").diff(lastDate) < 0) {
    dates.push(currDate.clone().toDate());
  }

  dates.push(lastDate.clone().toDate());
  return dates;
};

(function ($) {
  // init week
  var week = {
    start: $(calendarInput).val().split(":")[0],
    end: $(calendarInput).val().split(":")[1]
  };
  $(document).find("#week-prev").on("click", function () {
    setWeek(moment(week.start).add(-1, "week").format(isoDateFormat), moment(week.end).add(-1, "week").format(isoDateFormat));
  });
  $(document).find("#week-next").on("click", function () {
    setWeek(moment(week.start).add(1, "week").format(isoDateFormat), moment(week.end).add(1, "week").format(isoDateFormat));
  }); // init calendar datepicker widget

  $(document).find(calendar).datepicker({
    format: "yyyy-mm-dd",
    weekStart: 1,
    endDate: moment().isoWeek(moment().isoWeek()).format(isoDateFormat),
    maxViewMode: 1,
    language: "ru",
    multidate: false
  }); // set week dates to widget

  $(document).find(calendar).datepicker("setDates", betweenDates(week.start, week.end));
  var selected = $(document).find(calendar).find("td.active");
  selected.siblings("td").addClass("active"); // save selected week to session

  $(document).find(calendar).on("changeDate", function (e) {
    var startDate = moment(e.date).startOf("isoWeek").format(isoDateFormat);
    var endDate = moment(e.date).endOf("isoWeek").format(isoDateFormat);
    setWeek(startDate, endDate);
  }); // style week on mouse over

  $(document).find(calendar).on("mouseover", function () {
    var weeks = $(this).find(".table-condensed tbody tr");
    weeks.on("mouseover", function () {
      $(this).find("td").addClass("highlighted");
    });
    weeks.on("mouseout", function () {
      $(this).find("td").removeClass("highlighted");
    });
  });
})(jQuery);
/* END DATEPICKER */

/* SELECTPICKER SETTINGS */


(function ($) {
  $(".selectpicker.linkable").on("change", function (e) {
    e.preventDefault();
    window.location.href = $(this).find("option:selected").attr("data-link");
  });
})(jQuery);
/* END SELECTPICKER SETTINGS */

/* OTHERS */


(function ($) {
  $(document).on("scroll", function () {
    if ($(this).scrollTop() < $(window).height()) {
      $("#up-button").hide();
    } else {
      $("#up-button").show();
    }
  });
  $("#up-button").on("click", function () {
    $("html, body").animate({
      scrollTop: 0
    }, "fast");
  });
  $("input[type='checkbox']").on("change", function () {
    $(this).prev("input[type='hidden']").val($(this).is(":checked") ? 1 : 0);
  });
  $(".week-control").on("click", function () {
    var startDate = $(this).attr("data-startDate");
    var endDate = $(this).attr("data-endDate");
    setWeek(startDate, endDate);
  });
})(jQuery);

/***/ }),

/***/ "./resources/sass/app.scss":
/*!*********************************!*\
  !*** ./resources/sass/app.scss ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 0:
/*!*************************************************************!*\
  !*** multi ./resources/js/app.js ./resources/sass/app.scss ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! D:\REPO\sayat\bex\resources\js\app.js */"./resources/js/app.js");
module.exports = __webpack_require__(/*! D:\REPO\sayat\bex\resources\sass\app.scss */"./resources/sass/app.scss");


/***/ })

/******/ });