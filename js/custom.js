/**
 * Custom JS
 * */

// Inserts classes that cause the search container to render expanded on age load.

     var d = document.getElementById('dsidx-top-search');
     if (typeof(d) != 'undefined' && d != null) {
       d.className += "open";
     }



     var e = document.getElementById('expandableSearchFilters');
     if (typeof(e) != 'undefined' && d != null) {
       e.className += 'searchfilterexpand';
     }

