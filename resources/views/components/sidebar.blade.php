<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('home') }}" class="app-brand-link">
            <img src="{{ asset('ayam.png') }}" alt="{{ config('app.name') }}" width="35">
            <span class="app-brand-text demo text-black fw-bolder ms-2">{{ config('app.name') }}</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Home -->
        <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('home') ? 'active' : '' }}">
            <a href="{{ route('home') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="{{ __('menu.home') }}">{{ __('menu.home') }}</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">{{ __('menu.header.main_menu') }}</span>
        </li>

        <!-- grup inventory -->
        <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('inventory.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <img src="{{ asset('chart.png') }}" alt="Icon" class="menu-icon" style="width: 24px; height: 24px;">
                <div data-i18n="{{ __('Monitoring') }}">{{ __('Monitoring') }}</div>
            </a>
            <ul class="menu-sub">
                {{-- <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('inventory.stok.*') || \Illuminate\Support\Facades\Route::is('transaction.disposition.*') ? 'active' : '' }}">
                    <a href="{{ route('inventory.stok.index') }}" class="menu-link">
                        <div
                            data-i18n="{{ __('Stok Barang') }}">{{ __('Stok Barang') }}</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('inventory.goods.*') || \Illuminate\Support\Facades\Route::is('transaction.disposition.*') ? 'active' : '' }}">
                    <a href="{{ route('inventory.goods.index') }}" class="menu-link">
                        <div
                            data-i18n="{{ __('List Barang') }}">{{ __('List Barang') }}</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('inventory.kategori.*') || \Illuminate\Support\Facades\Route::is('transaction.disposition.*') ? 'active' : '' }}">
                    <a href="{{ route('inventory.kategori.index') }}" class="menu-link">
                        <div
                            data-i18n="{{ __('Kategori') }}">{{ __('Kategori') }}</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('inventory.category.*') || \Illuminate\Support\Facades\Route::is('transaction.disposition.*') ? 'active' : '' }}">
                    <a href="{{ route('inventory.category.index') }}" class="menu-link">
                        <div
                            data-i18n="{{ __('Kelompok Barang') }}">{{ __('Kelompok Barang') }}</div>
                    </a>
                </li> --}}
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('inventory.populasi.*') || \Illuminate\Support\Facades\Route::is('transaction.disposition.*') ? 'active' : '' }}">
                    <a href="{{ route('inventory.populasi.index') }}" class="menu-link">
                        <div
                            data-i18n="{{ __('Monitoring Populasi Ayam') }}">{{ __('Monitoring Populasi Ayam') }}</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('inventory.monitoring.*') || \Illuminate\Support\Facades\Route::is('transaction.disposition.*') ? 'active' : '' }}">
                    <a href="{{ route('inventory.monitoring.index') }}" class="menu-link">
                        <div
                            data-i18n="{{ __('Monitoring Pertumbuhan Ayam') }}">{{ __('Monitoring Pertumbuhan Ayam') }}</div>
                    </a>
                </li>
                
                
                
                
            </ul>
        </li>

    <!-- grup transaksi -->

        {{-- <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('transaksi.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-transfer-alt"></i>
                <div data-i18n="{{ __('Transaksi Barang') }}">{{ __('Transaksi Barang') }}</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('transaksi.barangmasuk.*') || \Illuminate\Support\Facades\Route::is('transaction.disposition.*') ? 'active' : '' }}">
                    <a href="{{ route('transaksi.barangmasuk.index') }}" class="menu-link">
                        <div
                            data-i18n="{{ __('Barang Masuk') }}">{{ __('Barang Masuk') }}</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('transaksi.detailbarangmasuk.*') || \Illuminate\Support\Facades\Route::is('transaction.disposition.*') ? 'active' : '' }}">
                    <a href="{{ route('transaksi.detailbarangmasuk.index') }}" class="menu-link">
                        <div
                            data-i18n="{{ __('Detail Barang Masuk') }}">{{ __('Detail Barang Masuk') }}</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('transaksi.barangkeluar.*') || \Illuminate\Support\Facades\Route::is('transaction.disposition.*') ? 'active' : '' }}">
                    <a href="{{ route('transaksi.barangkeluar.index') }}" class="menu-link">
                        <div
                            data-i18n="{{ __('Barang Keluar') }}">{{ __('Barang Keluar') }}</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('transaksi.detailbarangkeluar.*') || \Illuminate\Support\Facades\Route::is('transaction.disposition.*') ? 'active' : '' }}">
                    <a href="{{ route('transaksi.detailbarangkeluar.index') }}" class="menu-link">
                        <div
                            data-i18n="{{ __('Detail Barang Keluar') }}">{{ __('Detail Barang Keluar') }}</div>
                    </a>
                </li>
               
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('transaksi.permintaan.*') || \Illuminate\Support\Facades\Route::is('transaction.disposition.*') ? 'active' : '' }}">
                    <a href="{{ route('transaksi.permintaan.index') }}" class="menu-link">
                        <div
                            data-i18n="{{ __('Permintaan') }}">{{ __('Permintaan') }}</div>
                    </a>
                </li>
                {{-- <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('transaksi.barangmasuk.*') || \Illuminate\Support\Facades\Route::is('transaction.disposition.*') ? 'active' : '' }}">
                    <a href="{{ route('transaksi.barangmasuk.index') }}" class="menu-link">
                        <div
                            data-i18n="{{ __('Barang Masuk') }}">{{ __('Barang Masuk') }}</div>
                    </a>
                </li> 
               
            </ul>
        </li> --}}


        {{-- ini grup sistem --}}
        <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('sistem.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <img src="{{ asset('chikens.png') }}" alt="Icon" class="menu-icon" style="width: 24px; height: 24px;">
                <div data-i18n="{{ __('Ayam') }}">{{ __('Ayam ') }}</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('sistem.masuk.*') || \Illuminate\Support\Facades\Route::is('transaction.disposition.*') ? 'active' : '' }}">
                    <a href="{{ route('sistem.masuk.index') }}" class="menu-link">
                        <div
                            data-i18n="{{ __('Ayam Masuk') }}">{{ __('Ayam Masuk') }}</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('sistem.keluar.*') || \Illuminate\Support\Facades\Route::is('transaction.disposition.*') ? 'active' : '' }}">
                    <a href="{{ route('sistem.keluar.index') }}" class="menu-link">
                        <div
                            data-i18n="{{ __('Ayam Mati') }}">{{ __('Ayam Mati') }}</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('sistem.panen.*') || \Illuminate\Support\Facades\Route::is('transaction.disposition.*') ? 'active' : '' }}">
                    <a href="{{ route('sistem.panen.index') }}" class="menu-link">
                        <div
                            data-i18n="{{ __('Panen') }}">{{ __('Panen') }}</div>
                    </a>
                </li>
                {{-- <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('transaksi.detailbarangmasuk.*') || \Illuminate\Support\Facades\Route::is('transaction.disposition.*') ? 'active' : '' }}">
                    <a href="{{ route('transaksi.detailbarangmasuk.index') }}" class="menu-link">
                        <div
                            data-i18n="{{ __('Detail Barang Masuk') }}">{{ __('Detail Barang Masuk') }}</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('transaksi.barangkeluar.*') || \Illuminate\Support\Facades\Route::is('transaction.disposition.*') ? 'active' : '' }}">
                    <a href="{{ route('transaksi.barangkeluar.index') }}" class="menu-link">
                        <div
                            data-i18n="{{ __('Barang Keluar') }}">{{ __('Barang Keluar') }}</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('transaksi.detailbarangkeluar.*') || \Illuminate\Support\Facades\Route::is('transaction.disposition.*') ? 'active' : '' }}">
                    <a href="{{ route('transaksi.detailbarangkeluar.index') }}" class="menu-link">
                        <div
                            data-i18n="{{ __('Detail Barang Keluar') }}">{{ __('Detail Barang Keluar') }}</div>
                    </a>
                </li>
               
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('transaksi.permintaan.*') || \Illuminate\Support\Facades\Route::is('transaction.disposition.*') ? 'active' : '' }}">
                    <a href="{{ route('transaksi.permintaan.index') }}" class="menu-link">
                        <div
                            data-i18n="{{ __('Permintaan') }}">{{ __('Permintaan') }}</div>
                    </a>
                </li> --}}
                {{-- <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('transaksi.barangmasuk.*') || \Illuminate\Support\Facades\Route::is('transaction.disposition.*') ? 'active' : '' }}">
                    <a href="{{ route('transaksi.barangmasuk.index') }}" class="menu-link">
                        <div
                            data-i18n="{{ __('Barang Masuk') }}">{{ __('Barang Masuk') }}</div>
                    </a>
                </li> --}}
               
            </ul>
        </li>
        <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('pakan.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <img src="{{ asset('sack.png') }}" alt="Icon" class="menu-icon" style="width: 24px; height: 24px;">
                <div data-i18n="{{ __('Pakan') }}">{{ __('Pakan ') }}</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('pakan.stokpakan.*') || \Illuminate\Support\Facades\Route::is('transaction.disposition.*') ? 'active' : '' }}">
                    <a href="{{ route('pakan.stokpakan.index') }}" class="menu-link">
                        <div
                            data-i18n="{{ __('Stok Pakan') }}">{{ __('Stok Pakan') }}</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('pakan.monitoringpakan.*') || \Illuminate\Support\Facades\Route::is('transaction.disposition.*') ? 'active' : '' }}">
                    <a href="{{ route('pakan.monitoringpakan.index') }}" class="menu-link">
                        <div
                            data-i18n="{{ __('Monitoring Pakan') }}">{{ __('Monitoring Pakan') }}</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('pakan.pakanmasuk.*') || \Illuminate\Support\Facades\Route::is('transaction.disposition.*') ? 'active' : '' }}">
                    <a href="{{ route('pakan.pakanmasuk.index') }}" class="menu-link">
                        <div
                            data-i18n="{{ __('Pakan Masuk') }}">{{ __('Pakan Masuk') }}</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('pakan.pakankeluar.*') || \Illuminate\Support\Facades\Route::is('transaction.disposition.*') ? 'active' : '' }}">
                    <a href="{{ route('pakan.pakankeluar.index') }}" class="menu-link">
                        <div
                            data-i18n="{{ __('Pakan Keluar') }}">{{ __('Pakan Keluar') }}</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('pakan.transferpakan.*') || \Illuminate\Support\Facades\Route::is('transaction.disposition.*') ? 'active' : '' }}">
                    <a href="{{ route('pakan.transferpakan.index') }}" class="menu-link">
                        <div
                            data-i18n="{{ __('Pakan Transfer') }}">{{ __('Pakan Transfer') }}</div>
                    </a>
                </li>
                
               
            </ul>
        </li>
        <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('gaji.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <img src="{{ asset('money.png') }}" alt="Icon" class="menu-icon" style="width: 24px; height: 24px;">
                <div data-i18n="{{ __('Penggajian') }}">{{ __('Penggajian ') }}</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('gaji.operasional.*') || \Illuminate\Support\Facades\Route::is('transaction.disposition.*') ? 'active' : '' }}">
                    <a href="{{ route('gaji.operasional.index') }}" class="menu-link">
                        <div
                            data-i18n="{{ __('Operasional') }}">{{ __('Operasional') }}</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('gaji.pinjaman.*') || \Illuminate\Support\Facades\Route::is('transaction.disposition.*') ? 'active' : '' }}">
                    <a href="{{ route('gaji.pinjaman.index') }}" class="menu-link">
                        <div
                            data-i18n="{{ __('Pinjaman Petugas') }}">{{ __('Pinjaman Petugas') }}</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('gaji.penggajian.*') || \Illuminate\Support\Facades\Route::is('transaction.disposition.*') ? 'active' : '' }}">
                    <a href="{{ route('gaji.penggajian.index') }}" class="menu-link">
                        <div
                            data-i18n="{{ __('Penggajian') }}">{{ __('Penggajian') }}</div>
                    </a>
                </li>
                
                
               
            </ul>
        </li>
        <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('performa.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <img src="{{ asset('pie.png') }}" alt="Icon" class="menu-icon" style="width: 24px; height: 24px;">
                <div data-i18n="{{ __('Performa') }}">{{ __('Performa ') }}</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('performa.*') || \Illuminate\Support\Facades\Route::is('transaction.disposition.*') ? 'active' : '' }}">
                    <a href="{{ route('performa.ip.index') }}" class="menu-link">
                        <div
                            data-i18n="{{ __('Indeks Performa') }}">{{ __('Indeks Performa') }}</div>
                    </a>
                </li>
                
                
               
            </ul>
        </li>

    

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">{{ __('menu.header.other_menu') }}</span>
        </li>
        
        <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('lainnya.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-grid-alt"></i>
                <div data-i18n="{{ __('Data Lainnya') }}">{{ __('Data Lainnya') }}</div>
            </a>
            <ul class="menu-sub">
             
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('lainnya.kandang.*') ? 'active' : '' }}">
                    <a href="{{ route('lainnya.kandang.index') }}" class="menu-link">
                        <div data-i18n="{{ __('Kandang') }}">{{ __('Kandang') }}</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('lainnya.pakan.*') ? 'active' : '' }}">
                    <a href="{{ route('lainnya.pakan.index') }}" class="menu-link">
                        <div data-i18n="{{ __('List Pakan') }}">{{ __('List Pakan') }}</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('lainnya.abk.*') ? 'active' : '' }}">
                    <a href="{{ route('lainnya.abk.index') }}" class="menu-link">
                        <div data-i18n="{{ __('Petugas Kandang') }}">{{ __('Petugas Kandang') }}</div>
                    </a>
                </li>
                {{-- <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('lainnya.sekat.*') ? 'active' : '' }}">
                    <a href="{{ route('lainnya.sekat.index') }}" class="menu-link">
                        <div data-i18n="{{ __('Sekat') }}">{{ __('Sekat') }}</div>
                    </a>
                </li> --}}
               
            </ul>
        </li>

     
        @if(auth()->check() && auth()->user()->role == 'admin')
       
            <!-- User Management -->
            <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('user.*') ? 'active' : '' }}">
                <a href="{{ route('user.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-user-pin"></i>
                    <div data-i18n="{{ __('menu.users') }}">{{ __('menu.users') }}</div>
                </a>
            </li>
            {{-- <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('user.*') ? 'active' : '' }}">
                <a href="{{ route('user.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-file"></i>
                    <div data-i18n="{{ __('menu.users') }}">{{ __('Arsip') }}</div>
                </a>
            </li> --}}
        @endif
    </ul>
</aside>
