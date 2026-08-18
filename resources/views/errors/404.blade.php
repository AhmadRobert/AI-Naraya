@extends('layouts.app')

@section('no_sidebar', true)

@section('content')
<div class="max-w-[1440px] mx-auto min-h-[70vh] flex flex-col items-center justify-center px-4 pb-12">
    <div class="relative rounded-3xl border border-outline-variant/70 bg-surface-container-lowest/95 shadow-[0_18px_60px_rgba(0,0,0,0.07)] overflow-hidden backdrop-blur p-12 text-center max-w-2xl w-full">
        
        <div class="absolute -top-20 -right-20 w-48 h-48 rounded-full bg-primary-container/30 blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 w-48 h-48 rounded-full bg-error/10 blur-3xl"></div>

        <div class="relative z-10 flex flex-col items-center">
            <div class="w-24 h-24 rounded-full bg-error-container text-on-error-container flex items-center justify-center mb-6 shadow-lg shadow-error/10">
                <span class="material-symbols-outlined text-5xl" style="font-variation-settings:'FILL' 1;">broken_image</span>
            </div>

            <h1 class="text-7xl font-bold text-on-surface mb-2 font-headline-lg">404</h1>
            <h2 class="text-2xl font-bold text-on-surface-variant mb-4">Waduh, Halaman Tidak Ditemukan!</h2>
            
            <p class="text-body-lg text-on-surface-variant/80 mb-8 max-w-md">
                Sepertinya kamu tersesat. Halaman yang kamu tuju mungkin sudah dihapus, dipindahkan, atau memang tidak pernah ada.
            </p>

            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-full font-label-lg hover:brightness-105 active:scale-95 transition-all shadow-md shadow-primary/20">
                <span class="material-symbols-outlined text-[20px]">home</span>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection