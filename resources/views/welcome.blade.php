<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aduu Tech Medical Supplies - Quality Healthcare Equipment</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-gray-100 text-gray-900 overflow-x-hidden">
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-800" style="padding:2rem 1.5rem 6rem">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,<svg width=\"100\" height=\"100\" xmlns=\"http://www.w3.org/2000/svg\"><defs><pattern id=\"grid\" width=\"40\" height=\"40\" patternUnits=\"userSpaceOnUse\"><path d=\"M 40 0 L 0 0 0 40\" fill=\"none\" stroke=\"rgba(255,255,255,0.05)\" stroke-width=\"1\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23grid)\"/></svg></div>
        
        <nav class="max-w-7xl mx-auto flex justify-between items-center py-4 relative z-10">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center font-bold text-xl text-white border border-white/30">AT</div>
                <span class="text-white font-semibold text-lg">Aduu Tech</span>
            </div>
            <div class="flex gap-8 items-center">
                <a href="{{ url('/admin') }}" class="bg-white text-indigo-600 px-6 py-3 rounded-xl font-semibold transition-all border-2 border-indigo-600 hover:bg-indigo-600 hover:text-white">Admin Panel</a>
            </div>
        </nav>

        <div class="max-w-7xl mx-auto mt-16 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center relative z-10">
            <div class="opacity-0 translate-y-8 animate-fadeInUp">
                <h1 class="text-4xl md:text-6xl font-bold text-white leading-tight mb-6">Premium Medical Equipment for Healthcare Excellence</h1>
                <p class="text-xl text-white/90 mb-8 leading-relaxed">Trusted supplier of dialysis consumables, diagnostic devices, and critical care equipment with 24/7 support.</p>
                <div class="grid grid-cols-3 gap-8 mt-12">
                    <div class="text-center p-6 opacity-0 translate-y-8 animate-fadeInUp">
                        <div class="text-5xl font-bold bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent mb-2">15+</div>
                        <div class="text-white/80 font-medium">Brands</div>
                    </div>
                    <div class="text-center p-6 opacity-0 translate-y-8 animate-fadeInUp animate-delay-200">
                        <div class="text-5xl font-bold bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent mb-2">500+</div>
                        <div class="text-white/80 font-medium">Happy Customers</div>
                    </div>
                    <div class="text-center p-6 opacity-0 translate-y-8 animate-fadeInUp animate-delay-300">
                        <div class="text-5xl font-bold bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent mb-2">24/7</div>
                        <div class="text-white/80 font-medium">Support</div>
                    </div>
                </div>
            </div>
            <div class="opacity-0 translate-y-8 animate-fadeInUp animate-delay-200 grid grid-cols-2 gap-4">
                <div class="overflow-hidden rounded-xl relative group">
                    <img src="https://placehold.co/800x800/4f46e5/white?text=IV+Pump" alt="IV Pump" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                </div>
                <div class="overflow-hidden rounded-xl relative group mt-8">
                    <img src="https://placehold.co/800x800/ec4899/white?text=Ventilator" alt="Ventilator" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                </div>
                <div class="overflow-hidden rounded-xl relative group -mt-8">
                    <img src="https://placehold.co/800x800/8b5cf6/white?text=Surgical+Tools" alt="Surgical Tools" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                </div>
                <div class="overflow-hidden rounded-xl relative group">
                    <img src="https://placehold.co/800x800/0ea5e9/white?text=Defibrillator" alt="Defibrillator" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                </div>
            </div>
        </div>
    </section>

    <!-- Solutions Section -->
    <section id="solutions" class="max-w-7xl mx-auto -mt-12 mb-24 px-6 relative z-20">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-2xl p-8 transition-all duration-500 border border-gray-200 hover:translate-y-[-8px] hover:shadow-2xl hover:border-indigo-500 opacity-0 translate-y-8 animate-fadeInUp">
                <div class="w-14 h-14 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center mb-4">
                    <svg width="28" height="28" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold mb-2">Dialysis Equipment</h3>
                <p class="text-gray-600 leading-relaxed">Complete dialysis solutions with consumables and machines from leading manufacturers.</p>
            </div>
            <div class="bg-white rounded-2xl p-8 transition-all duration-500 border border-gray-200 hover:translate-y-[-8px] hover:shadow-2xl hover:border-indigo-500 opacity-0 translate-y-8 animate-fadeInUp animate-delay-200">
                <div class="w-14 h-14 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center mb-4">
                    <svg width="28" height="28" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold mb-2">Diagnostic Devices</h3>
                <p class="text-gray-600 leading-relaxed">Advanced imaging and diagnostic tools for accurate patient assessment.</p>
            </div>
            <div class="bg-white rounded-2xl p-8 transition-all duration-500 border border-gray-200 hover:translate-y-[-8px] hover:shadow-2xl hover:border-indigo-500 opacity-0 translate-y-8 animate-fadeInUp animate-delay-300">
                <div class="w-14 h-14 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center mb-4">
                    <svg width="28" height="28" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold mb-2">Lab Reagents</h3>
                <p class="text-gray-600 leading-relaxed">High-quality reagents for chemistry and hematology analyzers.</p>
            </div>
            <div class="bg-white rounded-2xl p-8 transition-all duration-500 border border-gray-200 hover:translate-y-[-8px] hover:shadow-2xl hover:border-indigo-500 opacity-0 translate-y-8 animate-fadeInUp animate-delay-500">
                <div class="w-14 h-14 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center mb-4">
                    <svg width="28" height="28" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold mb-2">Service & Support</h3>
                <p class="text-gray-600 leading-relaxed">Installation, training, and preventive maintenance services.</p>
            </div>
        </div>
    </section>

    <!-- Showcase Section -->
    <section id="showcase" class="max-w-7xl mx-auto mb-24 px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold mb-4 text-gray-900">Featured Equipment</h2>
            <p class="text-lg text-gray-600 mb-0">Explore our range of premium medical devices and supplies</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-2xl p-8 transition-all duration-500 border border-gray-200 hover:translate-y-[-8px] hover:shadow-2xl hover:border-indigo-500 opacity-0 translate-y-8 animate-fadeInUp">
                <div class="overflow-hidden rounded-xl relative group mb-6" style="height: 220px;">
                    <img src="https://placehold.co/1200x800/0ea5e9/white?text=Patient+Monitor" alt="Patient Monitor" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                </div>
                <span class="inline-block px-4 py-2 bg-gradient-to-br from-indigo-600 to-purple-600 text-white rounded-full text-sm font-semibold mb-1">Critical Care</span>
                <h3 class="text-2xl font-semibold mt-4 mb-2">Patient Monitoring</h3>
                <p class="text-gray-600 leading-relaxed">Advanced vital signs monitors for ICU and ward settings with real-time data tracking.</p>
            </div>
            <div class="bg-white rounded-2xl p-8 transition-all duration-500 border border-gray-200 hover:translate-y-[-8px] hover:shadow-2xl hover:border-indigo-500 opacity-0 translate-y-8 animate-fadeInUp animate-delay-200">
                <div class="overflow-hidden rounded-xl relative group mb-6" style="height: 220px;">
                    <img src="https://placehold.co/1200x800/ec4899/white?text=Ultrasound+Machine" alt="Ultrasound" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                </div>
                <span class="inline-block px-4 py-2 bg-gradient-to-br from-indigo-600 to-purple-600 text-white rounded-full text-sm font-semibold mb-1">Imaging</span>
                <h3 class="text-2xl font-semibold mt-4 mb-2">Ultrasound Systems</h3>
                <p class="text-gray-600 leading-relaxed">Portable and cart-based ultrasound systems for multiple medical specialties.</p>
            </div>
            <div class="bg-white rounded-2xl p-8 transition-all duration-500 border border-gray-200 hover:translate-y-[-8px] hover:shadow-2xl hover:border-indigo-500 opacity-0 translate-y-8 animate-fadeInUp animate-delay-300">
                <div class="overflow-hidden rounded-xl relative group mb-6" style="height: 220px;">
                    <img src="https://placehold.co/1200x800/8b5cf6/white?text=Lab+Analyzer" alt="Laboratory" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                </div>
                <span class="inline-block px-4 py-2 bg-gradient-to-br from-indigo-600 to-purple-600 text-white rounded-full text-sm font-semibold mb-1">Laboratory</span>
                <h3 class="text-2xl font-semibold mt-4 mb-2">Lab Analyzers</h3>
                <p class="text-gray-600 leading-relaxed">Clinical chemistry and hematology analyzers with comprehensive reagent support.</p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-gradient-to-br from-indigo-600 to-purple-600 py-24 px-6 text-center relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,<svg width=\"100\" height=\"100\" xmlns=\"http://www.w3.org/2000/svg\"><defs><pattern id=\"dots\" width=\"20\" height=\"20\" patternUnits=\"userSpaceOnUse\"><circle cx=\"2\" cy=\"2\" r=\"1\" fill=\"rgba(255,255,255,0.1)\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23dots)\"/></svg></div>
        <div class="max-w-3xl mx-auto relative z-10">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">Ready to Upgrade Your Medical Facility?</h2>
            <p class="text-xl text-white/90 mb-10">Contact our team for personalized equipment recommendations and competitive pricing.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="mailto:info@aduutech.com" class="bg-white text-indigo-600 px-8 py-4 rounded-xl font-semibold transition-all border-2 border-indigo-600 hover:bg-indigo-600 hover:text-white">Request Quote</a>
                <a href="tel:+251000000000" class="bg-white text-indigo-600 px-8 py-4 rounded-xl font-semibold transition-all border-2 border-indigo-600 hover:bg-indigo-600 hover:text-white">Call Us</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 px-6">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-12 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center font-bold">AT</div>
                    <span class="font-semibold text-lg">Aduu Tech</span>
                </div>
                <p class="text-gray-400 leading-relaxed">Premium medical equipment supplier serving healthcare facilities across Ethiopia.</p>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Quick Links</h4>
                <div class="flex flex-col gap-2">
                    <a href="#solutions" class="text-gray-400 hover:opacity-80 transition-opacity">Solutions</a>
                    <a href="#showcase" class="text-gray-400 hover:opacity-80 transition-opacity">Products</a>
                    <a href="{{ url('/admin') }}" class="text-gray-400 hover:opacity-80 transition-opacity">Admin Panel</a>
                </div>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Contact</h4>
                <div class="text-gray-400 leading-relaxed space-y-2">
                    <div>milkii@aduutech.com</div>
                    <div>+251904583333</div>
                    <div>Addis Ababa, Ethiopia</div>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-800 pt-8 text-center text-gray-400">
            <p>&copy; {{ date('Y') }} Aduu Tech Medical Supplies. All rights reserved.</p>
        </div>
    </footer>
    
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fadeInUp {
            animation: fadeInUp 0.8s ease forwards;
        }
        
        .animate-delay-200 {
            animation-delay: 0.2s;
        }
        
        .animate-delay-300 {
            animation-delay: 0.4s;
        }
        
        .animate-delay-500 {
            animation-delay: 0.6s;
        }
    </style>
</body>
</html>