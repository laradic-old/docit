<div class="btn-group {{ $groupClass or '' }}">
  <button type="button" class="btn disabled {{ $type or 'btn-primary' }} btn-{{ $size or 'sm' }}">Version</button>
  <button type="button" data-toggle="dropdown" aria-expanded="false"
          class="btn dropdown-toggle {{ $type or 'btn-primary' }} btn-{{ $size or 'sm' }}"
      >
      {{ $version }} &nbsp;
      <span class="caret"></span>
      <span class="sr-only">Toggle dropdown</span>
  </button>
  <ul role="menu" class="dropdown-menu">
      @foreach($project->getSortedVersions('desc') as $version)
      <li><a href="{{ Projects::url($project['slug'], $version) }}">{{ $version }}</a></li>
      @endforeach
  </ul>
</div>
