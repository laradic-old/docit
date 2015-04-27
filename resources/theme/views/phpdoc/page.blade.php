<style type="text/css">
    #phpdoc-index-nav {
        font-size: 11px;
    }

    #phpdoc-index-nav ul {
        padding-left: 10px;
    }

    .noclick:hover {
        cursor: default;
        background: transparent;
    }

    #phpdoc-doc-container table.table.table-small {
        font-size: 11px
    }

    #phpdoc-doc-container table.table.table-small td {
        padding: 3px;
    }
</style>
<div class="row">
    <div class="col-md-3">

        <div class="box">
            <header><i class="fa fa-list"></i>

                <h3>Menu</h3>
            </header>
            <section id="phpdoc-nav-container">

                <div id="phpdoc-index-nav" class="radic-nav">
                    {!! $index !!}
                </div>


            </section>
        </div>

    </div>
    <div class="col-md-9" id="phpdoc-doc-container">
        <div role="tabpanel" class="tabber">
            <ul role="tablist" class="nav nav-tabs">
                <li role="presentation" class="active"><a href="#doc" aria-controls="doc" role="tab" data-toggle="tab">Docs</a></li>
                <li role="presentation"><a href="#source" aria-controls="source" role="tab" data-toggle="tab">Source</a></li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" id="doc" class="tab-pane active fade in">
                    @if($doc instanceof \Laradic\Docit\Parsers\Phpdoc\File)

                        @include('laradic/docit::phpdoc.partials.general', [ 'doc' => $doc ])

                        @include('laradic/docit::phpdoc.partials.properties', [ 'doc' => $doc ])

                        @include('laradic/docit::phpdoc.partials.methods', [ 'doc' => $doc ])

                    @else
                        Pick a class from the left menu
                    @endif
                </div>
                <div role="tabpanel" id="source" class="tab-pane fade">
                    @if($doc instanceof \Laradic\Docit\Parsers\Phpdoc\File)

<textarea id="source-code"><?php echo $doc->getSource() ?></textarea>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


<script>

    document.addEventListener("DOMContentLoaded", function (event) {
        window.packadic.bindEventHandler('started', function () {


            require(['jquery', 'theme', 'code-mirror!htmlmixed|php:zenburn|twilight|monokai|eclipse' ], function ($, theme, CodeMirror) {

                var editor = CodeMirror.fromTextArea(document.getElementById("source-code"), {
                    mode: "php",
                    lineNumbers: true,
                    styleActiveLine: true,
                    matchBrackets: true,
                    theme: "zenburn",
                    extraKeys: {"Ctrl-Space": "autocomplete"},
                    value: document.documentElement.innerHTML
                });
                editor.setSize('100%', '100%');
                window.editor = editor;

                $('a[href="#source"]').click(function(){
                    console.log('editor click');
                    setTimeout(function(){
                        editor.refresh();
                    }, 200);
                });

                var $nav = $('#phpdoc-index-nav');
                $nav.children('ul').show().find('a[data-level]').each(function () {
                    var $a = $(this);
                    var level = parseInt($a.data('level'));
                    $a.css('padding-left', 10 + (level * 10) + 'px');
                });

                function handleNavHeight() {
                    var $main = $('main');
                    var $content = $main.find('> div.content');
                    var height = parseInt($main.css('min-height').replace('px', ''));
                    height = height - (($content.outerHeight() - $content.height()) * 4);

                    var $navContainer = $('#phpdoc-nav-container');
                    theme.destroySlimScroll($navContainer);
                    theme.initSlimScroll($navContainer, {
                        railColor: '#222',
                        railOpacity: 0.4,
                        size: '6px',
                        height: height + 'px',
                        alwaysVisible: true,
                        allowPageScroll: false
                    });
                }

                theme.on('resize', function () {
                    handleNavHeight();
                });

                setTimeout(function () {
                    handleNavHeight();
                }, 600)


                //$navContainer.slimScroll({
                //    height: height + 'px'
                //});
                //.css('max-height', height)
                //.attr('data-mcs-axis', 'y')
                ///.addClass('scrollable');
                //.mCustomScrollbar();

                //data-mcs-axis="y" style="max-height: 500px" class="scrollable"
            });
        });
    });
</script>
