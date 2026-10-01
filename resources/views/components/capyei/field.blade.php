@push('prehead')
    @vite(['resources/css/app.css', 'resources/js/app.js']);
@endpush

{{-- x-capyei.field --}}
@props([
    'type'          => 'text',
    'name',
    'id',
    'customId'      => false,
    'label'         => '',
    'value'         => null,
    'disabled'      => false,
    'required'      => false,
    'multiple'      => false,
    'errors',
    'fill'          => true,
    'hasLabel'      => true,
    'subtext'       => null,
    'property'      => '',
    'propertyField' => '',
    'propertyLabel' => '',
    'placeholder'   => '',
    'classField'    => '',
    'min'           => null,
    'max'           => null,
    'description',
    'options',
    'item',
    'node'
])

<div {{ $attributes->merge(['class' => "content__field " . ($fill ? 'fill' : '') . ""]) }}>
    <div class="d-block" {!! $property !!}>
        @if ($type == 'select')
            @if($hasLabel)
                <label for="{{ $customId ? ('field__custom-'.$id) : $id }}" class="form-label"
                    {!! $propertyLabel !!}>
                    <b>{{ $label }} @if($subtext) {!! $subtext !!} @endif</b>
                </label>
            @endif
            {{-- @if (isset($slot))
                {{ $slot }}
            @else --}}
                <select name="{{ $name }}" id="{{ $customId ? ('field__custom-'.$id) : $id }}" {{ $disabled ? 'disabled' : '' }} {{ $required ? 'required' : '' }} class="{{ $classField }} form-control selectTwo" {{ $multiple ? 'multiple="multiple"' : '' }}
                    {!! $propertyField !!}
                >
                    @if ($placeholder)
                        <option value="" disabled>{{ $placeholder }}</option>
                    @endif
                    {{-- @foreach ($options as $value_option => $text)
                        <option {{ $value == $value_option ? 'selected' : '' }} value="{{ $value_option }}">{{ $text }}</option>
                    @endforeach --}}
                    <option value="">--Seleccione una opción--</option>
                    @foreach ($options as $option)
                        <option {{ $value == $option['key'] || $option['key'] == request()->$name ?  'selected' : '' }} value="{{ $option['key'] }}">{{ $option['value'] }}</option>
                    @endforeach
                </select>
            {{-- @endif --}}
        @elseif($type == 'checkbox' || $type == 'radio')
            <div class="form-check">
                <div class="{{ $type }}__field">
                    <input type="{{ $type }}" value="{{ $value ?? '' }}" name="{{ $name }}" id="{{ $customId ? ('field__custom-'.$id) : $id }}" class="{{ $classField }} form-check-input" {{ $disabled ? 'disabled' : '' }} {{ $required ? 'required' : '' }}
                        {!! $propertyField !!}
                    >
                    <span></span>
                </div>
                @if($hasLabel)
                    <label for="{{ $customId ? ('field__custom-'.$id) : $id }}" class="form-check-label"
                        {!! $propertyLabel !!}>
                        <b>{{ $label }} @if($subtext) {!! $subtext !!} @endif</b>
                    </label>
                @endif
            </div>
        @elseif($type == 'switch')
            <div class="form-check form-switch">
                <div class="{{ $type }}__field">
                    <input type="checkbox" value="{{ $value ?? '' }}" name="{{ $name }}" id="{{ $customId ? ('field__custom-'.$id) : $id }}" class="{{ $classField }} form-check-input" {{ $disabled ? 'disabled' : '' }} {{ $required ? 'required' : '' }}
                        {!! $propertyField !!}
                    >
                    <span></span>
                </div>
                @if($hasLabel)
                    <label for="{{ $customId ? ('field__custom-'.$id) : $id }}" class="form-check-label"
                        {!! $propertyLabel !!}>
                        <b>{{ $label }} @if($subtext) {!! $subtext !!} @endif</b>
                    </label>
                @endif
            </div>
        @elseif($type == 'textarea')
            @if($hasLabel)
                <label for="{{ $customId ? ('field__custom-'.$id) : $id }}" class="form-label"
                    {!! $propertyLabel !!}>
                    <b>{{ $label }} @if($subtext) {!! $subtext !!} @endif</b>
                </label>
            @endif
            <div class="{{ $type }}__field">
                <textarea name="{{ $name }}" id="{{ $customId ? ('field__custom-'.$id) : $id }}" class="{{ $classField }} form-control" {{ $disabled ? 'disabled' : '' }} {{ $required ? 'required' : '' }} rows="3"
                    {!! $propertyField !!}
                >{!! $value ?? '' !!}</textarea>
                <span></span>
            </div>
        @elseif($type == 'froala')
            @if($hasLabel)
                <label for="{{ $customId ? ('field__custom-'.$id) : $id }}" class="form-label"
                    {!! $propertyLabel !!}>
                    <b>{{ $label }} @if($subtext) {!! $subtext !!} @endif</b>
                </label>
            @endif
            <div class="{{ $type }}__field" wire:ignore>
                <textarea name="{{ $name }}" value="<p>gg</p>" id="{{ $customId ? ('field__custom-'.$id) : $id }}" class="{{ $classField }} form-control froala-textarea" {{ $disabled ? 'disabled' : '' }} {{ $required ? 'required' : '' }} rows="3"
                    {!! $propertyField !!}
                >{!! htmlspecialchars_decode($value) !!}</textarea>
                <span></span>
            </div>
        @elseif($type == 'date')
            @if($hasLabel)
                <label for="{{ $customId ? ('field__custom-'.$id) : $id }}" class="form-label"
                    {!! $propertyLabel !!}>
                    <b>{{ $label }} @if($subtext) {!! $subtext !!} @endif</b>
                </label>
            @endif
            <div class="d-block" wire:ignore>
                <input placeholder="{{ $placeholder }}" type="date" value="{{ $value ?? request()->$name ?? '' }}" name="{{ $name }}" id="{{ $customId ? ('field__custom-'.$id) : $id }}" class="{{ $classField }} flatpickr-basic form-control" {{ $disabled ? 'disabled' : '' }} {{ $required ? 'required' : '' }} {{ $min ? 'min='.$min : '' }} {{ $max ? 'max='.$max : '' }}
                    {!! $propertyField !!}
                >
            </div>
        @elseif($type === 'password')
            @if($hasLabel)
                <label for="{{ $customId ? ('field__custom-'.$id) : $id }}" class="form-label"
                    {!! $propertyLabel !!}>
                    <b>{{ $label }} @if($subtext) {!! $subtext !!} @endif</b>
                </label>
            @endif
            <div class="input-group input-group-merge form-password-toggle">
                <input placeholder="{{ $placeholder }}" type="password" value="{{ $value ?? '' }}" name="{{ $name }}" id="{{ $customId ? ('field__custom-'.$id) : $id }}" aria-describedby="password" class="{{ $classField }} form-control form-control-merge" {{ $disabled ? 'disabled' : '' }} {{ $required ? 'required' : '' }} {{ $min ? 'min='.$min : '' }} {{ $max ? 'max='.$max : '' }}
                    {!! $propertyField !!}
                >
                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
            </div>
        @elseif($type === 'boolean')
            @if($hasLabel)
                <label for="{{ $customId ? ('field__custom-'.$id) : $id }}" class="form-label"
                    {!! $propertyLabel !!}>
                    <b>{{ $label }} @if($subtext) {!! $subtext !!} @endif</b>
                </label>
            @endif
            <select name="{{ $name }}" id="{{ $customId ? ('field__custom-'.$id) : $id }}" {{ $disabled ? 'disabled' : '' }} {{ $required ? 'required' : '' }} class="{{ $classField }} form-control selectTwo"
                {!! $propertyField !!}
            >
                @if ($placeholder)
                    <option value="" disabled>{{ $placeholder }}</option>
                @endif
                <option {{ $value == '1' ? 'selected' : '' }} value="1">Si</option>
                <option {{ $value == '0' ? 'selected' : '' }} value="0">No</option>
            </select>
        @elseif($type === 'image' || $type === 'video' || $type === 'file')
            @if($hasLabel)
                <label for="{{ $customId ? ('field__custom-'.$id) : $id }}" class="form-label"
                    {!! $propertyLabel !!}>
                    <b>{{ $label }} @if($subtext) {!! $subtext !!} @endif</b>
                </label>
            @endif
            <input placeholder="{{ $placeholder }}" type="file" value="{{ $value ?? '' }}" name="{{ $name }}" id="{{ $customId ? ('field__custom-'.$id) : $id }}" class="{{ $classField }} form-control" {{ $disabled ? 'disabled' : '' }} {{ $required ? 'required' : '' }} {{ $min ? 'min='.$min : '' }} {{ $max ? 'max='.$max : '' }}
                {!! $propertyField !!}
            >
            <br>
            @if( isset($item[$name]) )
                @if( $type == 'file' )
                    <div><a href="{{ \Asset::get_file( $node. '-'. $name, $item[$name] ) }}" target="_blank">Descargar</a></div>
                @elseif( $type == 'image' )
                    <div class="content__image">
                        <img src="{{\Asset::get_image_path( $node .'-'.$name, 'mini', $item[$name]) }}" alt="Image" />
                    </div>
                @endif
            @endif
        @elseif($type === 'integer' || $type === 'double')
            @if($hasLabel)
                <label for="{{ $customId ? ('field__custom-'.$id) : $id }}" class="form-label"
                    {!! $propertyLabel !!}>
                    <b>{{ $label }} @if($subtext) {!! $subtext !!} @endif</b>
                </label>
            @endif
            <input placeholder="{{ $placeholder }}" type="number" onkeypress="return isNumberKey(event,this)" value="{{ $value ?? request()->$name ?? '' }}" name="{{ $name }}" id="{{ $customId ? ('field__custom-'.$id) : $id }}" class="{{ $classField }} form-control" {{ $disabled ? 'disabled' : '' }} {{ $required ? 'required' : '' }} {{ $min ? 'min='.$min : '' }} {{ $max ? 'max='.$max : '' }}
                {!! $propertyField !!}
            >
        @else
            @if($hasLabel)
                <label for="{{ $customId ? ('field__custom-'.$id) : $id }}" class="form-label"
                    {!! $propertyLabel !!}>
                    <b>{{ $label }} @if($subtext) {!! $subtext !!} @endif</b>
                </label>
            @endif
            <input placeholder="{{ $placeholder }}" type="{{ $type }}" value="{{ $value ?? request()->$name ?? '' }}" name="{{ $name }}" id="{{ $customId ? ('field__custom-'.$id) : $id }}" class="{{ $classField }} form-control" {{ $disabled ? 'disabled' : '' }} {{ $required ? 'required' : '' }} {{ $min ? 'min='.$min : '' }} {{ $max ? 'max='.$max : '' }}
                {!! $propertyField !!}
            >
        @endif
    </div>
    @if ($errors)
        <div class="d-block">
            @foreach ((array) $errors as $error)
                <span class="error d-block">{{ $error }}</span>
            @endforeach
        </div>
    @endif
</div>
<script>
    function isNumberKey(evt, element) {
  var charCode = (evt.which) ? evt.which : event.keyCode
  if (charCode > 31 && (charCode < 48 || charCode > 57) && !(charCode == 46 || charCode == 8))
    return false;
  else {
    var len = $(element).val().length;
    var index = $(element).val().indexOf('.');
    if (index > 0 && charCode == 46) {
      return false;
    }
    if (index > 0) {
      var CharAfterdot = (len + 1) - index;
      if (CharAfterdot > 3) {
        return false;
      }
    }

  }
  return true;
}
</script>
