@extends('frontend.layouts.app')

@section('title', __('Tentang Kami'))

@push('styles')
<style>
    .about-page { background: #fafafa; }
    .about-shell { max-width: 1440px; margin: 0 auto; padding: 44px 24px 72px; }
    .about-grid { display: grid; grid-template-columns: minmax(0, 1fr) 360px; gap: 32px; align-items: start; }
    .about-card { background: #fff; border: 1px solid #e0e3e6; border-radius: 28px; }
    .about-muted { color: #68717b; }
    .about-kicker { color: #315b91; letter-spacing: .12em; font-size: .72rem; font-weight: 800; text-transform: uppercase; }
    .about-sidebar { position: sticky; top: 104px; }
    .about-hero { min-height: 245px; overflow: hidden; position: relative; background: linear-gradient(135deg, #193b69, #315b91); }
    .about-hero::after { content: ''; position: absolute; inset: 0; background: linear-gradient(90deg, rgba(19,48,85,.95), rgba(19,48,85,.25)); }
    .about-hero img { width: 100%; height: 100%; min-height: 245px; object-fit: cover; opacity: .65; }
    .about-hero-content { position: absolute; inset: 0; z-index: 1; display: flex; flex-direction: column; justify-content: center; padding: 36px; color: white; }
    .about-section { padding: 32px; }
    .about-section + .about-section { margin-top: 24px; }
    .about-title { color: #292e33; font-size: clamp(1.65rem, 3vw, 2.65rem); line-height: 1.1; font-weight: 800; letter-spacing: -.04em; }
    .about-heading { color: #292e33; font-size: 1.25rem; font-weight: 800; }
    .about-rule { width: 52px; height: 4px; border-radius: 99px; background: #d6a63c; margin-top: 14px; }
    .about-info { display: flex; gap: 14px; align-items: flex-start; padding: 14px 0; border-bottom: 1px solid #edf0f2; }
    .about-info:last-child { border-bottom: 0; }
    .about-icon { width: 38px; height: 38px; flex: 0 0 auto; border-radius: 12px; display: grid; place-items: center; background: #eef4fb; color: #315b91; }
    .about-list { display: grid; gap: 10px; margin: 0; padding: 0; list-style: none; }
    .about-item { display: flex; gap: 12px; align-items: flex-start; padding: 14px 16px; border-radius: 16px; background: #f5f7f8; color: #4e5862; }
    .about-number { width: 28px; height: 28px; flex: 0 0 auto; display: grid; place-items: center; border-radius: 50%; background: #315b91; color: #fff; font-size: .8rem; font-weight: 800; }
    .about-table { width: 100%; border-collapse: collapse; }
    .about-table th, .about-table td { padding: 14px 16px; border-bottom: 1px solid #edf0f2; text-align: left; }
    .about-table th { color: #68717b; font-size: .76rem; text-transform: uppercase; letter-spacing: .08em; }
    .about-table td:first-child { width: 34%; color: #315b91; font-weight: 800; white-space: nowrap; }
    .about-people { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
    .about-person { padding: 18px 12px; text-align: center; border: 1px solid #e7eaed; border-radius: 18px; }
    .about-person-avatar { width: 76px; height: 76px; margin: 0 auto 12px; border-radius: 50%; display: grid; place-items: center; background: #eef4fb; color: #315b91; }
    @media (max-width: 900px) {
        .about-grid { grid-template-columns: 1fr; }
        .about-sidebar { position: static; }
        .about-people { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 600px) {
        .about-shell { padding: 24px 16px 48px; }
        .about-section { padding: 22px; }
        .about-hero-content { padding: 24px; }
        .about-table th, .about-table td { padding: 11px 9px; font-size: .82rem; }
        .about-table td:first-child { white-space: normal; }
    }
</style>
@endpush

@section('content')
@php
    $jadwalSantri = [
        ['waktu' => '03.30 - 04.30', 'kegiatan' => 'Isi jadwal kegiatan santri'],
        ['waktu' => '04.30 - 06.00', 'kegiatan' => 'Isi jadwal kegiatan santri'],
        ['waktu' => '06.00 - 07.00', 'kegiatan' => 'Isi jadwal kegiatan santri'],
        ['waktu' => '07.00 - 12.00', 'kegiatan' => 'Isi jadwal kegiatan santri'],
        ['waktu' => '12.00 - 13.00', 'kegiatan' => 'Isi jadwal kegiatan santri'],
        ['waktu' => '13.00 - 15.00', 'kegiatan' => 'Isi jadwal kegiatan santri'],
        ['waktu' => '15.00 - 18.00', 'kegiatan' => 'Isi jadwal kegiatan santri'],
        ['waktu' => '18.00 - 20.00', 'kegiatan' => 'Isi jadwal kegiatan santri'],
        ['waktu' => '20.00 - 21.30', 'kegiatan' => 'Isi jadwal kegiatan santri'],
        ['waktu' => '21.30 - 03.30', 'kegiatan' => 'Istirahat malam'],
    ];
    $fasilitasPesantren = ['Isi nama fasilitas yang tersedia', 'Isi nama fasilitas yang tersedia', 'Isi nama fasilitas yang tersedia', 'Isi nama fasilitas yang tersedia'];
    $strukturPesantren = [
        ['foto' => null, 'nama' => 'Isi nama', 'jabatan' => 'Isi jabatan'],
        ['foto' => null, 'nama' => 'Isi nama', 'jabatan' => 'Isi jabatan'],
        ['foto' => null, 'nama' => 'Isi nama', 'jabatan' => 'Isi jabatan'],
        ['foto' => null, 'nama' => 'Isi nama', 'jabatan' => 'Isi jabatan'],
    ];
    $trilogi = [
        ['judul' => 'Menjalankan ibadah wajib', 'isi' => 'Menjalankan ibadah wajib yang menjadi tanggungan utama seorang Muslim secara istiqamah.'],
        ['judul' => 'Tidak melakukan dosa besar', 'isi' => 'Menjauhi perbuatan maksiat atau dosa besar yang dapat merusak akidah dan akhlak.'],
        ['judul' => 'Berbaik adab (akhlak)', 'isi' => 'Menjaga etika dan keluhuran budi kepada Allah SWT, sesama makhluk, serta lingkungan.'],
    ];
    $panca = [
        ['judul' => 'Kesadaran Beragama', 'isi' => 'Memahami dan mengamalkan ajaran agama secara benar.'],
        ['judul' => 'Kesadaran Berilmu', 'isi' => 'Menyadari pentingnya mencari dan mengembangkan ilmu.'],
        ['judul' => 'Kesadaran Bermasyarakat', 'isi' => 'Peduli dan memberi manfaat bagi masyarakat luas.'],
        ['judul' => 'Kesadaran Berbangsa dan Bernegara', 'isi' => 'Setia pada tanah air serta taat pada aturan kehidupan berbangsa.'],
        ['judul' => 'Kesadaran Berorganisasi', 'isi' => 'Mampu bekerja sama, memimpin, dan mengelola organisasi.'],
    ];
@endphp

<div class="about-page">
    <div class="about-shell">
        <div class="about-grid">
            <div>
                <section class="about-card about-hero">
                    <img src="{{ asset('images/kegiatan-pesantren-1200.webp') }}" alt="{{ __('Kegiatan pesantren') }}">
                    <div class="about-hero-content">
                        <span class="about-kicker" style="color:#f5d98a">{{ __('Tentang Kami') }}</span>
                        <h1 class="about-title" style="color:#fff; max-width:560px; margin-top:12px">{{ $pesantren?->nama ?? 'Pesantren Nurul Furqon' }}</h1>
                        <p style="max-width:590px; margin-top:14px; color:rgba(255,255,255,.82)">{{ __('Mengenal lebih dekat perjalanan, nilai, dan lingkungan pendidikan pesantren kami.') }}</p>
                    </div>
                </section>

                <section class="about-card about-section" style="margin-top:24px">
                    <span class="about-kicker">{{ __('Cerita Kami') }}</span>
                    <h2 class="about-title" style="font-size:2rem; margin-top:10px">{{ __('Membangun generasi berilmu dan berakhlak') }}</h2>
                    <div class="about-rule"></div>
                    <div class="about-muted prose max-w-none" style="margin-top:24px; line-height:1.85">
                        {!! $pesantren?->sejarah ?? '<p>' . __('Belum ada informasi sejarah pesantren.') . '</p>' !!}
                    </div>
                </section>

                <div class="grid md:grid-cols-2 gap-6" style="margin-top:24px">
                    <section class="about-card about-section">
                        <span class="about-kicker">{{ __('Arah Pendidikan') }}</span>
                        <h2 class="about-heading" style="margin-top:10px">{{ __('Visi') }}</h2>
                        <div class="about-rule"></div>
                        <div class="about-muted prose prose-sm max-w-none" style="margin-top:18px; line-height:1.8">
                            {!! $pesantren?->visi ?? '<p>' . __('Belum diisi.') . '</p>' !!}
                        </div>
                    </section>
                    <section class="about-card about-section">
                        <span class="about-kicker">{{ __('Langkah Kami') }}</span>
                        <h2 class="about-heading" style="margin-top:10px">{{ __('Misi') }}</h2>
                        <div class="about-rule"></div>
                        <div class="about-muted prose prose-sm max-w-none" style="margin-top:18px; line-height:1.8">
                            {!! $pesantren?->misi ?? '<p>' . __('Belum diisi.') . '</p>' !!}
                        </div>
                    </section>
                </div>

                <section class="about-card about-section" style="margin-top:24px">
                    <span class="about-kicker">{{ __('Kehidupan Santri') }}</span>
                    <h2 class="about-heading" style="margin-top:10px">{{ __('Jadwal Kegiatan Santri') }}</h2>
                    <p class="about-muted" style="margin-top:8px">{{ __('Gambaran aktivitas santri selama 24 jam') }}</p>
                    <div style="overflow-x:auto; margin-top:20px">
                        <table class="about-table">
                            <thead><tr><th>{{ __('Waktu') }}</th><th>{{ __('Kegiatan') }}</th></tr></thead>
                            <tbody>
                                @foreach($jadwalSantri as $jadwal)
                                    <tr><td>{{ $jadwal['waktu'] }}</td><td class="about-muted">{{ $jadwal['kegiatan'] }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="about-card about-section" style="margin-top:24px">
                    <span class="about-kicker">{{ __('Lingkungan Belajar') }}</span>
                    <h2 class="about-heading" style="margin-top:10px">{{ __('Fasilitas Pesantren') }}</h2>
                    <div class="about-list" style="margin-top:20px">
                        @foreach($fasilitasPesantren as $fasilitas)
                            <div class="about-item"><span class="about-number"><i data-lucide="check" style="width:15px"></i></span><span>{{ $fasilitas }}</span></div>
                        @endforeach
                    </div>
                </section>
            </div>

            <aside class="about-sidebar">
                <section class="about-card about-section">
                    <img src="{{ asset('images/logo-pesantren.webp') }}" alt="Logo" style="width:78px;height:78px;object-fit:contain;margin-bottom:20px">
                    <span class="about-kicker">{{ __('Identitas Pesantren') }}</span>
                    <h2 class="about-heading" style="font-size:1.4rem; margin-top:10px">{{ $pesantren?->nama ?? 'Pesantren Nurul Furqon' }}</h2>
                    <p class="about-muted" style="font-size:.8rem; margin-top:6px">NSPP: {{ $pesantren?->nspp ?? '-' }}</p>
                    <div style="margin-top:22px">
                        @if($pesantren?->telepon)<div class="about-info"><span class="about-icon"><i data-lucide="phone" style="width:17px"></i></span><span class="about-muted" style="font-size:.88rem; overflow-wrap:anywhere">{{ $pesantren->telepon }}</span></div>@endif
                        @if($pesantren?->email)<div class="about-info"><span class="about-icon"><i data-lucide="mail" style="width:17px"></i></span><span class="about-muted" style="font-size:.88rem; overflow-wrap:anywhere">{{ $pesantren->email }}</span></div>@endif
                        @if($pesantren?->website)<div class="about-info"><span class="about-icon"><i data-lucide="globe" style="width:17px"></i></span><span class="about-muted" style="font-size:.88rem; overflow-wrap:anywhere">{{ $pesantren->website }}</span></div>@endif
                        <div class="about-info"><span class="about-icon"><i data-lucide="map-pin" style="width:17px"></i></span><span class="about-muted" style="font-size:.88rem; line-height:1.6">{{ $pesantren?->alamat ?? '-' }}@if($pesantren?->kode_pos), {{ $pesantren->kode_pos }}@endif</span></div>
                    </div>
                </section>

                <section class="about-card about-section" style="margin-top:24px">
                    <span class="about-kicker">{{ __('Nilai Santri') }}</span>
                    <h2 class="about-heading" style="margin-top:10px">{{ __('Trilogi Santri') }}</h2>
                    <div class="about-list" style="margin-top:20px">
                        @foreach($trilogi as $item)<div class="about-item"><span class="about-number">{{ $loop->iteration }}</span><span><strong style="display:block;color:#292e33">{{ __($item['judul']) }}</strong><small class="about-muted" style="display:block;margin-top:5px;line-height:1.55">{{ __($item['isi']) }}</small></span></div>@endforeach
                    </div>
                </section>
            </aside>
        </div>

        <section class="about-card about-section" style="margin-top:24px">
            <span class="about-kicker">{{ __('Karakter dan Kesadaran') }}</span>
            <h2 class="about-heading" style="margin-top:10px">{{ __('Panca Kesadaran Santri') }}</h2>
            <div class="about-list" style="margin-top:20px; display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr))">
                @foreach($panca as $item)<div class="about-item"><span class="about-number">{{ $loop->iteration }}</span><span><strong style="display:block;color:#292e33">{{ __($item['judul']) }}</strong><small class="about-muted" style="display:block;margin-top:5px;line-height:1.55">{{ __($item['isi']) }}</small></span></div>@endforeach
            </div>
        </section>

        <section class="about-card about-section" style="margin-top:24px">
            <span class="about-kicker">{{ __('Pengelola') }}</span>
            <h2 class="about-heading" style="margin-top:10px">{{ __('Struktur Pesantren') }}</h2>
            <div class="about-people" style="margin-top:20px">
                @foreach($strukturPesantren as $anggota)
                    <div class="about-person">
                        @if($anggota['foto']) <img src="{{ asset($anggota['foto']) }}" alt="{{ $anggota['nama'] }}" class="about-person-avatar" style="object-fit:cover">@else <div class="about-person-avatar"><i data-lucide="user-round"></i></div>@endif
                        <strong style="display:block;color:#292e33;font-size:.9rem">{{ $anggota['nama'] }}</strong>
                        <small style="display:block;color:#315b91;margin-top:5px">{{ $anggota['jabatan'] }}</small>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</div>
@endsection
