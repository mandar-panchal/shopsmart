<ul>
    @foreach($marketingSources as $source)
        @if($source->parent_branch === $parentId)
            <li data-jstree='{"icon": "far fa-folder"}'>
                {{ $source->name }}
                @include('partials.tree-item', ['parentId' => $source->id])
            </li>
        @endif
    @endforeach
</ul>
