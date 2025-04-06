@extends('layouts.app', ['title' => 'Management Pengguna'])

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card border-bottom-success">
                <div class="card-header">
                    <a href="javascript:void(0)" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalForm"
                        title="Tambah Pengguna">
                        <i class="fas fa-plus"></i> Tambah Pengguna
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="user-table">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>Nama</th>
                                    <th>Role</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ App\UserRole::from($user->role)->label() }}</td>
                                        <td>
                                            <a href="javascript:void(0)" class="btn btn-warning btn-sm" data-toggle="modal"
                                                data-target="#modalForm" data-id="{{ $user->id }}"
                                                data-name="{{ $user->name }}" data-role="{{ $user->role }}"
                                                title="Edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal"
                                                data-target="#deleteModal" data-id="{{ $user->id }}" title="Hapus">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalForm" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalFormLabel">Tambah Pengguna</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" id="formUser">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Nama Menu/Product</label>
                            <input type="hidden" name="id" id="id">
                            <input type="text" name="name" id="name" class="form-control">
                            <small class="text-danger error-text name_error"></small>$collection
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="hidden" name="id" id="id">
                            <input type="text" name="username" id="username" class="form-control">
                            <small class="text-danger error-text username_error"></small>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="text" name="password" id="password" class="form-control">
                            <small class="text-danger error-text password_error"></small>
                        </div>
                        <div class="form-group">
                            <label for="role">Role Pengguna</label>
                            <select name="role" id="role" class="form-control" required>
                                @foreach (App\UserRole::cases() as $role)
                                    <option value="{{ $role->value }}">{{ $role->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#modalForm').on('hidden.bs.modal', function() {
                $('#formUser').trigger('reset');
                $('#id').val();
                $('#modalFormLabel').text('Tambah Kategori Product');
                $(this).find('small.error-text').text('');
            });

            $('#formUser').on('submit', function(e) {
                e.preventDefault();

                $(this).find('small.error-text').text('');

                let formData = new FormData(this);
                let id = $('#id').val();
                let url = id ? "{{ route('users.update', ':user') }}".replace(':user', id) :
                    "{{ route('users.store') }}";

                if (id) {
                    formData.append('_method', 'PUT');
                }

                $.ajax({
                    type: "POST",
                    url: url,
                    data: formData,
                    contentType: false,
                    processData: false,
                    cache: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#modalForm').modal('hide');
                            $('#formUser').trigger('reset');
                            Swal.fire(
                                'Berhasil!',
                                response.message,
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status == 422) {
                            $.each(errors, function(key, val) {
                                $('small.' + key + '_error').text(val[0]);
                            });
                        } else {
                            Swal.fire(
                                'Gagal!',
                                'Terjadi kesalahan saat menyimpan data!',
                                'error'
                            );
                        }
                    }
                });
            });

            $(document).on('click', '.btn-edit', function() {
                let id = $(this).data('id');
                let url = "{{ route('users.edit', ':user') }}".replace(':user', id);

                $.ajax({
                    type: "get",
                    url: url,
                    dataType: "json",
                    success: function(response) {
                        $('#modalForm').modal('show');
                        $('#id').val(id);
                        $('#name').val(response.name);
                        $('#username').val(response.username);
                        $('#role').val(response.role);
                        $('#modalFormLabel').text('Update Pengguna');
                    },
                    error: function(xhr, status, error) {
                        Swal.fire(
                            'Error',
                            'Proses update gagal !',
                            'error',
                        );
                        console.log(xhr.responseText);
                    }
                });
            });

            $(document).on('click', '.btn-destroy', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ route('users.destroy', ':user') }}".replace(
                                ':user', id),
                            dataType: "json",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Berhasil !',
                                        response.message,
                                        'success'
                                    ).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire(
                                        'Gagal !',
                                        response.message,
                                        'error'
                                    ).then(() => {
                                        location.reload();
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire(
                                    'Error',
                                    'Terjadi permasalahan server',
                                    'error'
                                );
                                console.log(xhr.responseText);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
