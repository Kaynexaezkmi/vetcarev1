@extends('layouts.website')

@section('title', 'Our Services - VetCare')

@php
    $normalizeServiceName = fn (string $name): string => preg_replace('/[^a-z0-9]/', '', strtolower($name));

    $serviceCatalog = $serviceCatalog ?? [
        [
            'key' => 'grooming',
            'title' => 'Grooming',
            'description' => 'Professional veterinary service for your beloved pets.',
            'intro' => 'Our grooming service keeps your pet clean, healthy, and looking their best. We use gentle, pet-safe products and professional techniques to ensure your furry friend feels comfortable and cared for.',
            'image' => 'images/services/1774290704_istockphoto-1421577226-612x612.jpg',
            'layout' => 'grooming',
            'features' => [
                ['title' => 'Professional Care', 'text' => 'Handled by experienced groomers.'],
                ['title' => 'Safe Products', 'text' => 'We use pet-safe and gentle products.'],
                ['title' => 'Stress-Free', 'text' => 'A calm and comfortable environment.'],
            ],
            'catPricing' => [
                ['name' => 'Full Groom', 'details' => 'Bath, haircut, nail trim, ear cleaning, anal gland expression, paw pad trim.', 'price' => '₱700'],
                ['name' => 'Basic Groom', 'details' => 'Bath, blow dry, nail trim, ear cleaning.', 'price' => '₱550'],
            ],
            'pricing' => [
                ['size' => 'Small', 'weight' => 'Up to 10 kg', 'breeds' => 'Shih Tzu, Pomeranian, Chihuahua, etc.', 'basic' => '₱600', 'full' => '₱850'],
                ['size' => 'Medium', 'weight' => '10 - 20 kg', 'breeds' => 'Beagle, Cocker Spaniel, French Bulldog, etc.', 'basic' => '₱800', 'full' => '₱1,100'],
                ['size' => 'Large', 'weight' => '20 kg & above', 'breeds' => 'Golden Retriever, Husky, Labrador, etc.', 'basic' => '₱1,100', 'full' => '₱1,500'],
            ],
        ],
        [
            'key' => 'deworming',
            'title' => 'Deworming',
            'description' => 'Professional veterinary service for your beloved pets.',
            'intro' => 'Regular deworming helps protect your pet from internal parasites and supports their overall health and wellbeing.',
            'image' => 'images/services/1774290704_istockphoto-1421577226-612x612.jpg',
            'layout' => 'price-list',
            'sectionTitle' => 'Deworming',
            'sectionSubtitle' => 'Price is based on your pet\'s weight.',
            'features' => [
                ['title' => 'Prevents Parasites', 'text' => 'Helps eliminate and prevent worms effectively.'],
                ['title' => 'Safe & Effective', 'text' => 'Vet-recommended dewormers for your pet\'s safety.'],
                ['title' => 'Better Health', 'text' => 'Supports digestion, immunity, and overall health.'],
            ],
            'pricing' => [
                ['label' => '1 - 2 kg', 'price' => '₱250'],
                ['label' => '2 - 5 kg', 'price' => '₱300'],
                ['label' => '+ 5 kg', 'price' => '+ ₱50 every 5 kg'],
            ],
            'note' => 'Add ₱50 for every additional 5 kg of body weight.',
        ],
        [
            'key' => 'surgery',
            'title' => 'Surgery',
            'description' => 'Professional veterinary service for your beloved pets.',
            'intro' => 'Our surgical services are performed with expert care and advanced techniques to ensure your pet\'s safety and fast recovery.',
            'image' => 'images/services/1774290704_istockphoto-1421577226-612x612.jpg',
            'layout' => 'option-list',
            'sectionTitle' => 'Surgical Services',
            'sectionSubtitle' => 'Choose the type of surgery to learn more and book.',
            'features' => [
                ['title' => 'Safe & Sterile Environment', 'text' => 'All surgeries are performed in a fully equipped and sterile facility.'],
                ['title' => 'Experienced Veterinarians', 'text' => 'Our skilled vets ensure the best care before, during, and after surgery.'],
                ['title' => 'Comfort & Recovery', 'text' => 'We prioritize your pet\'s comfort and a smooth, stress-free recovery.'],
            ],
            'options' => [
                ['name' => 'Major Surgery', 'details' => 'Complex surgical procedures that require general anesthesia and extended recovery time. Performed by experienced veterinarians with advanced monitoring.'],
                ['name' => 'Minor Surgery', 'details' => 'Routine surgical procedures that are less complex and have shorter recovery time. Safe and effective treatments for common health issues.'],
            ],
        ],
        [
            'key' => 'consultation',
            'title' => 'Consultation',
            'description' => 'Professional veterinary service for your beloved pets.',
            'intro' => 'Get professional advice and guidance from our veterinarians for your pet\'s health and concerns.',
            'image' => 'images/services/1774290704_istockphoto-1421577226-612x612.jpg',
            'layout' => 'single-price',
            'sectionTitle' => 'Consultation Fee',
            'sectionSubtitle' => 'Includes physical exam and professional advice.',
            'price' => '₱500',
            'features' => [
                ['title' => 'Expert Advice', 'text' => 'Accurate diagnosis and health recommendations.'],
                ['title' => 'Health Monitoring', 'text' => 'Regular check-ups for a healthy, happy pet.'],
                ['title' => 'Personalized Care', 'text' => 'Tailored treatment and care plans for your pet.'],
            ],
            'includedTitle' => 'What\'s Included',
            'included' => ['Physical examination', 'Health assessment', 'Diet & care recommendations', 'Treatment plan if needed'],
        ],
        [
            'key' => 'x-ray',
            'title' => 'X-Ray',
            'description' => 'Professional veterinary service for your beloved pets.',
            'intro' => 'Digital X-ray helps us see what\'s inside your pet\'s body to accurately diagnose and treat conditions.',
            'image' => 'images/services/1774290704_istockphoto-1421577226-612x612.jpg',
            'layout' => 'single-price',
            'sectionTitle' => 'X-Ray Service',
            'sectionSubtitle' => 'Includes digital x-ray and veterinarian interpretation.',
            'price' => '₱1,500',
            'features' => [
                ['title' => 'Accurate Diagnosis', 'text' => 'Helps detect fractures, foreign objects, and internal issues.'],
                ['title' => 'Fast & Safe', 'text' => 'Quick procedure with minimal stress for your pet.'],
                ['title' => 'Advanced Equipment', 'text' => 'Digital X-ray for clear and precise results.'],
            ],
            'includedTitle' => 'Common Uses',
            'included' => ['Fractures & bone issues', 'Chest & lung problems', 'Abdominal concerns', 'Foreign object detection'],
        ],
        [
            'key' => 'cbc',
            'title' => 'CBC',
            'description' => 'Professional veterinary service for your beloved pets.',
            'intro' => 'CBC helps assess your pet\'s overall health by checking the different components of their blood.',
            'image' => 'images/services/1774290704_istockphoto-1421577226-612x612.jpg',
            'layout' => 'single-price',
            'sectionTitle' => 'CBC Test',
            'sectionSubtitle' => 'Includes blood extraction and laboratory analysis.',
            'price' => '₱1,000',
            'features' => [
                ['title' => 'Health Screening', 'text' => 'Detects infections, anemia, and other conditions.'],
                ['title' => 'Early Detection', 'text' => 'Helps catch health issues early for better treatment.'],
                ['title' => 'Reliable Results', 'text' => 'Accurate and quick laboratory analysis.'],
            ],
            'includedTitle' => 'Tests Include',
            'included' => ['Red Blood Cells (RBC)', 'White Blood Cells (WBC)', 'Hemoglobin / Hematocrit', 'Platelets'],
        ],
        [
            'key' => 'confinement',
            'title' => 'Confinement',
            'description' => 'Professional veterinary service for your beloved pets.',
            'intro' => 'Safe, clean, and comfortable care for pets that need close monitoring or recovery support.',
            'image' => 'images/services/1774290704_istockphoto-1421577226-612x612.jpg',
            'layout' => 'option-list',
            'sectionTitle' => 'Confinement Service',
            'sectionSubtitle' => 'For pets that need supervised care during treatment or recovery.',
            'features' => [
                ['title' => 'Safe & Secure Environment', 'text' => 'Our facilities are secure, clean, and monitored for your pet\'s safety.'],
                ['title' => 'Comfort & Care', 'text' => 'We provide cozy spaces, clean bedding, fresh food, and attentive care.'],
                ['title' => 'Supervised by Professionals', 'text' => 'Our trained staff and veterinarians closely monitor your pet\'s wellbeing.'],
            ],
            'options' => [
                ['name' => 'Confinement', 'details' => 'Special care and restricted activity for pets that need close monitoring, treatment support, or recovery time.'],
            ],
            'note' => 'Bring your pet\'s food, bedding, and essentials for a more comfortable stay.',
        ],
        [
            'key' => 'vaccination',
            'title' => 'Vaccination',
            'description' => 'Professional veterinary service for your beloved pets.',
            'intro' => 'Vaccination helps protect your pet from common diseases and supports long-term health.',
            'image' => 'images/services/1774290704_istockphoto-1421577226-612x612.jpg',
            'layout' => 'option-list',
            'sectionTitle' => 'Vaccination Services',
            'sectionSubtitle' => 'Choose your vaccination type.',
            'features' => [
                ['title' => 'Disease Protection', 'text' => 'Helps prevent serious and contagious diseases.'],
                ['title' => 'Safe & Effective', 'text' => 'We only use high-quality and vet-approved vaccines.'],
                ['title' => 'Healthy & Active', 'text' => 'Keeps your pet strong, energetic, and full of life.'],
            ],
            'options' => [
                ['name' => '5in1 Vaccine', 'details' => 'Protects against Feline Panleukopenia, Calicivirus, Herpesvirus, Rhinotracheitis, and Chlamydophila.', 'price' => '₱500'],
                ['name' => 'Anti Rabies Vaccine', 'details' => 'Protects your pet from rabies virus and other related risks.', 'price' => '₱350'],
                ['name' => 'Kennel Cough Vaccine', 'details' => 'Helps protect against Bordetella bronchiseptica, a common cause of respiratory infection.', 'price' => '₱750'],
            ],
        ],
    ];

    $serviceCatalog = collect($serviceCatalog)->map(function ($item) use ($services, $normalizeServiceName) {
        $catalogName = $normalizeServiceName($item['title']);
        $catalogKey = $normalizeServiceName($item['key']);
        $record = $services->first(function ($service) use ($catalogName, $catalogKey, $normalizeServiceName) {
            $serviceName = $normalizeServiceName($service->name);

            return $serviceName === $catalogName
                || str_contains($serviceName, $catalogKey)
                || str_contains($catalogName, $serviceName);
        });

        if ($record?->image && file_exists(public_path($record->image))) {
            $item['image'] = $record->image;
        }

        $item['bookingUrl'] = auth()->check() && ! auth()->user()->isAdmin()
            ? route('appointments.create', array_filter(['service_id' => $record?->id]))
            : null;

        return $item;
    })->values();
@endphp

@section('content')
<section class="bg-white py-10 sm:py-14">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach($serviceCatalog as $service)
                <article class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <button
                        type="button"
                        class="service-card-trigger block w-full text-left"
                        data-service-key="{{ $service['key'] }}"
                    >
                        @if($service['image'] && file_exists(public_path($service['image'])))
                            <img src="{{ asset($service['image']) }}" alt="{{ $service['title'] }}" class="h-44 w-full object-cover">
                        @else
                            <span class="flex h-44 items-center justify-center bg-gradient-to-br from-orange-100 via-orange-100 to-orange-200">
                                <svg class="h-16 w-16 text-orange-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.32 6.32a4.5 4.5 0 0 1 6.36 0L12 7.64l1.32-1.32a4.5 4.5 0 0 1 6.36 6.36L12 20.36l-7.68-7.68a4.5 4.5 0 0 1 0-6.36Z" />
                                </svg>
                            </span>
                        @endif
                    </button>

                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-950">{{ $service['title'] }}</h2>
                        <p class="mt-3 max-w-sm text-lg leading-7 text-slate-600">{{ $service['description'] }}</p>

                        <div class="mt-5 border-t border-gray-950 pt-4 text-right">
                            <button
                                type="button"
                                class="service-card-trigger inline-flex items-center justify-center rounded-lg bg-orange-500 px-5 py-2.5 text-base font-semibold text-white transition hover:bg-orange-600"
                                data-service-key="{{ $service['key'] }}"
                            >
                                Book Now
                            </button>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

<div id="service-detail-modal" class="fixed inset-0 z-[70] hidden items-start justify-center overflow-y-auto bg-gray-950/65 px-4 py-6 sm:py-8" aria-hidden="true">
    <div class="relative w-full max-w-4xl rounded-2xl bg-white p-5 shadow-2xl sm:p-6 lg:p-8">
        <button type="button" id="close-service-detail-modal" class="absolute right-4 top-4 rounded-full p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-700" aria-label="Close service details">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18" />
            </svg>
        </button>

        <div class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
            <div>
                <h2 id="modal-service-title" class="pr-10 text-3xl font-bold tracking-tight text-gray-950"></h2>
                <p id="modal-service-intro" class="mt-4 text-sm leading-6 text-gray-700"></p>

                <div class="mt-5 overflow-hidden rounded-xl bg-orange-50">
                    <img id="modal-service-image" src="" alt="" class="h-52 w-full object-cover">
                </div>

                <div id="modal-feature-list" class="mt-5 space-y-4"></div>
            </div>

            <div class="flex min-h-full flex-col">
                <div id="modal-main-content" class="flex-1"></div>

                <div class="mt-6 flex justify-end">
                    @auth
                        @if(Auth::user()->isAdmin())
                        <button type="button" class="inline-flex w-full cursor-not-allowed items-center justify-center rounded-lg bg-gray-300 px-7 py-3 text-base font-bold text-white sm:w-auto sm:min-w-60" disabled>
                            Customer Booking Only
                        </button>
                        @else
                        <a id="modal-book-link" href="{{ route('appointments.create') }}" class="inline-flex w-full items-center justify-center rounded-lg bg-orange-500 px-7 py-3 text-base font-bold text-white shadow-lg shadow-orange-500/20 transition hover:bg-orange-600 sm:w-auto sm:min-w-60">
                            Book This Service
                        </a>
                        @endif
                    @else
                        <button type="button" id="modal-guest-book-button" class="inline-flex w-full items-center justify-center rounded-lg bg-orange-500 px-7 py-3 text-base font-bold text-white shadow-lg shadow-orange-500/20 transition hover:bg-orange-600 sm:w-auto sm:min-w-60">
                            Book This Service
                        </button>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>

@guest
<div id="guest-service-booking-modal" class="fixed inset-0 z-[80] hidden items-center justify-center bg-gray-950/65 px-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-2xl">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h3 class="text-2xl font-bold text-gray-950">Book an Appointment</h3>
                <p class="mt-3 text-gray-600">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-semibold text-orange-500 hover:text-orange-600">Sign in</a>.
                    If not,
                    <a href="{{ route('register') }}" class="font-semibold text-orange-500 hover:text-orange-600">register</a>.
                </p>
            </div>
            <button type="button" id="close-guest-service-booking-modal" class="rounded-full p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-700" aria-label="Close booking prompt">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18" />
                </svg>
            </button>
        </div>
        <div class="mt-6 flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('login') }}" class="inline-flex flex-1 items-center justify-center rounded-xl bg-orange-500 px-5 py-3 font-semibold text-white transition hover:bg-orange-600">
                Sign In
            </a>
            <a href="{{ route('register') }}" class="inline-flex flex-1 items-center justify-center rounded-xl border border-gray-200 px-5 py-3 font-semibold text-gray-700 transition hover:bg-gray-50">
                Register
            </a>
        </div>
    </div>
</div>
@endguest

<section id="inquiry" class="bg-orange-500 py-20">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        <div class="mb-12 text-center">
            <h2 class="text-3xl font-bold text-white lg:text-4xl">Have Questions?</h2>
            <p class="mt-4 text-lg text-orange-100">Send us a message and we'll get back to you as soon as possible.</p>
        </div>

        <form action="{{ route('inquiry.store') }}" method="POST" class="rounded-2xl bg-white p-8 shadow-2xl">
            @csrf
            <div class="mb-6 grid gap-6 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Your Name</label>
                    <input type="text" name="name" required class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-orange-500 focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" name="email" required class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-orange-500 focus:ring-2 focus:ring-orange-500">
                </div>
            </div>
            <div class="mb-6">
                <label class="mb-2 block text-sm font-medium text-gray-700">Phone Number</label>
                <div class="flex items-center">
                    <div class="flex-shrink-0 rounded-l-xl border border-r-0 border-gray-300 bg-gray-100 px-3 py-3">
                        <span class="font-medium text-gray-500">+63</span>
                    </div>
                    <input type="tel" name="phone" placeholder=" " pattern="[0-9]{11}" maxlength="11" class="flex-1 rounded-r-xl border border-gray-300 px-4 py-3 focus:border-orange-500 focus:ring-2 focus:ring-orange-500">
                </div>
                <p class="mt-1 text-xs text-gray-500">Enter 11-digit mobile number</p>
            </div>
            <div class="mb-6">
                <label class="mb-2 block text-sm font-medium text-gray-700">Your Message</label>
                <textarea name="message" rows="4" required class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-orange-500 focus:ring-2 focus:ring-orange-500"></textarea>
            </div>
            <button type="submit" class="w-full rounded-xl bg-orange-500 py-4 font-semibold text-white transition hover:bg-orange-600">
                Send Message
            </button>
        </form>
    </div>
</section>

@include('home.partials.inquiry-success-modal')
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const services = @json($serviceCatalog);
        const servicesByKey = services.reduce(function (items, service) {
            items[service.key] = service;

            return items;
        }, {});

        const modal = document.getElementById('service-detail-modal');
        const closeButton = document.getElementById('close-service-detail-modal');
        const title = document.getElementById('modal-service-title');
        const intro = document.getElementById('modal-service-intro');
        const image = document.getElementById('modal-service-image');
        const features = document.getElementById('modal-feature-list');
        const content = document.getElementById('modal-main-content');
        const bookLink = document.getElementById('modal-book-link');
        const guestBookButton = document.getElementById('modal-guest-book-button');
        const guestModal = document.getElementById('guest-service-booking-modal');
        const closeGuestButton = document.getElementById('close-guest-service-booking-modal');

        function iconMarkup() {
            return '<span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-orange-50 text-gray-700"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-7-4.35-7-10a4 4 0 0 1 7-2.65A4 4 0 0 1 19 11c0 5.65-7 10-7 10Z" /></svg></span>';
        }

        function featureMarkup(feature) {
            return '<div class="flex items-start gap-3">' + iconMarkup() + '<div><h3 class="text-sm font-bold text-gray-950">' + feature.title + '</h3><p class="mt-1 text-sm leading-5 text-gray-600">' + feature.text + '</p></div></div>';
        }

        function groomingTabClass(type, selectedType) {
            if (type === selectedType) {
                return 'rounded-lg border border-orange-300 bg-white px-5 py-3 text-sm font-bold text-orange-500';
            }

            return 'rounded-lg bg-orange-50 px-5 py-3 text-sm font-bold text-gray-950';
        }

        function catIconMarkup() {
            return '<div class="flex h-16 w-16 items-center justify-center rounded-lg bg-orange-50 text-orange-500"><svg class="h-9 w-9" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 9 7 4l4 3h2l4-3 2 5v4c0 4-3 7-7 7s-7-3-7-7V9Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h.01M15 12h.01M10 16c1.25.8 2.75.8 4 0" /></svg></div>';
        }

        function dogIconMarkup() {
            return '<div class="flex h-16 w-16 items-center justify-center rounded-lg bg-orange-50 text-orange-500"><svg class="h-9 w-9" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 11c0-3.5 2.5-6 7-6s7 2.5 7 6c0 5-3 8-7 8s-7-3-7-8Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M9 10h.01M15 10h.01M10 14c1.25 1 2.75 1 4 0" /></svg></div>';
        }

        function renderGrooming(service, selectedType = 'cat') {
            const isCat = selectedType === 'cat';
            const tabTitle = isCat ? 'Cat Grooming' : 'Dog Grooming';
            const tabDescription = isCat ? 'Choose your cat grooming type.' : 'Choose your dog\'s size to see the available options.';
            const rows = isCat
                ? service.catPricing.map(function (item) {
                    return '<div class="grid gap-3 rounded-lg border border-gray-200 p-3 sm:grid-cols-[64px_1fr_130px] sm:items-center">' +
                        catIconMarkup() +
                        '<div><h4 class="text-base font-bold text-gray-950">' + item.name + '</h4><p class="mt-1 text-sm leading-5 text-gray-600">' + item.details + '</p></div>' +
                        '<div class="border-gray-200 text-sm sm:border-l sm:pl-4"><strong class="block text-right text-xl font-bold text-orange-500">' + item.price + '</strong></div>' +
                        '</div>';
                }).join('')
                : service.pricing.map(function (item) {
                    return '<div class="grid gap-3 rounded-lg border border-gray-200 p-3 sm:grid-cols-[64px_1fr_130px] sm:items-center">' +
                        dogIconMarkup() +
                        '<div><h4 class="text-base font-bold text-gray-950">' + item.size + '</h4><p class="mt-0.5 text-sm font-semibold text-gray-700">(' + item.weight + ')</p><p class="mt-1 text-sm leading-5 text-gray-600">' + item.breeds + '</p></div>' +
                        '<div class="border-gray-200 text-sm sm:border-l sm:pl-4"><div class="flex justify-between gap-3"><span class="text-gray-600">Basic Groom</span><strong class="text-orange-500">' + item.basic + '</strong></div><div class="mt-3 flex justify-between gap-3"><span class="text-gray-600">Full Groom</span><strong class="text-orange-500">' + item.full + '</strong></div></div>' +
                        '</div>';
                }).join('');

            return [
                '<div class="grid grid-cols-2 gap-3">',
                '<button type="button" class="grooming-tab ' + groomingTabClass('cat', selectedType) + '" data-grooming-type="cat">Cat</button>',
                '<button type="button" class="grooming-tab ' + groomingTabClass('dog', selectedType) + '" data-grooming-type="dog">Dog</button>',
                '</div>',
                '<div class="mt-5">',
                '<h3 class="text-lg font-bold text-gray-950">' + tabTitle + '</h3>',
                '<p class="mt-1 text-sm text-gray-500">' + tabDescription + '</p>',
                '<div class="mt-4 space-y-3">',
                rows,
                '</div>',
                '</div>',
            ].join('');
        }

        function bindGroomingTabs(service) {
            content.querySelectorAll('.grooming-tab').forEach(function (tab) {
                tab.addEventListener('click', function () {
                    content.innerHTML = renderGrooming(service, tab.dataset.groomingType);
                    bindGroomingTabs(service);
                });
            });
        }

        function renderPriceList(service) {
            return [
                '<div>',
                '<h3 class="text-lg font-bold text-gray-950">' + service.sectionTitle + '</h3>',
                '<p class="mt-2 text-sm leading-5 text-gray-600">' + service.sectionSubtitle + '</p>',
                '<div class="mt-8 divide-y divide-gray-200 rounded-lg border border-gray-100 bg-white">',
                service.pricing.map(function (item) {
                    return '<div class="flex items-center justify-between gap-4 px-4 py-5"><span class="text-sm font-medium text-gray-700">' + item.label + '</span><strong class="text-base text-orange-500">' + item.price + '</strong></div>';
                }).join(''),
                '</div>',
                service.note ? '<div class="mt-5 rounded-lg bg-orange-50 p-4 text-sm text-gray-700"><strong class="block text-gray-950">Note</strong>' + service.note + '</div>' : '',
                '</div>',
            ].join('');
        }

        function renderSinglePrice(service) {
            return [
                '<div class="grid gap-7 md:grid-cols-[1fr_1fr]">',
                '<div class="rounded-xl border border-gray-100 p-6">',
                '<h3 class="text-lg font-bold text-gray-950">' + service.sectionTitle + '</h3>',
                '<p class="mt-3 text-sm leading-5 text-gray-600">' + service.sectionSubtitle + '</p>',
                '<p class="mt-10 text-center text-3xl font-bold text-orange-500">' + service.price + '</p>',
                '</div>',
                '<div><h3 class="text-lg font-bold text-gray-950">' + service.includedTitle + '</h3><ul class="mt-5 space-y-3">' + service.included.map(function (item) {
                    return '<li class="flex items-center gap-3 text-sm text-gray-700"><span class="flex h-5 w-5 items-center justify-center rounded-full bg-green-50 text-green-500"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" /></svg></span>' + item + '</li>';
                }).join('') + '</ul></div>',
                '</div>',
            ].join('');
        }

        function renderOptionList(service) {
            return [
                '<div>',
                '<h3 class="text-lg font-bold text-gray-950">' + service.sectionTitle + '</h3>',
                '<p class="mt-2 text-sm leading-5 text-gray-600">' + service.sectionSubtitle + '</p>',
                '<div class="mt-6 space-y-4">',
                service.options.map(function (item) {
                    const hasPrice = Boolean(item.price);
                    const gridClass = hasPrice ? 'sm:grid-cols-[64px_1fr_100px]' : 'sm:grid-cols-[64px_1fr_28px]';
                    const trailingContent = hasPrice
                        ? '<strong class="text-right text-xl font-bold text-orange-500">' + item.price + '</strong>'
                        : '<span class="hidden text-gray-400 sm:block"><svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m9 5 7 7-7 7" /></svg></span>';

                    return '<div class="grid gap-3 rounded-lg border border-gray-200 p-4 ' + gridClass + ' sm:items-center">' +
                        '<div class="flex h-16 w-16 items-center justify-center rounded-full bg-orange-50 text-orange-500"><svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3 4 7v6c0 4.5 3.2 7.5 8 8 4.8-.5 8-3.5 8-8V7l-8-4Z" /><path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-5" /></svg></div>' +
                        '<div><h4 class="text-base font-bold text-gray-950">' + item.name + '</h4><p class="mt-2 text-sm leading-6 text-gray-600">' + item.details + '</p></div>' +
                        trailingContent +
                        '</div>';
                }).join(''),
                '</div>',
                service.note ? '<div class="mt-5 border-t border-gray-100 pt-4 text-sm leading-6 text-gray-600">' + service.note + '</div>' : '',
                '</div>',
            ].join('');
        }

        function openModal(service) {
            title.textContent = service.title;
            intro.textContent = service.intro;
            image.src = '/' + service.image;
            image.alt = service.title;
            features.innerHTML = service.features.map(featureMarkup).join('');

            if (service.layout === 'grooming') {
                content.innerHTML = renderGrooming(service);
                bindGroomingTabs(service);
            } else if (service.layout === 'price-list') {
                content.innerHTML = renderPriceList(service);
            } else if (service.layout === 'option-list') {
                content.innerHTML = renderOptionList(service);
            } else {
                content.innerHTML = renderSinglePrice(service);
            }

            if (bookLink && service.bookingUrl) {
                bookLink.href = service.bookingUrl;
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
        }

        function openGuestModal() {
            if (!guestModal) {
                return;
            }

            guestModal.classList.remove('hidden');
            guestModal.classList.add('flex');
        }

        function closeGuestModal() {
            if (!guestModal) {
                return;
            }

            guestModal.classList.add('hidden');
            guestModal.classList.remove('flex');
        }

        document.querySelectorAll('.service-card-trigger').forEach(function (trigger) {
            trigger.addEventListener('click', function () {
                const service = servicesByKey[trigger.dataset.serviceKey];

                if (service) {
                    openModal(service);
                }
            });
        });

        closeButton.addEventListener('click', closeModal);
        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        if (guestBookButton) {
            guestBookButton.addEventListener('click', openGuestModal);
        }

        if (closeGuestButton) {
            closeGuestButton.addEventListener('click', closeGuestModal);
        }

        if (guestModal) {
            guestModal.addEventListener('click', function (event) {
                if (event.target === guestModal) {
                    closeGuestModal();
                }
            });
        }

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') {
                return;
            }

            closeGuestModal();

            if (!modal.classList.contains('hidden')) {
                closeModal();
            }
        });
    });
</script>
@endpush
