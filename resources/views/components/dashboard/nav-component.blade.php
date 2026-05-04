<div class="bg-white border border-gray-300 rounded-md p-4 space-y-3">
    <div class="">
        <a href="{{ route('dashboard') }}" 
        wire:navigate 
        class="py-2 px-4 border border-gray-100 rounded-md block w-full text-sm @if(request()->routeIs('dashboard')) bg-gray-100 @endif"> Dashboard </a>
    </div>
    <div class="">
        <a href="{{ route('dashboard.payment-list') }}" 
        wire:navigate 
        class="py-2 px-4 border border-gray-100 rounded-md block w-full text-sm @if(request()->routeIs('dashboard.payment-list')) bg-gray-100 @endif"> 
            Pembayaran 
        </a>
    </div>
    <div class="">
        <a href="{{ route('dashboard.account-settings') }}" 
        wire:navigate
        class="py-2 px-4 border border-gray-100 rounded-md block w-full text-sm @if(request()->routeIs('dashboard.account-settings')) bg-gray-100 @endif"> Pengaturan Akun </a>
    </div>
</div>