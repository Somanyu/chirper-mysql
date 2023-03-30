<?php

namespace App\Http\Controllers;

use App\Models\Chirp;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as InterImage;
use Illuminate\Support\Facades\DB;

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
        $thumbnails = array();

        if ($files = $request->file('images')) {
            foreach ($files as $file) {
                $ext = strtolower($file->getClientOriginalExtension());
                $image_name = md5(rand(1000, 10000));
                $image_fullName = $image_name . '.' . $ext;
                $image_upload_path = 'storage/images/';
                $image_url = $image_upload_path . $image_fullName;

                if ($ext == 'mp3') {
                    // Handle mp3 files
                    $filename = md5(rand(1000, 10000)) . '.' . $ext;
                    $upload_path = 'storage/mp3/';
                    $mp3_url = $upload_path . $filename;
                    $file->move($upload_path, $filename);
                    $images[] = $mp3_url;
                    $thumbnails[] = null;
                } else if ($ext == 'mp4') {
                    // For mp4 files, simply upload the file without creating a thumbnail
                    $file->move($image_upload_path, $image_fullName);
                    $images[] = $image_url;
                    $thumbnails[] = null;
                } else {
                    // For image files, create a thumbnail
                    $thumbnail_name = $image_name . '_thumb.' . $ext;
                    $thumbnail_upload_path = 'storage/images/thumbnails/';
                    $thumbnail_url = $thumbnail_upload_path . $thumbnail_name;
                    InterImage::make($file)->fit(200, 200)->save($thumbnail_url);

                    $file->move($image_upload_path, $image_fullName);
                    $thumbnails[] = $thumbnail_url;
                    $images[] = $image_url;
                }
            }
        }

        $combined = array_combine($images, $thumbnails);

        $chirps = $request->user()->chirps()->create([
            'message' => $request->message,
        ]);

        foreach ($combined as $filename => $thumbnail) {
            $imageModel = new Image([
                'filename' => $filename,
                'thumbnail' => $thumbnail,
            ]);
            $chirps->images()->save($imageModel);
        }


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

        $images = array();
        $thumbnails = array();

        if ($files = $request->file('images')) {
            foreach ($files as $file) {
                $ext = strtolower($file->getClientOriginalExtension());
                $image_name = md5(rand(1000, 10000));
                $image_fullName = $image_name . '.' . $ext;
                $image_upload_path = 'storage/images/';
                $image_url = $image_upload_path . $image_fullName;

                if ($ext == 'mp3') {
                    // Handle mp3 files
                    $filename = md5(rand(1000, 10000)) . '.' . $ext;
                    $upload_path = 'storage/mp3/';
                    $mp3_url = $upload_path . $filename;
                    $file->move($upload_path, $filename);
                    $images[] = $mp3_url;
                    $thumbnails[] = null;
                } else if ($ext == 'mp4') {
                    // For mp4 files, simply upload the file without creating a thumbnail
                    $file->move($image_upload_path, $image_fullName);
                    $images[] = $image_url;
                    $thumbnails[] = null;
                } else {
                    // For image files, create a thumbnail
                    $thumbnail_name = $image_name . '_thumb.' . $ext;
                    $thumbnail_upload_path = 'storage/images/thumbnails/';
                    $thumbnail_url = $thumbnail_upload_path . $thumbnail_name;
                    InterImage::make($file)->fit(200, 200)->save($thumbnail_url);

                    $file->move($image_upload_path, $image_fullName);
                    $thumbnails[] = $thumbnail_url;
                    $images[] = $image_url;
                }
            }
        }

        $combined = array_combine($images, $thumbnails);

        $chirp->update([
            'message' => $request->message,
        ]);

        foreach ($combined as $filename => $thumbnail) {
            $imageModel = new Image([
                'filename' => $filename,
                'thumbnail' => $thumbnail,
            ]);
            $chirp->images()->save($imageModel);
        }

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
                Storage::delete(str_replace('storage', 'public', $image->thumbnail));
            }
            $image->delete();
        }

        $chirp->delete();

        return redirect(route('chirps.index'));
    }
}
