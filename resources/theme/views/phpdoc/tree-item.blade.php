
@foreach($treeItem as $name => $item)
@if(is_array($item))
<li>{{ $name }}
    <ul>
        @include('laradic/docit::phpdoc.tree-item', ['treeItem' => $item ])
    </ul>
</li>
@else
<li>{{ $item->name }}</li>
@endif
@endforeach
