@props(['color', 'action'])

<a href="javascript:;">
    <button {{ $attributes->merge([
        'class' => 'bg-'.$color.'-500 hover:bg-'.$color.'-700 text-white py-1 px-2 rounded',
        ]) }}
        type='button'
        id="btn-{{$action}}"
        title="{{ __('imageable::messages.'.$action.'_image') }}" 
        onclick="imageLine.{{$action}}(this);"> 
        <span class="material-icons">{{$action}}</span>
    </button>
</a>