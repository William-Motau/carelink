@php
    $consent = request()->cookie('cookie_consent');
    $showBanner = !$consent;
@endphp

@if($showBanner)
<div 
    x-data="cookieConsent()" 
    x-show="showBanner" 
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-4"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform translate-y-4"
    class="fixed bottom-0 left-0 right-0 z-50"
    role="dialog"
    aria-label="Cookie consent"
>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="bg-white border-t border-gray-200 shadow-lg rounded-t-lg p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">We value your privacy</h3>
                    <p class="text-sm text-gray-600">
                        We use cookies to enhance your experience, analyze site traffic, and for marketing purposes. 
                        By clicking "Accept All", you consent to our use of cookies. You can customize your preferences or reject optional cookies.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <button 
                        @click="acceptAll" 
                        class="btn-primary text-sm"
                        type="button"
                    >
                        Accept All
                    </button>
                    <button 
                        @click="rejectOptional" 
                        class="btn-secondary text-sm"
                        type="button"
                    >
                        Reject Optional
                    </button>
                    <button 
                        @click="showPreferences = true" 
                        class="btn-ghost text-sm"
                        type="button"
                    >
                        Customize Preferences
                    </button>
                </div>
            </div>
            
            <!-- Preferences Modal -->
            <div x-show="showPreferences" x-transition class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div x-show="showPreferences" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="showPreferences = false"></div>
                    
                    <div x-show="showPreferences" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95" class="relative w-full max-w-md bg-white rounded-lg shadow-xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Cookie Preferences</h3>
                            <button @click="showPreferences = false" class="p-1 rounded text-gray-400 hover:text-gray-600" aria-label="Close">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <!-- Strictly Necessary -->
                            <div class="border rounded-lg p-4">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="font-medium text-gray-900">Strictly Necessary</h4>
                                        <p class="text-sm text-gray-500 mt-1">Required for the website to function properly. Cannot be disabled.</p>
                                    </div>
                                    <input type="checkbox" checked disabled class="mt-1 h-4 w-4 text-carelink-navy border-gray-300 rounded">
                                </div>
                                <ul class="mt-3 text-sm text-gray-600 space-y-1">
                                    <li>• Session management</li>
                                    <li>• Security (CSRF protection)</li>
                                    <li>• Load balancing</li>
                                </ul>
                            </div>
                            
                            <!-- Functional -->
                            <div class="border rounded-lg p-4">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="font-medium text-gray-900">Functional</h4>
                                        <p class="text-sm text-gray-500 mt-1">Enable enhanced functionality and personalization.</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" x-model="preferences.functional" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-carelink-navy/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-carelink-navy"></div>
                                    </label>
                                </div>
                                <ul class="mt-3 text-sm text-gray-600 space-y-1">
                                    <li>• Language preferences</li>
                                    <li>• Booking wizard progress</li>
                                </ul>
                            </div>
                            
                            <!-- Analytics -->
                            <div class="border rounded-lg p-4">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="font-medium text-gray-900">Analytics</h4>
                                        <p class="text-sm text-gray-500 mt-1">Help us understand how visitors interact with our website.</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" x-model="preferences.analytics" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-carelink-navy/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-carelink-navy"></div>
                                    </label>
                                </div>
                                <ul class="mt-3 text-sm text-gray-600 space-y-1">
                                    <li>• Page view tracking</li>
                                    <li>• User journey analysis</li>
                                </ul>
                            </div>
                            
                            <!-- Marketing -->
                            <div class="border rounded-lg p-4">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="font-medium text-gray-900">Marketing</h4>
                                        <p class="text-sm text-gray-500 mt-1">Used to deliver personalized advertisements.</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" x-model="preferences.marketing" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-carelink-navy/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-carelink-navy"></div>
                                    </label>
                                </div>
                                <ul class="mt-3 text-sm text-gray-600 space-y-1">
                                    <li>• Ad personalization</li>
                                    <li>• Conversion tracking</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="mt-6 flex justify-end gap-3">
                            <button @click="showPreferences = false" class="btn-ghost text-sm">Cancel</button>
                            <button @click="savePreferences" class="btn-primary text-sm">Save Preferences</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<script>
    function cookieConsent() {
        return {
            showBanner: @js($showBanner),
            showPreferences: false,
            preferences: {
                functional: false,
                analytics: false,
                marketing: false,
            },
            
            init() {
                const saved = localStorage.getItem('cookie_preferences');
                if (saved) {
                    this.preferences = JSON.parse(saved);
                }
            },
            
            acceptAll() {
                this.preferences = { functional: true, analytics: true, marketing: true };
                this.savePreferences();
            },
            
            rejectOptional() {
                this.preferences = { functional: false, analytics: false, marketing: false };
                this.savePreferences();
            },
            
            savePreferences() {
                localStorage.setItem('cookie_preferences', JSON.stringify(this.preferences));
                document.cookie = 'cookie_consent=true; max-age=31536000; path=/; SameSite=Lax';
                this.showBanner = false;
                this.showPreferences = false;
                this.loadScripts();
            },
            
            loadScripts() {
                if (this.preferences.analytics) {
                    // Load analytics scripts
                    console.log('Loading analytics scripts...');
                }
                if (this.preferences.marketing) {
                    // Load marketing scripts
                    console.log('Loading marketing scripts...');
                }
            }
        }
    }
</script>