<ul>
    @forelse ($items as $item)
        <li>{{ $item->name ?? $item->title ?? $item->id }}</li>
    @empty

    @endforelse
</ul>
