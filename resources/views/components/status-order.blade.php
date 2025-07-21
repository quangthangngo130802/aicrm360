@props(['status']) {{-- $status là OrderStatus enum --}}

<span class="{{ $status->class() }}">{{ $status->label() }}</span>
