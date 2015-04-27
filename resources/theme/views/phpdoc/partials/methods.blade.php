<div class="box">
    <header>
        <i class="fa fa-list"></i>

        <h3>Methods</h3>
    </header>
    <section>

        @foreach($doc->methods as $method)

            @if($method['visibility'] === 'public')
                @set('visibilityColor', 'green-dark')
            @elseif($method['visibility'] === 'protected')
                @set('visibilityColor', 'deep-orange-dark')
            @else
                @set('visibilityColor', 'grey-dark')
            @endif

            <h3 class="box-heading">
                {{ $method['name'] }}
                <span class="font-size-12">
                    @if($method['params'])
                        (
                        @foreach($method['params'] as $tag)
                            {{ $tag['variable'] }}
                            @if(!$loop->last)
                                ,
                            @endif
                        @endforeach
                        )
                    @endif
                            </span>
                <small class="text-{{ $visibilityColor }}">{{ $method['visibility'] }}</small>
                @if($method['isStatic'])
                    <span class="label label-sm label-warning">static</span>
                @endif
                @if($method['isFinal'])
                    <span class="label label-sm label-warning">static</span>
                @endif
                @if($method['isAbstract'])
                    <span class="label label-sm label-warning">static</span>
                @endif
                @if($method['return'])
                    <span class="pull-right font-size-14 mid-margin-top large-padding-right">
                    <span class="text-grey">returns &nbsp;&nbsp;</span>
                    <span class="text-orange-dark ">{{ $method['return']['type'] }}</span>
                    </span>
                @endif
            </h3>


            <div class="row">
                <div class="col-md-6">
                    <p>
                        <strong>Full name:</strong>
                        <span class="label label-sm label-warning">
                            {{ \Laradic\Support\String::removeLeft($method['fullName'], '\\') }}
                        </span>
                    </p>

                    <p><strong>Description:</strong></p>

                    @if($method['description'])
                        <p>{{ $method['description'] }}</p>
                    @endif

                </div>
                <div class="col-md-6">

                    @if($method['params'])
                        <table class="table table-hover table-condensed table-light table-striped table-bordered table-small">
                            <thead>
                            <tr>
                                <th>Parameter</th>
                                <th>Type</th>
                                <th>Description</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($method['params'] as $tag)

                                <tr>
                                    <td>{{ $tag['variable'] }}</td>
                                    <td>
                                        <small>{{ $tag['type'] }}</small>
                                    </td>
                                    <td>
                                        <small>{{ strip_tags($tag['description']) }}</small>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif

                </div>
            </div>
        @endforeach
    </section>
</div>
