/* 
 * Copyright 2011 Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
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
 * 
 */
function checkVial(filter)
{
    var id = parseInt($('#barcode').val(),10);
    var checkboxName;
    var checkbox;
    
    checkboxName = "#select_items_" + id;    
    checkbox = $(checkboxName);
    
    $('#barcode').parents('.control-group').removeClass('error');
    $('#barcode').removeClass('error');
    $('#barcode').siblings('span.help-inline').html('');
    
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
          
        var request = $.ajax({
            type: "GET",
            url: url,
            data: {id: id, filter: filter, format: 'html'}
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
                $('#barcode').siblings('span.help-inline').html(form_error(xhr.responseText));
            });
    }

    $('#barcode').val('');
    $('#barcode').parents('form').find(':input').blur();
    $('#barcode').focus();
}

function setupPopover(e) {
  var element = e;
  var timeout = element.data('delay') != null ? element.data('delay') : 0;
  element.data('timeout', setTimeout(function() {
    element.off('mouseenter mouseleave');
    clearTimeout(element.data('timeout'));
    $.ajax({
          url: element.data('link'),
          type: 'get',
          data: {type: element.data('type'), id: element.data('id')},
          success: function(json) {
            title = json.title == 'undefined' ? false : json.title;
            content = json.html == 'undefined' ? false : json.html;
            if ((title != false)&&(content != false)) {
              element.popover({
                title:title,
                content:content,
                html:true,
                trigger:'hover'
              }).popover('show');
            }
          }
    });
  },timeout));
}

function preventEnterSubmit(e) {
    if (e.which == 13) {
        var $targ = $(e.target);
        if (!$targ.is("textarea") && $targ.is(".barcode")) {
            $targ.blur();
            $targ.focus();
            $targ.select();
            return false;
        }
        return true;
    }
}

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

    $('.popover-trigger').hover(function() {
      setupPopover($(this));
    }, function() {
      clearTimeout($(this).data('timeout'));
    });

    $('.rack-display').find('td').click(function() {
      if($(this).hasClass('empty')) {
        $('.rack-display').find('td.info').removeClass('info');
        $(this).addClass('info');
      }
    });
}); 
