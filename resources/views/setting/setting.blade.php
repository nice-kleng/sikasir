@extends('layouts.app', ['title' => 'Pengaturan'])

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('setting.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h4>Pengaturan</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                {{-- <div class="form-group">
                                    <label>Logo</label>
                                    <input type="file" name="logo"
                                        class="form-control @error('logo') is-invalid @enderror">
                                    @error('logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Favicon</label>
                                    <input type="file" name="favicon"
                                        class="form-control @error('favicon') is-invalid @enderror">
                                    @error('favicon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div> --}}
                                <div class="form-group">
                                    <label>Nama Usaha</label>
                                    <input type="text" name="nama_usaha"
                                        class="form-control @error('nama_usaha') is-invalid @enderror"
                                        value="{{ old('nama_usaha', $setting->nama_usaha ?? '') }}">
                                    @error('nama_usaha')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Nama Pemilik</label>
                                    <input type="text" name="nama_pemilik"
                                        class="form-control @error('nama_pemilik') is-invalid @enderror"
                                        value="{{ old('nama_pemilik', $setting->nama_pemilik ?? '') }}">
                                    @error('nama_pemilik')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Nama Aplikasi</label>
                                    <input type="text" name="nama_aplikasi"
                                        class="form-control @error('nama_aplikasi') is-invalid @enderror"
                                        value="{{ old('nama_aplikasi', $setting->nama_aplikasi ?? '') }}">
                                    @error('nama_aplikasi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $setting->deskripsi ?? '') }}</textarea>
                                    @error('deskripsi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Alamat</label>
                                    <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', $setting->alamat ?? '') }}</textarea>
                                    @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Telepon</label>
                                    <input type="text" name="telepon"
                                        class="form-control @error('telepon') is-invalid @enderror"
                                        value="{{ old('telepon', $setting->telepon ?? '') }}">
                                    @error('telepon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $setting->email ?? '') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Midtrans Merchant ID</label>
                                    <input type="text" name="midtrans_merchant_id"
                                        class="form-control @error('midtrans_merchant_id') is-invalid @enderror"
                                        value="{{ old('midtrans_merchant_id', $setting->midtrans_merchant_id ?? '') }}">
                                    @error('midtrans_merchant_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Midtrans Client Key</label>
                                    <input type="text" name="midtrans_client_key"
                                        class="form-control @error('midtrans_client_key') is-invalid @enderror"
                                        value="{{ old('midtrans_client_key', $setting->midtrans_client_key ?? '') }}">
                                    @error('midtrans_client_key')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Midtrans Server Key</label>
                                    <input type="text" name="midtrans_server_key"
                                        class="form-control @error('midtrans_server_key') is-invalid @enderror"
                                        value="{{ old('midtrans_server_key', $setting->midtrans_server_key ?? '') }}">
                                    @error('midtrans_server_key')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Midtrans Environment</label>
                                    <select name="midtrans_environment"
                                        class="form-control @error('midtrans_environment') is-invalid @enderror">
                                        <option value="0"
                                            {{ old('midtrans_environment', $setting->midtrans_environment) == 0 ? 'selected' : '' }}>
                                            Sandbox</option>
                                        <option value="1"
                                            {{ old('midtrans_environment', $setting->midtrans_environment) == 1 ? 'selected' : '' }}>
                                            Production</option>
                                    </select>
                                    @error('midtrans_environment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Aktivasi Halaman Menu</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input
                                                class="form-check-input @error('permission_front_menu') is-invalid @enderror"
                                                type="radio" name="permission_front_menu" id="permission_front_menu_aktif"
                                                value="1"
                                                {{ old('permission_front_menu', $setting->permission_front_menu) == 1 ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permission_front_menu_aktif">Aktif</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input
                                                class="form-check-input @error('permission_front_menu') is-invalid @enderror"
                                                type="radio" name="permission_front_menu"
                                                id="permission_front_menu_nonaktif" value="0"
                                                {{ old('permission_front_menu', $setting->permission_front_menu) == 0 ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permission_front_menu_nonaktif">Non
                                                Aktif</label>
                                        </div>
                                        @error('permission_front_menu')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
