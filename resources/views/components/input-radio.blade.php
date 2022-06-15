<input
    autocomplete="off"
    type="checkbox"
    id="{{ $id ?? '' }}"
    name="{{ $name }}"
    size="{{ $size ?? '12' }}"
    placeholder="{{ $placeholder ?? '' }}"
    value="{{ old($name, $value ?? '') }}"
    class="mr-2"
    {{ ($required ?? false) ? 'required' : '' }}
    wire:model={{ $model }}
>
