@extends('layouts.app')

@section('title', ($page->meta_title ?: 'Layanan Service & Kalibrasi Alat Lab - '.setting('site_name', 'Lab Supply')))
@section('meta_description', $page->meta_description ?: 'Layanan service, perawatan preventif, dan kalibrasi terstandar untuk alat dan instrumen laboratorium.')

@section('content')
    @include('partials.breadcrumbs', ['items' => [
        ['label' => 'Layanan', 'url' => null],
        ['label' => 'Service Alat Lab', 'url' => null],
    ]])

    <div class="container-x py-10 lg:py-16">
        {{-- Hero Header --}}
        <div class="mx-auto max-w-3xl text-center">
            <h1 class="font-display text-[36px] font-bold tracking-tight text-ink sm:text-[46px] lg:text-[52px] leading-tight">
                Jasa Service & Kalibrasi Alat Laboratorium
            </h1>
            <p class="mt-3 text-sm leading-relaxed text-muted max-w-2xl mx-auto">
                Perawatan preventif, perbaikan suku cadang asli, dan kalibrasi akurasi tinggi oleh teknisi bersertifikasi agar instrumen riset dan operasional laboratorium Anda selalu presisi dan mematuhi standar regulasi.
            </p>
            <div class="mt-7 flex flex-wrap items-center justify-center gap-3.5">
                <a href="https://wa.me/628111174992?text={{ urlencode('Halo LabSupply, saya ingin konsultasi layanan service dan kalibrasi alat laboratorium.') }}"
                   target="_blank" rel="noopener"
                   class="btn-outline border-emerald-600 bg-white text-emerald-700 hover:bg-emerald-50 !px-6 !py-3 font-semibold shadow-sm">
                    <svg class="size-4 shrink-0 text-emerald-600" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2m.01 18.15c-1.5 0-2.97-.4-4.24-1.16l-.3-.18-3.12.82.83-3.04-.2-.31a7.9 7.9 0 0 1-1.22-4.37c0-4.38 3.56-7.94 7.94-7.94 4.38 0 7.94 3.56 7.94 7.94 0 4.38-3.56 7.94-7.94 7.94"/>
                    </svg>
                    Konsultasi via WhatsApp
                </a>
                <a href="{{ route('contact.index') }}" class="btn-outline !px-6 !py-3 font-semibold">
                    Kirim Formulir Permintaan
                </a>
            </div>
        </div>

        {{-- Keunggulan / Value Proposition --}}
        <div class="mt-14 lg:mt-20">
            <h2 class="text-center font-display text-xl font-bold text-ink sm:text-2xl">Mengapa Memilih Layanan LabSupply?</h2>
            <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl border border-line bg-white p-6 shadow-sm transition hover:shadow-md">
                    <span class="grid size-11 place-items-center rounded-lg bg-blue-50 text-brand">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </span>
                    <h3 class="mt-4 font-display text-base font-semibold text-ink">Teknisi Bersertifikat</h3>
                    <p class="mt-2 text-xs leading-relaxed text-muted">Ditangani langsung oleh teknisi terlatih dengan sertifikasi resmi dan pemahaman mendalam standar instrumen.</p>
                </div>

                <div class="rounded-xl border border-line bg-white p-6 shadow-sm transition hover:shadow-md">
                    <span class="grid size-11 place-items-center rounded-lg bg-emerald-50 text-emerald-600">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10 9 9 9 8 9"/>
                        </svg>
                    </span>
                    <h3 class="mt-4 font-display text-base font-semibold text-ink">Laporan Digital Lengkap</h3>
                    <p class="mt-2 text-xs leading-relaxed text-muted">Akses berita acara dan sertifikat kalibrasi dalam format digital secara transparan dan mudah ditelusuri.</p>
                </div>

                <div class="rounded-xl border border-line bg-white p-6 shadow-sm transition hover:shadow-md">
                    <span class="grid size-11 place-items-center rounded-lg bg-indigo-50 text-indigo-600">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </span>
                    <h3 class="mt-4 font-display text-base font-semibold text-ink">Sparepart Original</h3>
                    <p class="mt-2 text-xs leading-relaxed text-muted">Jaminan penggunaan suku cadang asli pabrikan (OEM) untuk menjaga ketahanan dan keandalan instrumen.</p>
                </div>

                <div class="rounded-xl border border-line bg-white p-6 shadow-sm transition hover:shadow-md">
                    <span class="grid size-11 place-items-center rounded-lg bg-amber-50 text-amber-600">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </span>
                    <h3 class="mt-4 font-display text-base font-semibold text-ink">Layanan On-Site Cepat</h3>
                    <p class="mt-2 text-xs leading-relaxed text-muted">Kunjungan teknisi langsung ke fasilitas laboratorium Anda guna meminimalkan downtime kegiatan analisis.</p>
                </div>
            </div>
        </div>

        {{-- Jenis Layanan Kami --}}
        <div class="mt-16 lg:mt-24">
            <div class="text-center">
                <span class="text-xs font-semibold uppercase tracking-wider text-brand">Solusi Komprehensif</span>
                <h2 class="mt-1.5 font-display text-2xl font-bold text-ink sm:text-3xl">Jenis Layanan Kami</h2>
                <p class="mx-auto mt-2.5 max-w-2xl text-sm text-muted">
                    Kami melayani perawatan rutin, perbaikan komprehensif, dan kalibrasi metrologi untuk berbagai kategori instrumen laboratorium.
                </p>
            </div>

            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                {{-- 1. Service Mikroskop --}}
                <div class="flex flex-col rounded-xl border border-line bg-white p-6 shadow-sm transition hover:border-brand/40 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="grid size-12 place-items-center rounded-xl bg-blue-50 text-brand">
                            {{-- Microscope SVG --}}
                            <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 18h8"/>
                                <path d="M3 22h18"/>
                                <path d="M14 22a7 7 0 1 0 0-14h-1"/>
                                <path d="M9 14h2"/>
                                <path d="M9 12a2 2 0 0 1-2-2V6h6v4a2 2 0 0 1-2 2Z"/>
                                <path d="M12 6V3a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v3"/>
                            </svg>
                        </span>
                        <span class="rounded-full bg-mist px-2.5 py-0.5 text-[11px] font-medium text-ink/70">Optik & Mekanis</span>
                    </div>
                    <h3 class="mt-4 font-display text-lg font-semibold text-ink">Service Mikroskop</h3>
                    <p class="mt-2 text-xs leading-relaxed text-muted">
                        Perawatan dan pembersihan menyeluruh sistem optik, perataan fokus mekanis, dan kalibrasi penerangan mikroskop binokuler maupun trinokuler.
                    </p>
                    <ul class="mt-4 space-y-2 border-t border-line/70 pt-4 text-xs text-ink/80">
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-3.5 shrink-0 text-brand" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Pembersihan jamur & debu pada lensa okuler/obyektif</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-3.5 shrink-0 text-brand" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Pelumasan & alignment fokus halus/kasar (coaxial)</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-3.5 shrink-0 text-brand" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Kalibrasi sistem pencahayaan halogen atau LED</span>
                        </li>
                    </ul>
                </div>

                {{-- 2. Kalibrasi Spektrofotometer --}}
                <div class="flex flex-col rounded-xl border border-line bg-white p-6 shadow-sm transition hover:border-brand/40 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="grid size-12 place-items-center rounded-xl bg-purple-50 text-purple-600">
                            {{-- Spectrophotometer / Prism SVG --}}
                            <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2 2 22h20L12 2z"/>
                                <path d="m4.93 17 4.24-7.35"/>
                                <path d="m14.83 9.65 4.24 7.35"/>
                                <path d="M12 10v6"/>
                            </svg>
                        </span>
                        <span class="rounded-full bg-mist px-2.5 py-0.5 text-[11px] font-medium text-ink/70">Fotometri UV-Vis</span>
                    </div>
                    <h3 class="mt-4 font-display text-lg font-semibold text-ink">Kalibrasi Spektrofotometer</h3>
                    <p class="mt-2 text-xs leading-relaxed text-muted">
                        Verifikasi akurasi panjang gelombang, absorbansi fotometrik, dan linearitas detektor instrumen spektrofotometer UV-Vis dan Visible.
                    </p>
                    <ul class="mt-4 space-y-2 border-t border-line/70 pt-4 text-xs text-ink/80">
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-3.5 shrink-0 text-brand" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Uji akurasi panjang gelombang dengan filter standar</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-3.5 shrink-0 text-brand" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Pengujian baseline flatness dan kebocoran cahaya (stray light)</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-3.5 shrink-0 text-brand" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Pembersihan ruang kuvet & penggantian lampu D2/W</span>
                        </li>
                    </ul>
                </div>

                {{-- 3. Perawatan Centrifuge --}}
                <div class="flex flex-col rounded-xl border border-line bg-white p-6 shadow-sm transition hover:border-brand/40 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="grid size-12 place-items-center rounded-xl bg-cyan-50 text-cyan-600">
                            {{-- Centrifuge Spin SVG --}}
                            <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 2a10 10 0 0 1 10 10"/>
                                <path d="M12 12a4 4 0 1 0 4 4"/>
                                <circle cx="12" cy="12" r="1.5"/>
                            </svg>
                        </span>
                        <span class="rounded-full bg-mist px-2.5 py-0.5 text-[11px] font-medium text-ink/70">Mekatronik & RPM</span>
                    </div>
                    <h3 class="mt-4 font-display text-lg font-semibold text-ink">Perawatan Centrifuge</h3>
                    <p class="mt-2 text-xs leading-relaxed text-muted">
                        Pemeriksaan kestabilan rotasi sentrifus, uji getaran (vibration analysis), inspeksi kelayakan rotor, serta kalibrasi timer digital.
                    </p>
                    <ul class="mt-4 space-y-2 border-t border-line/70 pt-4 text-xs text-ink/80">
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-3.5 shrink-0 text-brand" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Verifikasi kecepatan RPM dengan tachometer terkalibrasi</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-3.5 shrink-0 text-brand" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Inspeksi rotor, balancing, dan pelumasan bearing motor</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-3.5 shrink-0 text-brand" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Pengujian interlock safety lid switch & emergency brake</span>
                        </li>
                    </ul>
                </div>

                {{-- 4. Kalibrasi Pipet Presisi --}}
                <div class="flex flex-col rounded-xl border border-line bg-white p-6 shadow-sm transition hover:border-brand/40 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="grid size-12 place-items-center rounded-xl bg-teal-50 text-teal-600">
                            {{-- Pipette SVG --}}
                            <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m19 11-8-8-8.6 8.6a2 2 0 0 0 0 2.8l5.2 5.2a2 2 0 0 0 2.8 0L19 11Z"/>
                                <path d="m5 2 5 5"/>
                                <path d="m2 5 5 5"/>
                                <path d="m16 8 3-3"/>
                                <path d="m18 10 3-3"/>
                                <path d="M7 21l-3 1 1-3"/>
                            </svg>
                        </span>
                        <span class="rounded-full bg-mist px-2.5 py-0.5 text-[11px] font-medium text-ink/70">ISO 8655 Gravimetri</span>
                    </div>
                    <h3 class="mt-4 font-display text-lg font-semibold text-ink">Kalibrasi Pipet Presisi</h3>
                    <p class="mt-2 text-xs leading-relaxed text-muted">
                        Kalibrasi volume mikropipet single-channel dan multi-channel secara gravimetrik sesuai standar internasional untuk menjamin akurasi pemipetan.
                    </p>
                    <ul class="mt-4 space-y-2 border-t border-line/70 pt-4 text-xs text-ink/80">
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-3.5 shrink-0 text-brand" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Pengujian akurasi volume pada level 10%, 50%, dan 100%</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-3.5 shrink-0 text-brand" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Penggantian O-ring seal & pembersihan piston shaft</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-3.5 shrink-0 text-brand" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Penerbitan sertifikat kalibrasi toleransi deviasi (inaccuracy & imprecision)</span>
                        </li>
                    </ul>
                </div>

                {{-- 5. Service Autoclave --}}
                <div class="flex flex-col rounded-xl border border-line bg-white p-6 shadow-sm transition hover:border-brand/40 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="grid size-12 place-items-center rounded-xl bg-red-50 text-red-600">
                            {{-- Autoclave / Pressure Chamber SVG --}}
                            <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="16" rx="3"/>
                                <circle cx="12" cy="12" r="4"/>
                                <line x1="12" y1="10" x2="12" y2="12"/>
                                <line x1="12" y1="12" x2="14" y2="12"/>
                                <path d="M7 8h.01M17 8h.01M7 16h.01M17 16h.01"/>
                            </svg>
                        </span>
                        <span class="rounded-full bg-mist px-2.5 py-0.5 text-[11px] font-medium text-ink/70">Sterilisasi & Tekanan</span>
                    </div>
                    <h3 class="mt-4 font-display text-lg font-semibold text-ink">Service Autoclave</h3>
                    <p class="mt-2 text-xs leading-relaxed text-muted">
                        Perbaikan sistem pemanas, pengujian kedap tekanan chamber, kalibrasi sensor temperatur dan katup pengaman (safety valve).
                    </p>
                    <ul class="mt-4 space-y-2 border-t border-line/70 pt-4 text-xs text-ink/80">
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-3.5 shrink-0 text-brand" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Kalibrasi sensor suhu digital probe dan pressure gauge</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-3.5 shrink-0 text-brand" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Uji kebocoran dan penggantian gasket silikon pintu</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-3.5 shrink-0 text-brand" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Pembersihan kerak pemanas & inspeksi katup pengaman</span>
                        </li>
                    </ul>
                </div>

                {{-- 6. Kalibrasi Timbangan Analitik --}}
                <div class="flex flex-col rounded-xl border border-line bg-white p-6 shadow-sm transition hover:border-brand/40 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="grid size-12 place-items-center rounded-xl bg-amber-50 text-amber-600">
                            {{-- Scale / Balance SVG --}}
                            <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 3v18"/>
                                <path d="M5 6l7-3 7 3"/>
                                <path d="M5 6v6a4 4 0 0 0 8 0V6"/>
                                <path d="M19 6v6a4 4 0 0 1-8 0V6"/>
                                <path d="M2 21h20"/>
                            </svg>
                        </span>
                        <span class="rounded-full bg-mist px-2.5 py-0.5 text-[11px] font-medium text-ink/70">Metrologi Presisi</span>
                    </div>
                    <h3 class="mt-4 font-display text-lg font-semibold text-ink">Kalibrasi Timbangan Analitik</h3>
                    <p class="mt-2 text-xs leading-relaxed text-muted">
                        Pengujian performa penimbangan presisi menggunakan anak timbang standar kelas E2/F1 untuk memastikan kepresisian mikrogram dan miligram.
                    </p>
                    <ul class="mt-4 space-y-2 border-t border-line/70 pt-4 text-xs text-ink/80">
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-3.5 shrink-0 text-brand" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Uji linearitas, repeatability, dan corner load error</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-3.5 shrink-0 text-brand" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Penyesuaian span kalibrasi internal dan eksternal</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-3.5 shrink-0 text-brand" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Pembersihan sensor load cell dan perataan draft shield</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Alur Pengajuan Layanan Service --}}
        <div class="mt-16 lg:mt-24 rounded-2xl border border-line bg-mist/40 p-8 sm:p-10 lg:p-12">
            <h2 class="text-center font-display text-xl font-bold text-ink sm:text-2xl">Alur Mudah Pengajuan Service Alat Lab</h2>
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="relative flex flex-col items-center text-center">
                    <span class="grid size-12 place-items-center rounded-full bg-brand text-lg font-bold text-white shadow-sm">1</span>
                    <h3 class="mt-4 font-display text-sm font-semibold text-ink">Konsultasi Kendala</h3>
                    <p class="mt-1.5 text-xs leading-relaxed text-muted">Sampaikan merk, tipe alat, dan gejala kerusakan/kebutuhan kalibrasi melalui WhatsApp atau form kami.</p>
                </div>
                <div class="relative flex flex-col items-center text-center">
                    <span class="grid size-12 place-items-center rounded-full bg-brand text-lg font-bold text-white shadow-sm">2</span>
                    <h3 class="mt-4 font-display text-sm font-semibold text-ink">Estimasi & Penawaran</h3>
                    <p class="mt-1.5 text-xs leading-relaxed text-muted">Tim kami memberikan diagnosa awal, estimasi pengerjaan, dan rincian penawaran biaya yang transparan.</p>
                </div>
                <div class="relative flex flex-col items-center text-center">
                    <span class="grid size-12 place-items-center rounded-full bg-brand text-lg font-bold text-white shadow-sm">3</span>
                    <h3 class="mt-4 font-display text-sm font-semibold text-ink">Pengerjaan On-Site / Workshop</h3>
                    <p class="mt-1.5 text-xs leading-relaxed text-muted">Teknisi bersertifikasi melakukan tindakan perbaikan, penggantian part, dan kalibrasi sesuai standar SOP.</p>
                </div>
                <div class="relative flex flex-col items-center text-center">
                    <span class="grid size-12 place-items-center rounded-full bg-brand text-lg font-bold text-white shadow-sm">4</span>
                    <h3 class="mt-4 font-display text-sm font-semibold text-ink">Laporan & Serah Terima</h3>
                    <p class="mt-1.5 text-xs leading-relaxed text-muted">Instrumen diuji fungsi bersama, disertai penyerahan berita acara kerja dan sertifikat kalibrasi resmi.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
