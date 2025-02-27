<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::orderBy('nama_menu', 'asc')->get();
            return DataTables::of($products)
                ->addIndexColumn('DT_RowIndex')
                ->addColumn('foto', function ($row) {
                    return '<img src="' . asset('images/' . $row->foto) . '" alt="Foto Product ' . $row->nama_menu . '" class="img-thumbnail" width="100">';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" class="btn btn-warning btn-sm btnedit" data-id="' . $row->id . '"><i class="fas fa-edit"></i> Edit</a> ';
                    $btn .= '<a href="javascript:void(0)" class="btn btn-danger btn-sm btndestroy" data-id="' . $row->id . '"><i class="fas fa-trash"></i> Hapus</a>';

                    return $btn;
                })
                ->rawColumns(['foto', 'action'])
                ->make(true);
        }

        return view('products.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_menu' => 'required',
            'harga' => 'required|numeric',
            'foto' => 'required|image|mimes:png,jpg,jpeg',
            'kategori' => 'required',
        ], [
            'nama_menu.required' => 'Nama menu harus diisi',
            'kategori.required' => 'Kategori harus diisi',
            'harga.required' => 'Harga harus diisi',
            'harga.numeric' => 'Harga harus berupa angka',
            'foto.required' => 'Foto harus diupload',
            'foto.image' => 'File harus berupa gambar',
            'foto.mimes' => 'Format foto harus PNG atau JPG',
            'foto.max' => 'Ukuran foto maksimal 2MB'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $fileName);

            $product = Product::create([
                'nama_menu' => $request->nama_menu,
                'kateogri' => $request->kategori,
                'harga' => $request->harga,
                'deskripsi' => $request->deskripsi,
                'foto' => $fileName
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product berhasil ditambahkan',
                'data' => $product
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $rules = [
            'nama_menu' => 'required',
            'kategori' => 'required',
            'harga' => 'required|numeric',
        ];

        // Validasi foto hanya jika ada file yang diupload
        if ($request->hasFile('foto')) {
            $rules['foto'] = 'image|mimes:png,jpg,jpeg|max:2048';
        }

        $validator = Validator::make($request->all(), $rules, [
            'nama_menu.required' => 'Nama menu harus diisi',
            'kategori.required' => 'Kategori harus diisi',
            'harga.required' => 'Harga harus diisi',
            'harga.numeric' => 'Harga harus berupa angka',
            'foto.image' => 'File harus berupa gambar',
            'foto.mimes' => 'Format foto harus PNG atau JPG',
            'foto.max' => 'Ukuran foto maksimal 2MB'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = [
                'nama_menu' => $request->nama_menu,
                'harga' => $request->harga,
                'deskripsi' => $request->deskripsi,
                'kategori' => $request->kategori
            ];

            // Proses upload foto baru jika ada
            if ($request->hasFile('foto')) {
                // Hapus foto lama
                if ($product->foto && file_exists(public_path('images/' . $product->foto))) {
                    unlink(public_path('images/' . $product->foto));
                }

                $file = $request->file('foto');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images'), $fileName);
                $data['foto'] = $fileName;
            }

            $product->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Product berhasil diupdate',
                'data' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            // Hapus foto dari storage jika ada
            if ($product->foto && file_exists(public_path('images/' . $product->foto))) {
                unlink(public_path('images/' . $product->foto));
            }

            // Hapus data dari database
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus product',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
