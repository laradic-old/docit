<div class="box">
    <header><i class="fa fa-list"></i>

        <h3>{{ \Laradic\Support\String::removeLeft($doc->fullName, '\\') }}</h3>
    </header>
    <section>

        <h3 class="box-heading">
            {{ $doc->name }}
            <small>{{ $doc->type }}</small>
        </h3>

        <div class="row">
            <div class="col-md-6">

                <p>
                    <strong>Full name:</strong>
                    <span class="label label-sm label-warning">
                        {{ \Laradic\Support\String::removeLeft($doc->fullName, '\\') }}
                    </span>
                </p>

                <p><strong>Description:</strong></p>

                <p>{{ $doc->dochead['description'] }}</p>
                @if($doc->docblock['description'])
                    <p>{{ $doc->docblock['description'] }}</p>
                @endif

            </div>
            <div class="col-md-6">

                @if(count($doc->docblock['tags']) > 0)
                    <table class="table table-hover table-condensed table-light table-striped table-bordered">
                        <tbody>
                        @foreach($doc->docblock['tags'] as $tag)
                            @if($tag['name'] === 'link')
                                @set('link', $tag)
                                @continue
                            @endif
                            <tr>
                                <td><strong>{{ $tag['name'] }}</strong></td>
                                <td>
                                    @if($tag['link'])
                                        <a href="{{ $tag['link'] }}" target="_blank">{{ $tag['description'] }}</a>
                                    @else
                                        {{ $tag['description'] }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @if(isset($link))
                        <a href="{{ $link['link'] }}" target="_blank" class="btn btn-info btn-block">Visit website</a>
                    @endif
                @endif

            </div>
        </div>

    </section>
</div>
