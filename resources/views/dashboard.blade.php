<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-2 gap-6 lg:gap-8">
            <div class="text-center">
                {{ __("Welcome ") }} {{ $user->name ?? "" }}
            </div>
            <div class="text-center">
                <a href="{{ url('/') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500 mr-10">(back to Home)</a>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="text-center">
            </div>
            <div class="p-6 text-gray-900">
            </div>
        </div>
    </div>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <section class="text-center">
                    <header>
                        <h2 class="m-1 p-6 text-lg font-medium text-gray-900">
                            {{ __('Instagram User Information') }}
                        </h2>
                        <h3 class="p-6"><a href="https://instagram.com/{{ $insta_username ?? '' }}/" target="_blank">https://instagram.com/{{ $insta_username ?? '...' }}/</a></h3>
                        <h3>{{ __('Insta user name:') }} {{ $insta_username ?? '...' }}</h3>
                        <p>{{ $nb_insta_media ?? '...' }} publications</p>
                        <span>{{ __('Insta user id') }}</span>
                        <x-text-input readonly id="insta_user_id" name="insta_user_id" type="password" pattern="[^' ']+" class="" :value="old('insta_user_id', $insta_user_id)" required autocomplete="" />
                    </header>

                    <div class="grid grid-cols-2 md:grid-cols-2 gap-6 lg:gap-8">
                        <div class="bg-gray-400">
                            <h2 class="m-1 p-6 text-lg font-medium text-gray-900 bg-gray-100">
                                {{ __('(action) Update Infos from Insta') }}
                            </h2>
                            <p class="mt-10 text-sm text-gray-600">
                                {{ __("Update infos from your instagram's profile information by access token.") }}
                            </p>
                            <p>
                                {{ __('* If needed, you can easily generate an access token here:') }}
                            </p>
                            <h3><a href="https://spotlightwp.com/access-token-generator/" target="_blank">https://spotlightwp.com/access-token-generator/</a></h3>
                            <form method="post" action="{{ route('insta_api') }}" class="mt-6 bg-gray-400 space-y-6">
                                @csrf
                                @method('post')
                                <input type="hidden" name="redirect_to" value="dashboard" />
                                <div>
                                    <x-input-label for="insta_access_token" :value="__('Access Token')" />
                                    <x-text-input id="insta_access_token" name="insta_access_token" type="text" pattern="[^' ']+" class="mt-1 block w-full" :value="old('insta_access_token', $insta_access_token)" required autocomplete="" />
                                    <x-input-error class="mt-2" :messages="$errors->get('insta_access_token')" />
                                </div>
                                <div>
                                    <label for="refresh_access_token">Refresh Access Token</label>
                                    <input class="w-6 h-6" type="checkbox" id="refresh_access_token" name="refresh_access_token" type="checkbox" class="mt-1 block w-full" value="1" autocomplete="" />
                                    <div>{{ __('last update: ') }} {{ $insta_user?->updated_at }}</div>
                                </div>
                                <div class="gap-4 text-center">
                                    <x-primary-button>{{ __('Update Infos From Insta') }}</x-primary-button>
                                </div>
                                <p>{{ __('note: manual update is preferred as Insta has 200 req/hour limit...') }}</p>
                                <p class="p-6"><a target="_blank" href="https://developers.facebook.com/docs/graph-api/overview/rate-limiting/">(https://developers.facebook.com/docs/graph-api/overview/rate-limiting/)</a></p>
                            </form>

                            <h2 class="m-1 p-6 text-lg font-medium text-gray-900 bg-gray-100">
                                {{ __('(cron) Update Infos from Insta by cron task') }}
                            </h2>
                            <p>{{ __('* To automatically update infos, cron can be set to ping this URL: ')}}<a target="_blank" href="{{ $insta_cron_url ?? '#' }}">{{ $insta_cron_url ?? "..." }}</a></p>
                            <p class="p-6">{{ __('tip: ') }}<a target="_blank" href="https://uptimerobot.com/">https://uptimerobot.com/</a>{{ __(' is a free uptime monitoring service.') }}</p>
                        </div>
                        <div class="bg-gray-400">
                            <hr>
                            <h2 class="m-1 p-6 text-lg font-medium text-gray-900 bg-white">
                                {{ __('(action) Refresh Access Token') }}
                            </h2>
                            <form method="post" action="{{ route('insta_refresh_access_token') }}" class="mt-6 space-y-6">
                                @csrf
                                @method('post')
                                <input type="hidden" name="redirect_to" value="dashboard" />
                                <div>
                                    <x-input-label for="insta_access_token" :value="__('Access Token')" />
                                    <x-text-input id="insta_access_token" name="insta_access_token" type="text" pattern="[^' ']+" class="mt-1 block w-full" :value="old('insta_access_token', $insta_access_token)" required autocomplete="" />
                                    <x-input-error class="mt-2" :messages="$errors->get('insta_access_token')" />
                                </div>

                                <div class="">
                                    <x-primary-button>{{ __('Refresh Access Token / every month') }}</x-primary-button>
                                    <div>{{ __('last update: ') . $insta_user?->updated_at }}</div>
                                </div>
                                <p>{{ __('note: long lived access tokens lasts 90 days and can be refreshed...') }}</p>
                                <p class="p-6"><a target="_blank" href="https://developers.facebook.com/docs/instagram-basic-display-api/guides/long-lived-access-tokens">{{ __('https://developers.facebook.com/docs/instagram-basic-display-api/guides/long-lived-access-tokens') }}</a></p>
                            </form>

                        </div>

                    </div>

                    @if ($nb_insta_media ?? 0)
                    <div class="mt-16">
                        <div>
                            {{ $insta_media->links() }}
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-2 gap-6 lg:gap-8">
                            @foreach ($insta_media as $im)
                            <div class="">
                                <!--<div>{{ $im->id }}</div>-->
                                <img src="{{ $im->insta_media_url }}" alt="" />
                                <h5>{{ $im->insta_media_caption }}</h5>
                                <p>published {{ $im->insta_media_timestamp}}</p>
                            </div>
                            @endforeach
                        </div>
                        <div>
                            {{ $insta_media->links() }}
                        </div>
                    </div>
                    @endif

                </section>

            </div>
        </div>
    </div>

</x-app-layout>