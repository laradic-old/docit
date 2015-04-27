@foreach($treeItem as $name => $item)
    @if(is_array($item))
        <li>
            <a href="#" data-level="{{ $level }}"
               data-toggle="open-close" href="#collapse-{{ md5($name) }}" aria-expanded="false" aria-controls="collapse-{{ md5($name) }}"
                ><i class="fa fa-sort"></i>&nbsp;&nbsp;{{ $name }}</a>
            <ul>
                @include('laradic/docit::phpdoc.tree-item', ['treeItem' => $item, 'level' => $level + 1 ])
            </ul>
        </li>
    @else
        <li>
            <a href="{{ $page->docUrl($item) }}" data-level="{{ $level + 1 }}">
                <i class="fa fa-caret-right"></i>&nbsp;&nbsp;{{ $item->name }}
            </a>
        </li>

    @endif
@endforeach
