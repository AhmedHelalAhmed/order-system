@component('mail::message')
    <h2>Dear Merchant,</h2>
    <p>
        The stock of the following ingredient(s) below the percentage level {{ $percentage }}
    </p>
    <ul>
        @foreach($ingredients as $ingredient)
            <li>{{ $ingredient }}</li>
        @endforeach
    </ul>

@endcomponent
