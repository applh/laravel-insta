<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("Welcome ") }} {{ $user->name }}
                </div>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form id="insta-update-access-token" method="post" action="{{ route('insta_api') }}">
                        @csrf
                        <div>
                            <x-input-label for="insta_access_token" :value="__('Insta Access Token')" />
                            <!-- warning: copy paste can input wrong text (before access token...) -->
                            <x-text-input id="insta_access_token" name="insta_access_token" type="text" class="mt-1 block w-full" :value="old('insta_access_token', $insta_access_token)" required pattern="[^' ']+" autofocus autocomplete="insta_access_token" />
                            <x-input-error class="mt-2" :messages="$errors->get('insta_access_token')" />
                        </div>
                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Connect with Insta API') }}</x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>