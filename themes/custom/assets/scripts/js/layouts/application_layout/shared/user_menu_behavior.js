/**
 * Scripts Ahoy! Software
 *
 * Copyright (c) 2012 Scripts Ahoy! (scriptsahoy.com)
 * All terms, conditions and copyrights as defined
 * in the Scripts Ahoy! License Agreement
 * http://www.scriptsahoy.com/license
 *
 */
 
/**
 * User Menu behavior
 */

var Page = (function(page, $) {

    page.bindUserMenu = function() {
        $('#site-user-menu .user > li').click(function(){            
            $(this).children('ul:first').slideToggle().end().closest('#site-user-menu').toggleClass('expanded');
        }); 
    }

    return page;
}(Page || {}, jQuery));