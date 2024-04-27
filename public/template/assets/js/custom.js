'use strict';

(function () {

  let cardColor, headingColor, labelColor, shadeColor, grayColor;
  if (isDarkStyle) {
    cardColor = config.colors_dark.cardColor;
    labelColor = config.colors_dark.textMuted;
    headingColor = config.colors_dark.headingColor;
    shadeColor = 'dark';
    grayColor = '#5E6692'; // gray color is for stacked bar chart
  } else {
    cardColor = config.colors.cardColor;
    labelColor = config.colors.textMuted;
    headingColor = config.colors.headingColor;
    shadeColor = '';
    grayColor = '#817D8D';
  }

  // swiper loop and autoplay
  // --------------------------------------------------------------------
  const swiperWithPagination = document.querySelector('#swiper-with-pagination-cards');
  if (swiperWithPagination) {
    new Swiper(swiperWithPagination, {
      loop: true,
      autoplay: {
        delay: 2500,
        disableOnInteraction: false
      },
      pagination: {
        clickable: true,
        el: '.swiper-pagination'
      }
    });
  }

  const selectPicker = $('.selectpicker'),
    select2 = $('.select2'),
    select2Icons = $('.select2-icons');

  // Bootstrap Select
  // --------------------------------------------------------------------
  if (selectPicker.length) {
    selectPicker.selectpicker();
  }

  // Select2
  // --------------------------------------------------------------------
  // Default
  if (select2.length) {
    select2.each(function () {
      var $this = $(this);
      $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'Select value',
        dropdownParent: $this.parent()
      });
    });
  }


  // Bootstrap toasts example
  // --------------------------------------------------------------------
  const toastAnimationExample = document.querySelector('.toast-ex'),
    toastPlacementExample = document.querySelector('.toast-placement-ex'),
    toastAnimationBtn = document.querySelector('#showToastAnimation'),
    toastPlacementBtn = document.querySelector('#showToastPlacement');
  let selectedType, selectedAnimation, selectedPlacement, toast, toastAnimation, toastPlacement;

  // Animation Button click
  if (toastAnimationBtn) {
    toastAnimationBtn.onclick = function () {
      if (toastAnimation) {
        toastDispose(toastAnimation);
      }
      selectedType = document.querySelector('#selectType').value;
      selectedAnimation = document.querySelector('#selectAnimation').value;
      toastAnimationExample.classList.add(selectedAnimation);
      toastAnimationExample.querySelector('.ti').classList.add(selectedType);
      toastAnimation = new bootstrap.Toast(toastAnimationExample);
      toastAnimation.show();
    };
  }

  // Dispose toast when open another
  function toastDispose(toast) {
    if (toast && toast._element !== null) {
      if (toastPlacementExample) {
        toastPlacementExample.classList.remove(selectedType);
        toastPlacementExample.querySelector('.ti').classList.remove(selectedType);
        DOMTokenList.prototype.remove.apply(toastPlacementExample.classList, selectedPlacement);
      }
      if (toastAnimationExample) {
        toastAnimationExample.classList.remove(selectedType, selectedAnimation);
        toastAnimationExample.querySelector('.ti').classList.remove(selectedType);
      }
      toast.dispose();
    }
  }
  // Placement Button click
  if (toastPlacementBtn) {
    toastPlacementBtn.onclick = function () {
      if (toastPlacement) {
        toastDispose(toastPlacement);
      }
      selectedType = document.querySelector('#selectTypeOpt').value;
      selectedPlacement = document.querySelector('#selectPlacement').value.split(' ');

      toastPlacementExample.querySelector('.ti').classList.add(selectedType);
      DOMTokenList.prototype.add.apply(toastPlacementExample.classList, selectedPlacement);
      toastPlacement = new bootstrap.Toast(toastPlacementExample);
      toastPlacement.show();
    };
  }

  setTimeout(function() {
    $('.bs-toast').toast('hide');
  }, 5000);

  // Flat Picker
  // --------------------------------------------------------------------
  const flatpickrDate = document.querySelector('#flatpickr-date'),
    flatpickrTime = document.querySelector('#flatpickr-time'),
    flatpickrDateTime = document.querySelector('#flatpickr-datetime'),
    flatpickrMulti = document.querySelector('#flatpickr-multi'),
    flatpickrRange = document.querySelector('#flatpickr-range'),
    flatpickrInline = document.querySelector('#flatpickr-inline'),
    flatpickrFriendly = document.querySelector('#flatpickr-human-friendly'),
    flatpickrDisabledRange = document.querySelector('#flatpickr-disabled-range');

  // Date
  if (flatpickrDate) {
    flatpickrDate.flatpickr({
      monthSelectorType: 'static'
    });
  }

  // Time
  if (flatpickrTime) {
    flatpickrTime.flatpickr({
      enableTime: true,
      noCalendar: true
    });
  }

  // Datetime
  if (flatpickrDateTime) {
    flatpickrDateTime.flatpickr({
      enableTime: true,
      dateFormat: 'Y-m-d H:i'
    });
  }

  // Multi Date Select
  if (flatpickrMulti) {
    flatpickrMulti.flatpickr({
      weekNumbers: true,
      enableTime: true,
      mode: 'multiple',
      minDate: 'today'
    });
  }

  // Range
  if (typeof flatpickrRange != undefined) {
    flatpickrRange.flatpickr({
      mode: 'range'
    });
  }

  // Inline
  if (flatpickrInline) {
    flatpickrInline.flatpickr({
      inline: true,
      allowInput: false,
      monthSelectorType: 'static'
    });
  }

  // Human Friendly
  if (flatpickrFriendly) {
    flatpickrFriendly.flatpickr({
      altInput: true,
      altFormat: 'F j, Y',
      dateFormat: 'Y-m-d'
    });
  }

  // Disabled Date Range
  if (flatpickrDisabledRange) {
    const fromDate = new Date(Date.now() - 3600 * 1000 * 48);
    const toDate = new Date(Date.now() + 3600 * 1000 * 48);

    flatpickrDisabledRange.flatpickr({
      dateFormat: 'Y-m-d',
      disable: [
        {
          from: fromDate.toISOString().split('T')[0],
          to: toDate.toISOString().split('T')[0]
        }
      ]
    });
  }

  // Bootstrap Datepicker
  // --------------------------------------------------------------------
  var bsDatepickerBasic = $('#bs-datepicker-basic'),
    bsDatepickerFormat = $('#bs-datepicker-format'),
    bsDatepickerRange = $('#bs-datepicker-daterange'),
    bsDatepickerDisabledDays = $('#bs-datepicker-disabled-days'),
    bsDatepickerMultidate = $('#bs-datepicker-multidate'),
    bsDatepickerOptions = $('#bs-datepicker-options'),
    bsDatepickerAutoclose = $('#bs-datepicker-autoclose'),
    bsDatepickerInlinedate = $('#bs-datepicker-inline');

  // Basic
  if (bsDatepickerBasic.length) {
    bsDatepickerBasic.datepicker({
      todayHighlight: true,
      orientation: isRtl ? 'auto right' : 'auto left'
    });
  }

  // Format
  if (bsDatepickerFormat.length) {
    bsDatepickerFormat.datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      orientation: isRtl ? 'auto right' : 'auto left'
    });
  }

  // Range
  if (bsDatepickerRange.length) {
    bsDatepickerRange.datepicker({
      todayHighlight: true,
      orientation: isRtl ? 'auto right' : 'auto left'
    });
  }

  // Disabled Days
  if (bsDatepickerDisabledDays.length) {
    bsDatepickerDisabledDays.datepicker({
      todayHighlight: true,
      daysOfWeekDisabled: [0, 6],
      orientation: isRtl ? 'auto right' : 'auto left'
    });
  }

  // Multiple
  if (bsDatepickerMultidate.length) {
    bsDatepickerMultidate.datepicker({
      multidate: true,
      todayHighlight: true,
      orientation: isRtl ? 'auto right' : 'auto left'
    });
  }

  // Options
  if (bsDatepickerOptions.length) {
    bsDatepickerOptions.datepicker({
      calendarWeeks: true,
      clearBtn: true,
      todayHighlight: true,
      orientation: isRtl ? 'auto left' : 'auto right'
    });
  }

  // Auto close
  if (bsDatepickerAutoclose.length) {
    bsDatepickerAutoclose.datepicker({
      todayHighlight: true,
      autoclose: true,
      orientation: isRtl ? 'auto right' : 'auto left'
    });
  }

  // Inline picker
  if (bsDatepickerInlinedate.length) {
    bsDatepickerInlinedate.datepicker({
      todayHighlight: true
    });
  }

  // Bootstrap Daterange Picker
  // --------------------------------------------------------------------
  var bsRangePickerBasic = $('#bs-rangepicker-basic'),
    bsRangePickerSingle = $('#bs-rangepicker-single'),
    bsRangePickerTime = $('#bs-rangepicker-time'),
    bsRangePickerRange = $('#bs-rangepicker-range'),
    bsRangePickerWeekNum = $('#bs-rangepicker-week-num'),
    bsRangePickerDropdown = $('#bs-rangepicker-dropdown'),
    bsRangePickerCancelBtn = document.getElementsByClassName('cancelBtn');

  // Basic
  if (bsRangePickerBasic.length) {
    bsRangePickerBasic.daterangepicker({
      opens: isRtl ? 'left' : 'right'
    });
  }

  // Single
  if (bsRangePickerSingle.length) {
    bsRangePickerSingle.daterangepicker({
      singleDatePicker: true,
      opens: isRtl ? 'left' : 'right'
    });
  }

  // Time & Date
  if (bsRangePickerTime.length) {
    bsRangePickerTime.daterangepicker({
      timePicker: true,
      timePickerIncrement: 30,
      locale: {
        format: 'MM/DD/YYYY h:mm A'
      },
      opens: isRtl ? 'left' : 'right'
    });
  }

  if (bsRangePickerRange.length) {
    bsRangePickerRange.daterangepicker({
      ranges: {
        Today: [moment(), moment()],
        Yesterday: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
      opens: isRtl ? 'left' : 'right'
    });
  }

  // Week Numbers
  if (bsRangePickerWeekNum.length) {
    bsRangePickerWeekNum.daterangepicker({
      showWeekNumbers: true,
      opens: isRtl ? 'left' : 'right'
    });
  }
  // Dropdown
  if (bsRangePickerDropdown.length) {
    bsRangePickerDropdown.daterangepicker({
      showDropdowns: true,
      opens: isRtl ? 'left' : 'right'
    });
  }

  // Adding btn-secondary class in cancel btn
  for (var i = 0; i < bsRangePickerCancelBtn.length; i++) {
    bsRangePickerCancelBtn[i].classList.remove('btn-default');
    bsRangePickerCancelBtn[i].classList.add('btn-secondary');
  }

  // jQuery Timepicker
  // --------------------------------------------------------------------
  var basicTimepicker = $('#timepicker-basic'),
    minMaxTimepicker = $('#timepicker-min-max'),
    disabledTimepicker = $('#timepicker-disabled-times'),
    formatTimepicker = $('#timepicker-format'),
    stepTimepicker = $('#timepicker-step'),
    altHourTimepicker = $('#timepicker-24hours');

  // Basic
  if (basicTimepicker.length) {
    basicTimepicker.timepicker({
      orientation: isRtl ? 'r' : 'l'
    });
  }

  // Min & Max
  if (minMaxTimepicker.length) {
    minMaxTimepicker.timepicker({
      minTime: '2:00pm',
      maxTime: '7:00pm',
      showDuration: true,
      orientation: isRtl ? 'r' : 'l'
    });
  }

  // Disabled Picker
  if (disabledTimepicker.length) {
    disabledTimepicker.timepicker({
      disableTimeRanges: [
        ['12am', '3am'],
        ['4am', '4:30am']
      ],
      orientation: isRtl ? 'r' : 'l'
    });
  }

  // Format Picker
  if (formatTimepicker.length) {
    formatTimepicker.timepicker({
      timeFormat: 'H:i:s',
      orientation: isRtl ? 'r' : 'l'
    });
  }

  // Steps Picker
  if (stepTimepicker.length) {
    stepTimepicker.timepicker({
      step: 15,
      orientation: isRtl ? 'r' : 'l'
    });
  }

  // 24 Hours Format
  if (altHourTimepicker.length) {
    altHourTimepicker.timepicker({
      show: '24:00',
      timeFormat: 'H:i:s',
      orientation: isRtl ? 'r' : 'l'
    });
  }
})
();