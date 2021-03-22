<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Download link') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    Here is your download link!<br/>
                    <label for="link">Copy and share:</label><br>
                    <input type="text" id="link" name="link" value={{$path}}>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
