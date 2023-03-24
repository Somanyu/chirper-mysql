<x-app-layout>
    <div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 my-5">
            @foreach($chirp->images as $image)
                <form action="{{ route('images.destroy', $image->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    {{-- <img class="relative inline-flex items-center max-w-full h-auto rounded-lg" src="{{ asset($image->filename) }}" alt="Chirp Image"> --}}
                    <div class="relative inline-flex items-center text-sm font-medium text-center text-white rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        <input type="hidden" name="image_id" value="{{ $image->id }}">
                        <img class="relative inline-flex items-center max-w-full h-auto rounded-lg" src="{{ asset($image->filename) }}" alt="Chirp Image">
                        <button type="submit" class="absolute inline-flex items-center justify-center drop-shadow-lg hover:drop-shadow-2xl w-8 h-8 text-xs font-bold text-white bg-red-500 border-white rounded-full -top-2 -right-2 dark:border-gray-900"><svg aria-hidden="true" class="w-3.5 h-3.5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg></button>
                    </div>
                </form>
            @endforeach
        </div>
        
        <form method="POST" action="{{ route('chirps.update', $chirp) }}" enctype="multipart/form-data">
            @csrf
            @method('patch')
            <textarea
                name="message"
                class="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
            >{{ old('message', $chirp->message) }}</textarea>
            <x-input-error :messages="$errors->get('message')" class="mt-2" />
            <!-- Image Upload -->
            <div class="mt-4">
                <x-input-label for="image" :value="__('Upload Post')" />
                <input id="image" class="block w-full text-sm text-slate-500
                file:mr-4 file:py-2 file:px-4
                file:rounded-full file:border-0
                file:text-sm file:font-semibold
                file:bg-blue-50 file:text-slate-700
                hover:file:bg-slate-300"  type="file" name="images[]" multiple/>
                <x-input-error :messages="$errors->get('image')" class="mt-2" />
            </div>
            <div class="mt-4 space-x-2">
                <x-primary-button>{{ __('Save') }}</x-primary-button>
                <a href="{{ route('chirps.index') }}">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</x-app-layout>