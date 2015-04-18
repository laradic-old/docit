define([ 'jquery' ], function( $ ){
    'use strict';

    function Markdoc( options ){
        this.options = _.merge(this.defaults, options);
    }

    Markdoc.prototype = {
        defaults   : {},
        options    : {},
        Constructor: Markdoc,
        apply: function($el){
            if( _.isString($el) ){
                $el = $($el);
            }
            this.applyTable($el);
        },
        applyTable: function($el){

            $el.find('div.table-markdoc').each(function(){
                var $tableWrapper = $(this);
                $tableWrapper.removeClass('table-markdoc')

                var $table = $tableWrapper.find('> table').first();
                $table.addClass($tableWrapper[0 ].className);
                $tableWrapper.replaceWith($table);

            });
        }
    };
    return {
        instance   : null,
        applyTo: function( $el ){
            if( _.isString($el) ){
                $el = $($el);
            }
            var md;
            if( !_.isObject(this.instance) ){
                md = this.instance = new Markdoc({

                });
            }
            md.apply($el);
            if($el.hasClass('markdoc')){
                $el.css('display', 'block');
            }
        }
    };

});
