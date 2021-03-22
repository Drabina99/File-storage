<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Your files') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    @if($files==null)
                    <p class="p-6">No files yet!</p>
                @else
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Size
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($files as $file)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $file->file_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $file->size }} bytes</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('disk.download', $file->id) }}" class="text-indigo-600 hover:text-indigo-900">Download</a>
                                    <br>
                                    <a href="{{ route('disk.link', $file->id) }}" class="text-indigo-600 hover:text-indigo-900">Share download link</a>
                                    <br>
                                    @if($shared_flag==false)
                                    <form method="post">
                                        <a href="{{ route('disk.delete', $file->id) }}" class="text-indigo-600 hover:text-indigo-900" >Delete</a>
                                        <br>
                                        @else
                                           <a href="{{ route('disk.soft_delete', $file->id) }}" class="text-indigo-600 hover:text-indigo-900" >Delete</a>
                                        </form>
                                     @endif
                                    <form method="post">
                                        <a href="{{ route('disk.share', $file->id) }}" class="text-indigo-600 hover:text-indigo-900" >Share</a>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif

                @if($shared_flag==false)
                <div class="flex items-center justify-end mt-4 px-4 pb-5">
                    <form method="get" action="/disk/upload">
                        <x-button class="ml-4">
                            {{ __('Upload new file!') }}
                        </x-button>
                    </form>
                </div>
                    @endif
            </div>
        </div>
    </div>
</x-app-layout>


