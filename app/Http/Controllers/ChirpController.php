<?php

namespace App\Http\Controllers;

use App\Models\Chirp;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChirpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('chirps.index', [
            'chirps' => Chirp::with('user')->latest()->get(),
        ]);
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
    public function store(Request $request): RedirectResponse
    {
        $images = array();
        if ($files = $request->file('images')) {
            foreach ($files as $file) {
                $image_name = md5(rand(1000, 10000));
                $ext = strtolower($file->getClientOriginalExtension());
                $image_fullName = $image_name . '.' . $ext;
                $upload_path = 'storage/images/';
                $image_url = $upload_path . $image_fullName;
                $file->move($upload_path, $image_fullName);
                $images[] = $image_url;
            }
        }


        $chirps = $request->user()->chirps()->create([
            'message' => $request->message,
        ]);

        foreach ($images as $image) {
            $chirps->images()->create([
                'filename' => $image,
            ]);
        };


        return redirect(route('chirps.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Chirp $chirp)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Chirp $chirp): View
    {
        $this->authorize('update', $chirp);

        return view('chirps.edit', [
            'chirp' => $chirp,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chirp $chirp): RedirectResponse
    {
        $this->authorize('update', $chirp);

        // Save the new images to storage
        $images = array();
        if ($files = $request->file('images')) {
            foreach ($files as $file) {
                $image_name = md5(rand(1000, 10000));
                $ext = strtolower($file->getClientOriginalExtension());
                $image_fullName = $image_name . '.' . $ext;
                $upload_path = 'storage/images/';
                $image_url = $upload_path . $image_fullName;
                $file->move($upload_path, $image_fullName);
                $images[] = $image_url;
            }
        }
        // dd($image);

        $chirp->update([
            'message' => $request->message,
        ]);

        foreach ($images as $image) {
            $chirp->images()->create([
                'filename' => $image,
            ]);
        };

        return redirect(route('chirps.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chirp $chirp, Image $image): RedirectResponse
    {
        $this->authorize('delete', $chirp);

        foreach ($chirp->images as $image) {
            if (Storage::exists(str_replace('storage', 'public', $image->filename))) {
                Storage::delete(str_replace('storage', 'public', $image->filename));
            }
            $image->delete();
        }

        $chirp->delete();

        return redirect(route('chirps.index'));
    }
}
