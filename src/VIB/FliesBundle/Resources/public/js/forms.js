/* 
 * Copyright 2013 Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */


/**
 * Handles vial barcode input for vial selection form
 * 
 * @param filter
 */
function checkVial(filter) {
    
    var barcode = $('#barcode').val();
    var rack_redirect = $('#barcode').data('rack-redirect');
    var id = parseInt(barcode.match(/\d+$/),10);
    
    if ((rack_redirect != null)&&(barcode.match(/^R\d+$/) != null)) {
        window.location = rack_redirect +  id;
    } else if (barcode.match(/^\d+$/) == null) {
        $('#barcode').parents('.control-group').addClass('error');
        $('#barcode').addClass('error');
        $('#barcode').parents('.input-append').siblings('span.help-inline').html(form_error('Wrong barcode format'));
        $('#barcode').val('');
        $('#barcode').parents('form').find(':input').blur();
        $('#barcode').focus();
        return;
    }
    
    var checkboxName;
    var checkbox;
    
    checkboxName = "#select_items_" + id;    
    checkbox = $(checkboxName);
    
    $('#barcode').parents('.control-group').removeClass('error');
    $('#barcode').removeClass('error');
    $('#barcode').parents('.input-append').siblings('span.help-inline').html('');
    
    if(checkbox.length) {
        if (checkbox.is(':checked')) {
            checkbox.removeAttr('checked');
            checkbox.parents('tr').children('td')
                .stop().css("background-color","")
                .effect("highlight", {color: "red"}, 5000);
        } else {
            checkbox.prop("checked", true);
            checkbox.parents('tr').children('td')
                .stop().css("background-color","")
                .effect("highlight", {color: "green"}, 5000);
        }
    } else {

        url = $('#barcode').data('link');
        order = $('#select').children('tbody').children('tr').length;
        
        var request = $.ajax({
            type: "GET",
            url: url,
            data: {id: id, filter: filter, format: 'html', order: order}
        });
        
        request.done(
            function(response) {
                $("#select tbody").append(response);
                checkbox = $(checkboxName);
                checkbox.parents('tr').children('td')
                    .stop().css("background-color","")
                    .effect("highlight", {color: "green"}, 5000);
                        
                });
                
        request.fail(
            function(xhr, ajaxOptions, thrownError) {
                $('#barcode').parents('.control-group').addClass('error');
                $('#barcode').addClass('error');
                $('#barcode').parents('.input-append').siblings('span.help-inline').html(form_error(xhr.responseText));
            });
    }

    $('#barcode').val('');
    $('#barcode').parents('form').find(':input').blur();
    $('#barcode').focus();
}

/**
 * Handles vial barcode input for rack vial selection form
 */
function checkRackVial() {
  
    var barcode = $('#barcode').val();
    var vialID = parseInt(barcode.match(/\d+$/),10);
    
    if (barcode.match(/^\d+$/) == null) {
        $('#barcode').parents('.control-group').addClass('error');
        $('#barcode').addClass('error');
        $('#barcode').parents('.input-append').siblings('span.help-inline').html(form_error('Wrong barcode format'));
        $('#barcode').val('');
        $('#barcode').parents('form').find(':input').blur();
        $('#barcode').focus();
        return;
    }
  
    var position = $('.rack-display').find('td.empty.info');
    var positionID = position.length ? position.attr('id').replace("position_", "") : null;
    var rackID = $('.rack-display').attr('id').replace("rack_", "");
    var checkboxName;
    var checkbox;
        
    checkboxName = "#select_items_" + vialID;    
    checkbox = $(checkboxName);
    
    $('#barcode').parents('.control-group').removeClass('error');
    $('#barcode').removeClass('error');
    $('#barcode').parents('.input-append').siblings('span.help-inline').html('');
    
    if(checkbox.length) {
        if (checkbox.is(':checked')) {
            checkbox.removeAttr('checked');
            checkbox.parents('td').stop().css("background-color","")
                .effect("highlight", {color: "red"}, 5000);
        } else {
            checkbox.prop("checked", true);
            checkbox.parents('td').stop().css("background-color","")
                .effect("highlight", {color: "green"}, 5000);
        }
    } else if(position.length) {

        var emptyPositions = $('.rack-display').find('td.empty');
        url = $('#barcode').data('link');
        order = $('.rack-display').find('td').index(position);
        var request = $.ajax({
            type: "GET",
            url: url,
            data: { vialID: vialID, positionID: positionID, rackID: rackID, order: order }
        });
        request.done(
            function(response) {
                position.html(response).removeClass('empty').removeClass('info').addClass('success')
                    .stop().css("background-color","")
                    .effect("highlight", {color: "green"}, 5000);
                position.find('.popover-trigger').hover(function(e) {
                    popoverHover($(this), e);
                });
                if (emptyPositions.length > 1) {
                  var index = emptyPositions.index(position);
                  if (++index < emptyPositions.length) {
                    $(emptyPositions[index]).addClass('info');
                  } else {
                    $(emptyPositions[0]).addClass('info');
                  }
                }
        });
        request.fail(
            function(xhr, ajaxOptions, thrownError) {
                $('#barcode').parents('.control-group').addClass('error');
                $('#barcode').addClass('error');
                $('#barcode').parents('.input-append').siblings('span.help-inline').html(form_error(xhr.responseText));
        });
    } else {
      $('#barcode').parents('.control-group').addClass('error');
      $('#barcode').addClass('error');
      $('#barcode').parents('.input-append').siblings('span.help-inline').html(form_error('Rack is full'));
    }

    $('#barcode').val('');
    $('#barcode').parents('form').find(':input').blur();
    $('#barcode').focus();
}

/**
 * Remove vial from the rack
 * 
 * @param e
 * @param vialID
 * @param rackID
 */
function removeVial(e,vialID,rackID) {
    var element = $(e);
    $.ajax({
          url: element.data('link'),
          type: 'get',
          data: {vialID: vialID, rackID: rackID},
          success: function() {
            var cell = $('#select_items_' + vialID).parents('td')
            $('.rack-display').find('td.info').removeClass('info');
            cell.html('').removeClass('success').addClass('empty info')
                .stop().css("background-color","")
                .effect("highlight", {color: "red"}, 5000);
          }
    });
}

/**
 * Clear rack
 * 
 * @param e
 * @param rackID
 */
function clearRack(e,rackID) {
    var element = $(e);
    $.ajax({
          url: element.data('link'),
          type: 'get',
          data: {rackID: rackID},
          success: function() {
            var cells = $('.rack-display').find('td')
            cells.html('').removeClass('info success').addClass('empty')
            $(cells[0]).addClass('info');
            cells.stop().css("background-color","")
                .effect("highlight", {color: "red"}, 5000);
          }
    });
}

/**
 * Set autoprint
 * 
 * @param e
 */
function setAutoPrint(e) {
  var element = $(e);
  var checked = element.prop("checked");
  var setting = checked ? 'enabled' : 'disabled'
  $.ajax({
          url: element.data('link'),
          type: 'post',
          data: {setting: setting}
    });
}

/**
 * Set labelmode
 * 
 * @param e
 */
function setLabelMode(e) {
  var element = $(e);
  var mode = element.prop("value");
  $.ajax({
          url: element.data('link'),
          type: 'post',
          data: {labelmode: mode}
    });
}

/**
 * Handle popover events
 * 
 * @param el Element
 * @param ev Event
 *
 */
function popoverHover(el, ev) {
  var element = el;
  var event = ev;
  var timeout = element.data('delay') != null ? element.data('delay') : 0;
    
  if (event.type == 'mouseenter') {
    clearTimeout(element.data('timeout'));
    element.data('timeout', setTimeout(function() {
      clearTimeout(element.data('timeout'));
      if (! element.hasClass('loaded')) {
        $.ajax({
          url: element.data('link'),
          type: 'get',
          data: {type: element.data('type'), id: element.data('id'), rack: element.data('rack')},
          success: function(json) {
            title = json.title == 'undefined' ? false : json.title;
            content = json.html == 'undefined' ? false : json.html;
            if ((title != false)&&(content != false)) {
              element.popover({
                title: title,
                content: content,
                html: true,
                trigger: 'manual'
              }).popover('show');
              element.addClass('loaded');
              element.addClass('on');
            }
          }
        });
      } else {
        if (! element.hasClass('on')) {
          element.popover('show');
          element.addClass('on');
        }
      }
    }, timeout));
  } else if (event.type == 'mouseleave') {
    clearTimeout(element.data('timeout'));
    element.data('timeout', setTimeout(function() {
      if (element.hasClass('on')) {
        element.popover('hide');
        element.removeClass('on')
      }
    }, 250));
  }
}

/**
 * Prevent form submission on barcode inputs
 * 
 * @param e
 */
function preventEnterSubmit(e) {
    if (e.which == 13) {
        var targ = $(e.target);
        var form = targ.parents('form');
        if (!targ.is("textarea") && (targ.is(".barcode")||(form.hasClass('select')))) {
            if(targ.is(".barcode")) {
              targ.blur();
              targ.focus();
              targ.select();
            }
            return false;
        }
        return true;
    }
    return null;
}

/**
 * Generate form error html
 * 
 * @param message
 */
function form_error(message) {
    var error_html = "";
    error_html += '<div class="ui-widget">';
    error_html += '<div class="ui-state-error ui-corner-all" style="padding: 3px 5px;">'
    error_html += '<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>'
    error_html += message;
    error_html += '</div>'
    error_html += '</div>'
    return error_html;
}

/*
 * The following code is executed on page load
 */
$(document).ready(function() {
    $('form').bind("keypress", function(e) {
        return preventEnterSubmit(e);
    });

    $('#checkall').click(function () {
        var checked = $(this).is(":checked");
        $(this).parents('table').find('tbody :checkbox').each(function(index) {
           $(this).prop("checked", checked);
        });
    });
    
    $('.checkrow').click(function () {
        var checked = $(this).is(":checked");
        $(this).parents('tr').find(':checkbox').each(function(index) {
           $(this).prop("checked", checked);
        });
    });
   
   $('.popover-trigger').hover(function(e) {
        popoverHover($(this), e);
   });

    $('.rack-display').find('td').click(function() {
        if($(this).hasClass('empty')) {
            $('.rack-display').find('td.info').removeClass('info');
            $(this).addClass('info');
        }
        $('#barcode').focus();
    });
    
    $('.control-group').children('.collapse-toggle').click(function() {
        var target = $(this).parent().find('.collapse').add($(this).parent().nextAll('.collapse:first')).eq(0);
        if (target.hasClass('in')) {
            target.removeClass('visible');
        } else {
            setTimeout(function() {
                target.addClass('visible');
            }, 1000);
        }
    });
})
