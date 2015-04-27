<div class="box">
    <header>
        <i class="fa fa-list"></i>

        <h3>Properties</h3>
    </header>

    <section class="box-table">
        @if(count($doc->properties) > 0)
            <table class="table table-hover table-condensed table-light table-striped table-bordered">
                <thead>
                <tr>
                    <th>Property</th>
                    <th>Visibility</th>
                    <th>Type</th>
                    <th>Description</th>
                </tr>
                </thead>
                <tbody>
                @foreach($doc->properties as $prop)
                    <tr>
                        <td><strong>{{ $prop['name'] }}</strong></td>
                        <td>
                            @if($prop['visibility'] === 'public')
                                @set('visibilityColor', 'green-dark')
                            @elseif($prop['visibility'] === 'protected')
                                @set('visibilityColor', 'deep-orange-dark')
                            @else
                                @set('visibilityColor', 'grey-dark')
                            @endif
                            <small class="text-{{ $visibilityColor }}">{{ $prop['visibility'] }}</small>
                        </td>
                        <td><small>{{ $prop['type'] }}</small></td>
                        <td>
                            @if(is_string($prop['docblock']['description']))
                                {{ $prop['docblock']['description'] }}
                            @else
                                <small class="text-muted">na</small>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </section>
</div>
