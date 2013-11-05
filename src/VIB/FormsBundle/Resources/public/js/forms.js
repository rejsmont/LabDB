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


/*
 * Initialize AJAX typeahead controls
 */
$(document).ready(function () {
  $('.date').datepicker();
  $('.ajax-typeahead').each(function() {
    var $this = $(this);
    var $url = $this.data('link') + '?query=%QUERY';
    $this.typeahead({
      remote: $url
    });
  });
  $('.user-typeahead').each(function() {
    var $this = $(this);
    var $url = $this.data('link') + '?query=%QUERY';
    $this.typeahead({
      remote: $url,
      valueKey: 'username',
      template: '<p>{{fullname}} <strong class="pull-right">{{username}}</strong></p>',
      engine: Hogan
    });
  });
  $('.select2').not('.select2-container').not('.select2-offscreen').select2({
    width: 'resolve',
    minimumResultsForSearch: -1
  });
  $('body').off('click.collection.data-api', '[data-collection-add-btn]');
  $('body').on('click.collection.data-api', '[data-collection-add-btn]', function ( e ) {
    var $btn = $(e.target);
    if (!$btn.hasClass('btn')){
        $btn = $btn.closest('.btn');
    }
    $btn.collection('add');
    e.preventDefault();
      $('.date').datepicker();
    $('.ajax-typeahead').each(function() {
      var $this = $(this);
      var $url = $this.data('link') + '?query=%QUERY';
      $this.typeahead({
        remote: $url
      });
    });
    $('.user-typeahead').each(function() {
      var $this = $(this);
      var $url = $this.data('link') + '?query=%QUERY';
      $this.typeahead({
        remote: $url,
        valueKey: 'username',
        template: '<p>{{fullname}} <strong class="pull-right">{{username}}</strong></p>',
        engine: Hogan
      });
    });
    $('.select2').not('.select2-container').not('.select2-offscreen').select2({
      width: 'resolve',
      minimumResultsForSearch: -1
    });
  });
});
