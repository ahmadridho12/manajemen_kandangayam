<?php

namespace App\Http\Controllers;
use App\Models\Kategoribm;

use Illuminate\Http\Request;

class KategoribmController extends Controller
{
    //
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        // Create the base query
        $query = Kategoribm::query();
    
        // If there's a search parameter, add a WHERE condition
        if ($search) {
            $query->where('nama_ibm', 'like', '%' . $search . '%');
        }
    
        // Use paginate to get the Paginator instance
        $data = $query->paginate(10); // 10 items per page
    
        return view('pages.lainnya.ppn', [
            'data' => $data,
            'search' => $search,
        ]);
    }
    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'nama_ibm' => 'required|string|max:255',
            'ppn' => 'required|numeric|between:0,99.99',
        ]);
    
        // Create a new category
        Kategoribm::create([
            'nama_ibm' => $request->nama_ibm,
            'ppn' => $request->ppn,
        ]);
    
        // Redirect or show a success message
        return redirect()->route('lainnya.ppn.index')->with('success', 'ppn berhasil ditambahkan!');
    }

    
    public function update(Request $request, $id_kategoribm)
    {
        // Validate input
        $request->validate([
            'nama_ibm' => 'required|string|max:255',
            'ppn' => 'required|numeric|between:0,99.99',
        ]);

        // Find the category
        $kategoribm = Kategoribm::findOrFail($id_kategoribm);
        $kategoribm->update([
            'nama_ibm' => $request->nama_ibm,
            'ppn' => $request->ppn,
        ]);

        // Redirect or show a success message
        return redirect()->route('lainnya.ppn.index')->with('success', 'PPN berhasil diperbarui!');
        }

    public function destroy($id_kategoribm)
    {
        // Find the category
        $kategoribm = Kategoribm::findOrFail($id_kategoribm);
        $kategoribm->delete();

        // Redirect or show a success message
        return redirect()->route('lainnya.ppn.index')->with('success', 'PPN berhasil dihapus!');
    }

    
}
