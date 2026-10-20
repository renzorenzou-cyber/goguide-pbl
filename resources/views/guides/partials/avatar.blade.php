@if($guide->photo_url)
    <img src="{{ $guide->photo_url }}" alt="{{ $guide->user->name }}"
         class="{{ $size }} rounded-full object-cover guide-avatar-ring shrink-0">
@else
    <div class="{{ $size }} rounded-full guide-avatar-ring shrink-0 bg-brand-blue text-white flex items-center justify-center font-extrabold">
        {{ $guide->initials }}
    </div>
@endif