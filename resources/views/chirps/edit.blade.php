<x-app-layout>
    <div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
        <form method="POST" action="{{ route('chirps.update', $chirp) }}">
            @csrf
            @method('patch')
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 my-5">
                @foreach(explode('|', $chirp->images) as $image)
                    <img class="max-w-full h-auto rounded-lg" src="{{ asset($image) }}" alt="Chirp Image">
                @endforeach
            </div>
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