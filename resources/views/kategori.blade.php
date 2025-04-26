@extends('layouts.app', ['title' => 'Kategori Product'])

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card border-bottom-success">
                <div class="card-header">
                    <a href="javascript:void(0)" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalForm"
                        title="Tambah Kategori Product">
                        <i class="fas fa-plus"></i> Tambah Kategori
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="kategoriTable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kategori</th>
                                    <th>Deskripsi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($kategori as $value)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $value->nama_kategori }}</td>
                                        <td>{{ $value->deskripsi }}</td>
                                        <td>
                                            <a href="javascript:void(0)" class="btn btn-edit btn-info btn-sm"
                                                data-id="{{ $value->id }}" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="javascript:void(0)" class="btn btn-danger btn-sm btn-destroy"
                                                data-id="{{ $value->id }}" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $kategori->links() }}
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
                    <h5 class="modal-title" id="modalFormLabel">Tambah Kategori Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" id="formKategori">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nama_kategori">Nama Kategori</label>
                            <input type="hidden" name="id" id="id">
                            <input type="text" name="nama_kategori" id="nama_kategori" class="form-control">
                            <small class="text-danger error-text nama_kategori_error"></small>
                        </div>
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <input type="text" name="deskripsi" id="deskripsi" class="form-control">
                            <small class="text-danger error-text deskripsi_error"></small>
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
                $('#formKategori').trigger('reset');
                $('#id').val();
                $('#modalFormLabel').text('Tambah Kategori Product');
                $(this).find('small.error-text').text('');
            });

            $('#formKategori').on('submit', function(e) {
                e.preventDefault();

                $(this).find('small.error-text').text('');

                let formData = new FormData(this);
                let id = $('#id').val();
                let url = id ? "{{ route('kategori.update', ':kategori') }}".replace(':kategori', id) :
                    "{{ route('kategori.store') }}";

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
                            $('#formProduct').trigger('reset');
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
                            $.each(xhr.responseJSON.errors, function(key, val) {
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
                let url = "{{ route('kategori.edit', ':kategori') }}".replace(':kategori', id);

                $.ajax({
                    type: "get",
                    url: url,
                    dataType: "json",
                    success: function(response) {
                        $('#modalForm').modal('show');
                        $('#id').val(id);
                        $('#nama_kategori').val(response.nama_kategori);
                        $('#deskripsi').val(response.deskripsi);
                        $('#modalFormLabel').text('Update Kategori Product');
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
                            url: "{{ route('kategori.destroy', ':kategori') }}".replace(
                                ':kategori', id),
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
