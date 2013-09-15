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
  $('.ajax-typeahead').typeahead({
    source: function(query, process) {
      return $.ajax({
        url: $(this)[0].$element.data('link'),
        type: 'get',
        data: {query: query},
        dataType: 'json',
        success: function(json) {
          return typeof json.options == 'undefined' ? false : process(json.options);
        }
      });
    },
    matcher: function (item) {
      return true;
    }
  });
  
  $('.user-typeahead').typeahead({
    source: function(query, process) {
      return $.ajax({
        url: $(this)[0].$element.data('link'),
        type: 'get',
        data: {query: query},
        dataType: 'json',
        success: function(json) {
          return typeof json.options == 'undefined' ? false : process(json.options);
        }
      });
    },
    matcher: function (item) {
      return true;
    },
    updater: function (item) {
      return item.replace(/.*\[\[/,'').replace(/\]\]/,'')
    },
    sorter: function (items) {
      var beginswith = []
        , caseSensitive = []
        , caseInsensitive = []
        , item

      while (item = items.shift()) {
        if (!item.toLowerCase().indexOf(this.query.toLowerCase())) beginswith.push(item)
        else if (~item.indexOf(this.query)) caseSensitive.push(item)
        else caseInsensitive.push(item)
      }

      return beginswith.concat(caseSensitive, caseInsensitive)
    },
    highlighter: function (item) {
      var query = this.query.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, '\\$&')
      var name = item.replace(/\[\[.*\]\]/,'')
        .replace(new RegExp('(' + query + ')', 'ig'), function ($1, match) {
          return '<strong>' + match + '</strong>'
      })
      var username = item.replace(/.*\[\[/,'').replace(/\]\]/,'')
        .replace(new RegExp('(' + query + ')', 'ig'), function ($1, match) {
          return '<strong>' + match + '</strong>'
      })
      return name + '<span class="pull-right">' + username + '</span>'
    }
  })
  $('.select2').select2({
    width: 'resolve',
    minimumResultsForSearch: -1
  })
});
