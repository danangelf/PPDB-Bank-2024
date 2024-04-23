<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
  />     
<ul class="flex flex-col py-4 space-y-1">
    <li class="px-5">
        <div class="flex flex-row items-center h-8 border-b-2 border-white mt-6">
        <div class="font-bold tracking-wide text-gray-200">
            @if (session()->has('current_role_name'))
                {{ strtoupper(session('current_role_name')) }}
            @else
                {{ __('YOUR ROLE NAME') }}
            @endif
        </div>
        </div>
    </li>
    <li class="">
        <a href="{{ env('APP_URL') }}/home" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-white hover:text-gray-800 border-l-4 border-transparent hover:border-indigo-500 pr-6">
        <span class="inline-flex justify-center items-center ml-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </span>
        <span class="ml-2 text-sm tracking-wide truncate">Dashboard</span>
        </a>
    </li>
    @if (!empty($menu))
    @foreach ($menu as $mg)
        <li x-data="{isOpenMenu: false}" class="px-5 transform duration-500">
            <div class="flex flex-row items-center justify-between border-b-2 border-white mt-6 cursor-pointer text-gray-200 hover:bg-gray-200 hover:text-gray-800 px-3 py-2" 
                @click="isOpenMenu = !isOpenMenu">
                <div class="font-bold tracking-wide ">
                    {{ $mg['menugroup_label'] }}
                </div>
                <div class="font-bold text-xs">
                    <i class="fa fa-chevron-down origin-center duration-500 " :class="isOpenMenu ? `rotate-0` : `-rotate-180`" ></i>
                </div>
            </div>
            <ul x-show="isOpenMenu" @click.away="isOpenMenu = false"  
                x-transition:enter="ease-in duration-500"
                x-transition:enter-start="opacity-0 transform origin-top scale-y-0"
                x-transition:enter-end="opacity-100 transform origin-top scale-y-100"
                x-transition:leave="ease-out duration-500"
                x-transition:leave-start="opacity-100 transform origin-top scale-y-100"
                x-transition:leave-end="opacity-0 transform origin-top scale-y-0">
                @foreach ($mg['menus'] as $m)
                    <li>
                        <a href="{{ env('APP_URL') . $m['route'] }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-white hover:text-gray-800 border-l-4 border-transparent hover:border-indigo-500 pr-6">
                        <span class="inline-flex justify-center items-center ml-4">
                            <?=$m['menu_icon']; ?>
                        </span>
                        <span class="ml-2 text-sm tracking-wide truncate">{{ $m['menu_label'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>
    @endforeach
    @endif
    
    </ul>