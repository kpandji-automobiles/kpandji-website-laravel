{{-- Champ de formulaire du design system (libellé flottant). type : text, email, tel, date, textarea, select, file. --}}
@props(['name', 'label', 'type' => 'text', 'required' => false, 'options' => [], 'value' => null, 'placeholder' => null, 'help' => null, 'max' => null, 'accept' => null, 'error' => null])
@php
  $id = 'kp-'.str_replace(['[', ']', '_'], '-', $name);
  $current = old($name, $value);
@endphp
<div {{ $attributes->class(['kp-field', 'kp-field--error' => filled($error), 'kp-field--file' => $type === 'file']) }}>
  @if($type === 'textarea')
    <textarea class="kp-field__control" id="{{ $id }}" name="{{ $name }}" rows="5" @if($required) required @endif @if($max) maxlength="{{ $max }}" @endif @if($placeholder) placeholder="{{ $placeholder }}" @endif>{{ $current }}</textarea>
  @elseif($type === 'select')
    <select class="kp-field__control" id="{{ $id }}" name="{{ $name }}" @if($required) required @endif>
      @if($placeholder)<option value="">{{ $placeholder }}</option>@endif
      @foreach($options as $optionValue => $optionLabel)
        <option value="{{ $optionValue }}" @selected((string) $current === (string) $optionValue)>{{ $optionLabel }}</option>
      @endforeach
    </select>
  @else
    <input class="kp-field__control" id="{{ $id }}" type="{{ $type }}" name="{{ $name }}" @if($type !== 'file') value="{{ $current }}" @endif @if($required) required @endif @if($max) maxlength="{{ $max }}" @endif @if($placeholder) placeholder="{{ $placeholder }}" @endif @if($accept) accept="{{ $accept }}" @endif>
  @endif
  <label class="kp-field__label" for="{{ $id }}">{{ $label }}@if(! $required && $type !== 'file') <span class="kp-muted">(facultatif)</span>@endif</label>
  @if(filled($error) || filled($help))<p class="kp-field__help">{{ $error ?: $help }}</p>@endif
</div>
