@php
    $phone = $siteSettings?->contact_phone ?: '+1 (347) 562-3532';
    $email = $siteSettings?->contact_email ?: 'info@artstoryglobal.com';
    $whatsappPhone = preg_replace('/\D+/', '', $siteSettings?->whatsapp_phone ?: $phone);
    $headOffice = $siteSettings?->head_office_address ?: $siteSettings?->address ?: '8041 Commonwealth Blvd, Bellerose, NY 11426, USA';
    $newWorkOffice = $siteSettings?->new_work_office_address;
@endphp

<x-artstory.layout :site-settings="$siteSettings" title="Contact" active="contact">
    <main class="mx-auto max-w-7xl px-6 py-16">
        <p class="text-center text-xs font-black uppercase tracking-[0.24em] text-gray-500">{{ $siteSettings?->contact_eyebrow ?: 'Contact' }}</p>
        <h1 class="mb-4 text-center font-display text-4xl font-light text-black">{{ $siteSettings?->contact_title ?: 'Get in Touch' }}</h1>
        @if($siteSettings?->contact_description)<p class="mx-auto mb-12 max-w-2xl text-center text-gray-600">{{ $siteSettings->contact_description }}</p>@else<div class="mb-12"></div>@endif
        <div class="grid grid-cols-1 gap-12 lg:grid-cols-2">
            <div class="bg-gray-100 p-8 md:p-12">
                <h2 class="mb-6 font-display text-2xl font-light text-black">Contact Information</h2>
                <div class="mb-6 flex items-start gap-4"><span class="material-symbols-rounded text-[#88884d]">location_on</span><div><p class="mb-1 text-xs font-black uppercase tracking-[0.16em] text-gray-500">Head Office</p><p class="whitespace-pre-line text-gray-700">{{ $headOffice }}</p></div></div>
                @if ($newWorkOffice)
                    <div class="mb-6 flex items-start gap-4"><span class="material-symbols-rounded text-[#88884d]">business</span><div><p class="mb-1 text-xs font-black uppercase tracking-[0.16em] text-gray-500">New Work Office</p><p class="whitespace-pre-line text-gray-700">{{ $newWorkOffice }}</p></div></div>
                @endif
                <div class="mb-6 flex items-start gap-4"><span class="material-symbols-rounded text-[#88884d]">call</span><p class="text-gray-700">{{ $phone }}</p></div>
                <div class="mb-6 flex items-start gap-4"><span class="material-symbols-rounded text-[#88884d]">alternate_email</span><p class="text-gray-700">{{ $email }}</p></div>
                @if ($whatsappPhone)
                    <a href="https://wa.me/{{ $whatsappPhone }}?text={{ rawurlencode('Hello ART Story, I would like to inquire about artworks and artists.') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 bg-black px-8 py-3 text-sm font-bold text-white transition hover:bg-gray-800">
                        <svg class="size-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2C6.53 2 2.06 6.47 2.06 11.98c0 1.76.46 3.47 1.34 4.98L2 22l5.17-1.36a9.94 9.94 0 0 0 4.87 1.27h.01c5.5 0 9.97-4.47 9.97-9.98A9.97 9.97 0 0 0 12.04 2Zm0 18.22h-.01a8.23 8.23 0 0 1-4.2-1.15l-.3-.18-3.07.81.82-2.99-.2-.31a8.23 8.23 0 1 1 6.96 3.82Zm4.52-6.16c-.25-.13-1.47-.72-1.7-.8-.23-.08-.4-.13-.56.13-.17.25-.65.8-.8.96-.15.17-.3.19-.55.06a6.73 6.73 0 0 1-1.98-1.22 7.42 7.42 0 0 1-1.37-1.7c-.14-.25-.02-.39.1-.52.1-.1.25-.27.37-.4.13-.15.17-.25.25-.42.08-.16.04-.3-.02-.42-.06-.13-.56-1.34-.77-1.83-.2-.48-.41-.41-.56-.42h-.48c-.17 0-.43.06-.66.3-.23.26-.87.84-.87 2.03s.9 2.35 1.02 2.51c.13.17 1.76 2.69 4.26 3.77.6.25 1.06.4 1.42.51.6.19 1.15.16 1.59.1.48-.07 1.47-.6 1.68-1.18.2-.58.2-1.07.14-1.18-.06-.1-.22-.16-.46-.28Z"/></svg>
                        WhatsApp
                    </a>
                @endif
            </div>
            <div class="p-4">
                <h2 class="mb-6 font-display text-2xl font-light text-black">{{ $siteSettings?->contact_form_title ?: 'Send us a message' }}</h2>
                @if (session('contact_message_sent'))
                    <p class="mb-6 border border-green-200 bg-green-50 p-4 text-sm font-semibold text-green-800">{{ $siteSettings?->contact_form_success_message ?: 'Thank you. Your message has been received and our team will be in touch shortly.' }}</p>
                @endif
                <form action="{{ route('contact.store') }}" method="POST">
                    @csrf
                    <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                        <input name="name" value="{{ old('name') }}" placeholder="Full Name" class="w-full border border-gray-300 p-3 outline-none focus:border-gray-900" required>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" class="w-full border border-gray-300 p-3 outline-none focus:border-gray-900" required>
                    </div>
                    <input name="subject" value="{{ old('subject') }}" placeholder="Subject" class="mb-6 w-full border border-gray-300 p-3 outline-none focus:border-gray-900" required>
                    <textarea name="message" placeholder="Message" class="mb-8 h-32 w-full border border-gray-300 p-3 outline-none focus:border-gray-900" required>{{ old('message') }}</textarea>
                    @if ($errors->any())<p class="mb-5 text-sm font-semibold text-red-700">Please complete all fields with valid information.</p>@endif
                    <button type="submit" class="w-full bg-black px-8 py-3 text-sm font-bold text-white transition hover:bg-gray-800">{{ $siteSettings?->contact_form_submit_label ?: 'Submit Message' }}</button>
                </form>
            </div>
        </div>
    </main>
</x-artstory.layout>
