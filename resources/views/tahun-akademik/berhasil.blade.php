@extends('layouts.app')

@section('title', 'Berhasil')
@section('page_title', 'Tahun Akademik')

@section('styles')
    <link rel="stylesheet" href="/css/tahun-akademik.css">
@endsection

@section('content')
    {{-- Background Page (Table Detail Tahun Akademik) --}}
    <div class="ta-card-head">
        <div class="ta-card-head-left">
            <img src="{{ asset('icon/figma_calendar_alt.svg') }}" alt="Calendar" class="ta-head-icon">
            <span class="ta-head-title">Detail Tahun Akademik</span>
        </div>
        <div class="ta-card-head-right">
            <select class="ta-select-show" disabled>
                <option>Tampilkan 10</option>
            </select>
            <button class="ta-btn-add" disabled>+ Tambah</button>
        </div>
    </div>

    <div class="ta-table-wrap">
        <table class="ta-tbl">
            <thead>
                <tr>
                    <th class="ta-th ta-th-no">No</th>
                    <th class="ta-th ta-th-thn">Tahun Ajaran</th>
                    <th class="ta-th ta-th-sem">Semester</th>
                    <th class="ta-th ta-th-tgl1">Tanggal Mulai</th>
                    <th class="ta-th ta-th-tgl2">Tanggal Selesai</th>
                    <th class="ta-th ta-th-aksi">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tahunAkademikList as $i => $item)
                    <tr>
                        <td class="ta-td ta-td-no">{{ $i + 1 }}</td>
                        <td class="ta-td">{{ $item->nama_tahun }}</td>
                        <td class="ta-td">{{ $item->semester ?? 'Ganjil' }}</td>
                        <td class="ta-td">{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('Y-m-d') }}</td>
                        <td class="ta-td">{{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('Y-m-d') }}</td>
                        <td class="ta-td ta-td-aksi">
                            <div class="ta-aksi-cell-grid">
                                <span class="ta-aksi-btn">
                                    <svg width="13" height="15" viewBox="42.5 10.5 13 15" fill="none" class="ta-icon-trash">
                                        <path d="M53.099 24.11C53.0719 24.3549 52.9554 24.5811 52.7718 24.7453C52.5881 24.9096 52.3504 25.0003 52.104 25H45.897C45.6506 25.0003 45.4129 24.9096 45.2292 24.7453C45.0456 24.5811 44.9291 24.3549 44.902 24.11L44 16H54L53.099 24.11ZM51 11C51.2652 11 51.5196 11.1054 51.7071 11.2929C51.8946 11.4804 52 11.7348 52 12V13H54C54.2652 13 54.5196 13.1054 54.7071 13.2929C54.8946 13.4804 55 13.7348 55 14V15H43V14C43 13.7348 43.1054 13.4804 43.2929 13.2929C43.4804 13.1054 43.7348 13 44 13H46V12C46 11.7348 46.1054 11.4804 46.2929 11.2929C46.4804 11.1054 46.7348 11 47 11H51ZM51 12H47V13H51V12Z" fill="#FF0000"/>
                                    </svg>
                                </span>
                                <span class="ta-aksi-btn">
                                    <svg width="18" height="16" viewBox="40 10 18 16" fill="none" class="ta-icon-edit">
                                        <path d="M52.5719 20.7805L53.5719 19.7805C53.7281 19.6242 54 19.7336 54 19.9586V24.5023C54 25.3305 53.3281 26.0023 52.5 26.0023H41.5C40.6719 26.0023 40 25.3305 40 24.5023V13.5023C40 12.6742 40.6719 12.0023 41.5 12.0023H50.0469C50.2688 12.0023 50.3813 12.2711 50.225 12.4305L49.225 13.4305C49.1781 13.4773 49.1156 13.5023 49.0469 13.5023H41.5V24.5023H52.5V20.9555C52.5 20.8898 52.525 20.8273 52.5719 20.7805ZM57.4656 14.4742L49.2594 22.6805L46.4344 22.993C45.6156 23.0836 44.9188 22.393 45.0094 21.568L45.3219 18.743L53.5281 10.5367C54.2437 9.82109 55.4 9.82109 56.1125 10.5367L57.4625 11.8867C58.1781 12.6023 58.1781 13.7617 57.4656 14.4742ZM54.3781 15.4398L52.5625 13.6242L46.7562 19.4336L46.5281 21.4742L48.5688 21.2461L54.3781 15.4398ZM56.4031 12.9492L55.0531 11.5992C54.925 11.4711 54.7156 11.4711 54.5906 11.5992L53.625 12.5648L55.4406 14.3805L56.4062 13.4148C56.5312 13.2836 56.5312 13.0773 56.4031 12.9492Z" fill="#0E7651"/>
                                    </svg>
                                </span>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="ta-td ta-empty">Belum ada data tahun akademik.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Alert Modal Overlay Berhasil --}}
    <div class="ta-overlay">
        <div class="ta-alert-modal berhasil">
            {{-- Close Button (Figma ci:close-lg) --}}
            <a href="{{ route('tahun-akademik.index') }}" class="ta-alert-close" title="Tutup">
                <img src="{{ asset('icon/figma_close_lg.svg') }}" alt="Tutup">
            </a>

            {{-- Logo --}}
            <img src="{{ asset('gambar/logo.png') }}" alt="Logo" class="ta-alert-logo">

            {{-- Title & Subtitle --}}
            <div>
                <h1 class="ta-alert-title">Berhasil</h1>
                <p class="ta-alert-subtitle">
                    {{ (!empty($isDelete) || request('action') === 'delete') ? 'Data Berhasil Di Hapus' : 'Data Tahun Akademik Berhasil Di Tambahkan Atau Di Perbarui' }}
                </p>
            </div>

            {{-- Button Terima --}}
            <div class="ta-alert-btn-wrap berhasil">
                <a href="{{ route('tahun-akademik.index') }}">Terima</a>
            </div>
        </div>
    </div>
@endsection
