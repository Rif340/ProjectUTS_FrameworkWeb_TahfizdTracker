@extends('layouts.app')

@section('title', 'Edit Hari Libur')
@section('page_title', 'Hari Libur')

@section('styles')
    <link rel="stylesheet" href="/css/hari-libur.css">
@endsection

@section('content')
    {{-- Background Page (Table Detail Hari Libur) --}}
    <div class="hl-card-head">
        <div class="hl-card-head-left">
            <img src="{{ asset('icon/figma_calendar_alt.svg') }}" alt="Calendar" class="hl-head-icon">
            <span class="hl-head-title">Detail Hari Libur</span>
        </div>
        <div class="hl-card-head-right">
            <select class="hl-select-show" disabled>
                <option>Tampilkan 10</option>
            </select>
            <button class="hl-btn-add" disabled>+ Tambah</button>
        </div>
    </div>

    <div class="hl-table-wrap">
        <table class="hl-tbl">
            <thead>
                <tr>
                    <th class="hl-th hl-th-no">No</th>
                    <th class="hl-th hl-th-ket">Keterangan Libur</th>
                    <th class="hl-th hl-th-tipe">Tipe Libur</th>
                    <th class="hl-th hl-th-tgl1">Tanggal Mulai</th>
                    <th class="hl-th hl-th-tgl2">Tanggal Selesai</th>
                    <th class="hl-th hl-th-aksi">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hariLiburList as $i => $item)
                    <tr>
                        <td class="hl-td hl-td-no">{{ $i + 1 }}</td>
                        <td class="hl-td">{{ $item->keterangan }}</td>
                        <td class="hl-td">{{ $item->tipe }}</td>
                        <td class="hl-td">{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('Y-m-d') }}</td>
                        <td class="hl-td">{{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('Y-m-d') }}</td>
                        <td class="hl-td hl-td-aksi">
                            <div class="hl-aksi-cell-grid">
                                <span class="hl-aksi-btn">
                                    <svg width="13" height="15" viewBox="42.5 10.5 13 15" fill="none" class="hl-icon-trash">
                                        <path d="M53.099 24.11C53.0719 24.3549 52.9554 24.5811 52.7718 24.7453C52.5881 24.9096 52.3504 25.0003 52.104 25H45.897C45.6506 25.0003 45.4129 24.9096 45.2292 24.7453C45.0456 24.5811 44.9291 24.3549 44.902 24.11L44 16H54L53.099 24.11ZM51 11C51.2652 11 51.5196 11.1054 51.7071 11.2929C51.8946 11.4804 52 11.7348 52 12V13H54C54.2652 13 54.5196 13.1054 54.7071 13.2929C54.8946 13.4804 55 13.7348 55 14V15H43V14C43 13.7348 43.1054 13.4804 43.2929 13.2929C43.4804 13.1054 43.7348 13 44 13H46V12C46 11.7348 46.1054 11.4804 46.2929 11.2929C46.4804 11.1054 46.7348 11 47 11H51ZM51 12H47V13H51V12Z" fill="#FF0000"/>
                                    </svg>
                                </span>
                                <span class="hl-aksi-btn">
                                    <svg width="18" height="16" viewBox="40 10 18 16" fill="none" class="hl-icon-edit">
                                        <path d="M52.5719 20.7805L53.5719 19.7805C53.7281 19.6242 54 19.7336 54 19.9586V24.5023C54 25.3305 53.3281 26.0023 52.5 26.0023H41.5C40.6719 26.0023 40 25.3305 40 24.5023V13.5023C40 12.6742 40.6719 12.0023 41.5 12.0023H50.0469C50.2688 12.0023 50.3813 12.2711 50.225 12.4305L49.225 13.4305C49.1781 13.4773 49.1156 13.5023 49.0469 13.5023H41.5V24.5023H52.5V20.9555C52.5 20.8898 52.525 20.8273 52.5719 20.7805ZM57.4656 14.4742L49.2594 22.6805L46.4344 22.993C45.6156 23.0836 44.9188 22.393 45.0094 21.568L45.3219 18.743L53.5281 10.5367C54.2437 9.82109 55.4 9.82109 56.1125 10.5367L57.4625 11.8867C58.1781 12.6023 58.1781 13.7617 57.4656 14.4742ZM54.3781 15.4398L52.5625 13.6242L46.7562 19.4336L46.5281 21.4742L48.5688 21.2461L54.3781 15.4398ZM56.4031 12.9492L55.0531 11.5992C54.925 11.4711 54.7156 11.4711 54.5906 11.5992L53.625 12.5648L55.4406 14.3805L56.4062 13.4148C56.5312 13.2836 56.5312 13.0773 56.4031 12.9492Z" fill="#0E7651"/>
                                    </svg>
                                </span>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="hl-td hl-empty">Belum ada data hari libur.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Overlay Form Edit (Figma Node 625:4377 / 625:4517) --}}
    <div class="hl-overlay">
        <div class="hl-form-modal">
            {{-- Logo --}}
            <img src="{{ asset('gambar/logo.png') }}" alt="Logo" class="hl-form-logo">

            {{-- Title & Subtitle --}}
            <div class="hl-form-header">
                <h1 class="hl-form-title">Form Edit Hari Libur</h1>
                <p class="hl-form-subtitle">Isi Dengan Lenkap, dan pastikan tidak ada yang salah</p>
            </div>

            {{-- Form Inputs --}}
            <form action="{{ route('hari-libur.update', $hariLibur->id_hari_libur) }}" method="POST" style="width: 100%;">
                @csrf
                @method('PUT')

                <div class="hl-form-grid">
                    {{-- Kolom Kiri --}}
                    <div class="hl-form-col">
                        {{-- Tahun Akademik (Figma 625:4524) --}}
                        <div class="hl-field-row">
                            <label class="hl-field-label">Tahun Akademik <span class="hl-req">*</span></label>
                            <div class="hl-field-input-wrap">
                                <select name="id_tahun_akademik" class="hl-select" required>
                                    <option value="" disabled>Pilih Tahun Akademik</option>
                                    @foreach($tahunAkademikList as $ta)
                                        <option value="{{ $ta->id_tahun_akademik }}"
                                            {{ (old('id_tahun_akademik', $hariLibur->id_tahun_akademik) == $ta->id_tahun_akademik) ? 'selected' : '' }}>
                                            {{ $ta->nama_tahun }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Keterangan Libur (Figma 625:4530) --}}
                        <div class="hl-field-row">
                            <label class="hl-field-label">Keterangan Libur <span class="hl-req">*</span></label>
                            <div class="hl-field-input-wrap">
                                <input type="text" name="keterangan" class="hl-input"
                                       value="{{ old('keterangan', $hariLibur->keterangan) }}" placeholder="Tahfizd" required>
                            </div>
                        </div>

                        {{-- Tipe Libur (Figma 625:4536) --}}
                        <div class="hl-field-row">
                            <label class="hl-field-label">Tipe Libur <span class="hl-req">*</span></label>
                            <div class="hl-field-input-wrap">
                                <select name="tipe" class="hl-select" required>
                                    <option value="" disabled>Pilih Tipe</option>
                                    <option value="Nasional" {{ old('tipe', $hariLibur->tipe) == 'Nasional' ? 'selected' : '' }}>Nasional</option>
                                    <option value="Sekolah"  {{ old('tipe', $hariLibur->tipe) == 'Sekolah' ? 'selected' : '' }}>Sekolah</option>
                                    <option value="Khusus"   {{ old('tipe', $hariLibur->tipe) == 'Khusus' ? 'selected' : '' }}>Khusus</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Kolom Kanan --}}
                    <div class="hl-form-col">
                        {{-- Tanggal Mulai (Figma 625:4542) --}}
                        <div class="hl-field-row">
                            <label class="hl-field-label">Tanggal Mulai <span class="hl-req">*</span></label>
                            <div class="hl-field-input-wrap">
                                <input type="date" name="tanggal_mulai" class="hl-input hl-input-date"
                                       value="{{ old('tanggal_mulai', \Carbon\Carbon::parse($hariLibur->tanggal_mulai)->format('Y-m-d')) }}" required>
                            </div>
                        </div>

                        {{-- Tanggal Selesai (Figma 625:4548) --}}
                        <div class="hl-field-row">
                            <label class="hl-field-label">Tanggal Selesai <span class="hl-req">*</span></label>
                            <div class="hl-field-input-wrap">
                                <input type="date" name="tanggal_selesai" class="hl-input hl-input-date"
                                       value="{{ old('tanggal_selesai', \Carbon\Carbon::parse($hariLibur->tanggal_selesai)->format('Y-m-d')) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons (Batal & Simpan) --}}
                <div class="hl-form-btns">
                    <a href="{{ route('hari-libur.index') }}" class="hl-btn-batal">Batal</a>
                    <button type="submit" class="hl-btn-simpan">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
