@extends('layouts.app', ['title' => 'Product'])

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card border-bottom-success">
                <div class="card-header">
                    <a href="javascript:void(0)" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalForm"
                        title="Tambah Product">
                        <i class="fas fa-plus"></i> Tambah Menu
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="productTable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>Foto</th>
                                    <th>Nama Menu</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Ketersediaan</th>
                                    <th>Deskripsi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
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
                    <h5 class="modal-title" id="modalFormLabel">Tambah Menu / Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" enctype="multipar/form-data" id="formProduct">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nama_menu">Nama Menu/Product</label>
                            <input type="hidden" name="id" id="id">
                            <input type="text" name="nama_menu" id="nama_menu" class="form-control">
                            <small class="text-danger error-text nama_menu_error"></small>
                        </div>
                        <div class="form-group">
                            <label for="kategori">Kategori</label>
                            <input type="text" name="kategori" id="kategori" class="form-control">
                            <small class="text-danger error-text kategori_error"></small>
                        </div>
                        <div class="form-group">
                            <label for="harga">Harga</label>
                            <input type="number" name="harga" id="harga" class="form-control">
                            <small class="text-danger error-text harga_error"></small>
                        </div>
                        <div class="form-group">
                            <label for="foto">Foto</label>
                            <input type="file" name="foto" id="foto" class="form-control">
                            <small class="text-danger error-text foto_error"></small>
                        </div>
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" cols="30" rows="5" class="form-control"></textarea>
                            <small class="text-danger error-text deskripsi_error"></small>
                        </div>
                        <div class="form">
                            <label for="status">Ketersediaan</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="Tersedia">Tersedia</option>
                                <option value="Tidak Tersedia">Tidak Tersedia</option>
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
            var table = $('#productTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('products.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'foto',
                        name: 'foto',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_menu',
                        name: 'nama_menu'
                    },
                    {
                        data: 'kategori',
                        name: 'kategori'
                    },
                    {
                        data: 'harga',
                        name: 'harga'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'deskripsi',
                        name: 'deskripsi'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // Reset form when modal is closed
            $('#modalForm').on('hidden.bs.modal', function() {
                $('#formProduct').trigger('reset');
                $('#id').val('');
                $('#modalFormLabel').text('Tambah Menu / Product');
                $(this).find('small.error-text').text(''); // Reset error messages
            });

            $('#formProduct').on('submit', function(e) {
                e.preventDefault();

                // Reset error messages
                $(this).find('small.error-text').text('');

                let formData = new FormData(this);
                let id = $('#id').val();
                let url = id ? "{{ route('products.update', ':product') }}".replace(':product', id) :
                    "{{ route('products.store') }}";

                if (id) {
                    formData.append('_method', 'PUT');
                }

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formData,
                    dataType: "json",
                    contentType: false,
                    processData: false,
                    cache: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            table.ajax.reload();
                            $('#modalForm').modal('hide');
                            $('#formProduct').trigger('reset');
                            Swal.fire(
                                'Berhasil!',
                                response.message,
                                'success'
                            );
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;

                            $.each(errors, function(key, value) {
                                $('small.' + key + '_error').text(value[0]);
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat menyimpan data',
                                'error'
                            );
                            console.error(xhr.responseText);
                        }
                    }
                });
            });

            $(document).on('click', '.btnedit', function() {
                let id = $(this).data('id');

                $.ajax({
                    type: "get",
                    url: "{{ route('products.edit', ':product') }}".replace(':product', id),
                    dataType: "json",
                    success: function(response) {
                        $('#modalForm').modal('show');
                        $('#id').val(response.id);
                        $('#nama_menu').val(response.nama_menu);
                        $('#kategori').val(response.kategori);
                        $('#harga').val(response.harga);
                        $('#deskripsi').val(response.deskripsi);
                        $('#status').val(response.status).trigger('change');
                        $('#modalFormLabel').text("Update Menu/Product");
                    },
                    error: function(xhr, status, error) {
                        Swal.fire(
                            'Error!',
                            'Terjadi kesalahan saat mengambil data',
                            'error'
                        );
                        console.error(xhr.responseText);
                    }
                });
            });

            // Add delete functionality
            $(document).on('click', '.btndestroy', function() {
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
                            url: "{{ route('products.destroy', ':product') }}".replace(
                                ':product', id),
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                table.ajax.reload();
                                Swal.fire(
                                    'Terhapus!',
                                    response.message,
                                    'success'
                                );
                            },
                            error: function(xhr, status, error) {
                                Swal.fire(
                                    'Error!',
                                    'Terjadi kesalahan saat menghapus data',
                                    'error'
                                );
                                console.error(xhr.responseText);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
