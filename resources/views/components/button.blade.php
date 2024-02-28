@props(['action'])

<a href="javascript:;">
    <button {{ $attributes->merge(['class' => 'text-white px-2 pt-1.5 pb-1 rounded']) }}
        type='button'
        id="btn-{{$action}}"
        title="{{ __('imageable::messages.'.$action.'_image') }}" 
        onclick="imageLine.{{$action}}(this);"> 
        <span class="material-icons">{{$action}}</span>
    </button>
</a>