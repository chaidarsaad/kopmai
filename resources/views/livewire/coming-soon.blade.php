@section('title')
    Coming Soon
@endsection

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-100 to-purple-100">
    <div class="max-w-md w-full mx-auto p-8">
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
            <!-- Store Icon -->
            <div
                class="w-20 h-20 bg-blue-500 rounded-2xl mx-auto mb-6 flex items-center justify-center transform rotate-3">
                <svg class="w-10 h-10 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>

            <!-- Content -->
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Coming Soon!</h1>
            <p class="text-gray-600 mb-8">We're working hard to bring you something amazing. Stay tuned!</p>

            <!-- Progress Bar -->
            <div class="w-full bg-gray-200 rounded-full h-2 mb-8">
                <div class="bg-blue-500 h-2 rounded-full w-3/4 animate-pulse"></div>
            </div>

            <!-- Additional Text -->
            <div class="text-sm text-gray-500">
                <p class="mb-2">🚀 Exciting features are on the way</p>
                <p>⭐ We'll be launching soon!</p>
            </div>

            <!-- Register Button - Only shown if user count is 0 -->
            @if (\App\Models\User::count() === 0)
                <div class="mt-8">
                    <a href="{{ route('register') }}" wire:navigate
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        Register Admin
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
