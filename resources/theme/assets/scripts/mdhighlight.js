(function(){
    window.packadic.bindEventHandler('started', function(){

        // @todo  move to seperate script
        console.log('mdhighlight window.packadic.started', window.packadic);
        require([ 'jquery', 'plugins/highlightjs' ],
            function( $, highlightjs ){
                'use strict';

                console.log('highlightjs', highlightjs);
                var parsedown = ".blade-markdown pre code[class^='language-']";
                var cidonia = ".blade-markdown .prettyprint code";
                $(parsedown + ", " + cidonia).each(function(){
                    var $el = $(this);
                    $el.addClass('hljs');
                    console.log('pre code markdown', $el);
                    $el.html(highlightjs.highlightAuto($el.text()).value);
                })

            });


    })
}.call())
